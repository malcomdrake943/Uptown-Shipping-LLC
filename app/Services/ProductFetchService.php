<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductFetchService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'timeout'         => 30, // Increased timeout for proxy services
            'connect_timeout' => 15,
            'headers'         => [
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Connection'      => 'keep-alive',
            ],
            'allow_redirects' => ['max' => 5],
            'http_errors'     => false,
        ]);
    }

    /**
     * Fetch product metadata from a URL.
     * Returns null on any failure so callers can gracefully fall back to manual entry.
     *
     * @return array{name: ?string, image_url: ?string, price: ?float, description: ?string, platform: string}|null
     */
    public function fetch(string $url): ?array
    {
        $cacheKey = 'product_fetch_' . md5($url);
        $cacheTtl = (int) config('app.product_fetch_cache_ttl', 3600);

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $platform = $this->detectPlatform($url);

        // ── 1. If Amazon & Rainforest API Key exists, try it ────────────────────
        if ($platform === 'amazon' && $apiKey = env('RAINFOREST_API_KEY')) {
            $asin = $this->extractAsin($url);
            $domain = $this->extractAmazonDomain($url);
            if ($asin) {
                try {
                    $apiUrl = "https://api.rainforestapi.com/request?api_key={$apiKey}&type=product&amazon_domain={$domain}&asin={$asin}";
                    $response = $this->http->get($apiUrl);
                    
                    if ($response->getStatusCode() === 200) {
                        $data = json_decode($response->getBody(), true);
                        if (isset($data['product'])) {
                            $product = $data['product'];
                            
                            $price = null;
                            if (isset($product['buybox_winner']['price']['value'])) {
                                $price = (float) $product['buybox_winner']['price']['value'];
                            } elseif (isset($product['price']['value'])) {
                                $price = (float) $product['price']['value'];
                            }

                            $result = [
                                'name'        => $product['title'] ?? null,
                                'image_url'   => $product['main_image']['link'] ?? ($product['images'][0]['link'] ?? null),
                                'price'       => $price,
                                'description' => Str::limit(strip_tags($product['description'] ?? $product['feature_bullets_flat'] ?? ''), 300),
                                'platform'    => 'amazon',
                            ];

                            if ($result['name']) {
                                Cache::put($cacheKey, $result, $cacheTtl);
                                return $result;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Rainforest API failed: " . $e->getMessage());
                }
            }
        }

        // ── 2. Route through ScraperAPI proxy if key exists ─────────────────────
        if ($scraperKey = env('SCRAPER_API_KEY')) {
            try {
                $proxyUrl = "http://api.scraperapi.com/?api_key={$scraperKey}&url=" . urlencode($url);
                $response = $this->http->get($proxyUrl);

                if ($response->getStatusCode() === 200) {
                    $html = (string) $response->getBody();
                    $result = $this->parse($html, $platform, $url);
                    if ($result) {
                        Cache::put($cacheKey, $result, $cacheTtl);
                        return $result;
                    }
                }
            } catch (\Throwable $e) {
                Log::error("ScraperAPI failed: " . $e->getMessage());
            }
        }

        // ── 3. Direct Scraping Fallback ─────────────────────────────────────────
        try {
            $response = $this->http->get($url);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $html = (string) $response->getBody();
            $result = $this->parse($html, $platform, $url);

            if ($result) {
                Cache::put($cacheKey, $result, $cacheTtl);
            }

            return $result;

        } catch (RequestException $e) {
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Detect platform from URL.
     */
    public function detectPlatform(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        if (str_contains($host, 'amazon')) return 'amazon';
        if (str_contains($host, 'ebay'))   return 'ebay';
        return 'other';
    }

    /**
     * Helper to extract Amazon ASIN.
     */
    private function extractAsin(string $url): ?string
    {
        if (preg_match('/\/([A-Z0-9]{10})(?:[?\/]|$)/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extract Amazon domain (e.g. amazon.ca, amazon.co.uk).
     */
    private function extractAmazonDomain(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        $domain = str_replace('www.', '', $host);
        return $domain ?: 'amazon.com';
    }

    /**
     * Parse HTML for product metadata: JSON-LD first, then OG tags, then <title>.
     */
    private function parse(string $html, string $platform, string $url): ?array
    {
        $name        = null;
        $imageUrl    = null;
        $price       = null;
        $description = null;

        // ── 1. Try JSON-LD (Product schema) ─────────────────────────────────────
        if (preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $json) {
                try {
                    $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    if (isset($data['@graph'])) {
                        foreach ($data['@graph'] as $item) {
                            if (($item['@type'] ?? '') === 'Product') {
                                $data = $item;
                                break;
                            }
                        }
                    }
                    if (($data['@type'] ?? '') === 'Product') {
                        $name        = $data['name'] ?? null;
                        $imageUrl    = is_array($data['image'] ?? null)
                            ? ($data['image'][0] ?? null)
                            : ($data['image'] ?? null);
                        $description = Str::limit(strip_tags($data['description'] ?? ''), 300);

                        if (isset($data['offers'])) {
                            $offers = $data['offers'];
                            if (isset($offers['price'])) {
                                $price = (float) $offers['price'];
                            } elseif (is_array($offers) && isset($offers[0]['price'])) {
                                $price = (float) $offers[0]['price'];
                            }
                        }
                        break;
                    }
                } catch (\JsonException) {
                    continue;
                }
            }
        }

        // ── 2. OG tags fallback ──────────────────────────────────────────────────
        if (! $name && preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\'](.*?)["\']/i', $html, $m)) {
            $name = html_entity_decode(trim($m[1]));
        }
        if (! $imageUrl && preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\'](.*?)["\']/i', $html, $m)) {
            $imageUrl = trim($m[1]);
        }
        if (! $description && preg_match('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\'](.*?)["\']/i', $html, $m)) {
            $description = Str::limit(html_entity_decode(trim($m[1])), 300);
        }

        // ── 3. Amazon-specific HTML Fallbacks (Very Robust) ──────────────────────
        if ($platform === 'amazon') {
            // Price Extraction Fallback
            if (! $price) {
                if (preg_match('/<span[^>]*class="a-offscreen"[^>]*>\$([0-9.,]+)<\/span>/i', $html, $m)) {
                    $price = (float) str_replace(',', '', $m[1]);
                } elseif (preg_match('/<span[^>]*class="a-price-whole"[^>]*>([0-9,]+)<span[^>]*class="a-price-decimal"[^>]*>\.<\/span><\/span><span[^>]*class="a-price-fraction"[^>]*>([0-9]+)<\/span>/i', $html, $m)) {
                    $price = (float) (str_replace(',', '', $m[1]) . '.' . $m[2]);
                } elseif (preg_match('/id=["\']priceblock_[^"\']+["\'][^>]*>\$([0-9.,]+)/i', $html, $m)) {
                    $price = (float) str_replace(',', '', $m[1]);
                }
            }

            // Image Extraction Fallback
            if (! $imageUrl) {
                if (preg_match('/data-old-hires=["\'](https:\/\/[^"\']+\.jpg)["\']/i', $html, $m)) {
                    $imageUrl = $m[1];
                } elseif (preg_match('/data-a-dynamic-image=["\']\{&quot;(https:\/\/[^&]+)&quot;/i', $html, $m)) {
                    $imageUrl = $m[1];
                } elseif (preg_match('/id=["\']landingImage["\'][^>]*src=["\']([^"\']+)["\']/i', $html, $m)) {
                    $imageUrl = $m[1];
                }
            }
        }

        // ── 4. eBay-specific HTML Fallbacks ──────────────────────────────────────
        if ($platform === 'ebay') {
            // Price: <div class=x-price-primary ...><span class=ux-textspans>US $127.50</span>
            if (! $price) {
                if (preg_match('/class=x-price-primary[^>]*>.*?<span[^>]*class=ux-textspans[^>]*>[A-Z\s]*\$([0-9.,]+)/is', $html, $m)) {
                    $price = (float) str_replace(',', '', $m[1]);
                } elseif (preg_match('/itemprop=["\']price["\'][^>]*content=["\']([\d.]+)["\']/i', $html, $m)) {
                    $price = (float) $m[1];
                } elseif (preg_match('/data-testid=["\']x-price-primary["\'][^>]*>.*?\$([0-9.,]+)/is', $html, $m)) {
                    $price = (float) str_replace(',', '', $m[1]);
                }
            }

            // Image: eBay OG image is usually present and already fetched above.
            // Fallback: first high-res image from carousel
            if (! $imageUrl) {
                if (preg_match('/data-zoom-src=(https:\/\/i\.ebayimg\.com\/[^\s"\']+\.(?:jpg|webp))/i', $html, $m)) {
                    $imageUrl = $m[1];
                } elseif (preg_match('/src=(https:\/\/i\.ebayimg\.com\/images\/g\/[^\/]+\/s-l[0-9]+\.(?:jpg|webp))/i', $html, $m)) {
                    $imageUrl = $m[1];
                }
            }

            // Clean title suffix (e.g. " | eBay")
            if ($name) {
                $name = preg_replace('/\s*\|\s*eBay\s*$/i', '', $name);
            }
        }

        // ── 4. <title> last resort ───────────────────────────────────────────────
        if (! $name && preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
            $name = html_entity_decode(trim(strip_tags($m[1])));
        }

        // ── 5. meta description fallback ─────────────────────────────────────────
        if (! $description && preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']/i', $html, $m)) {
            $description = Str::limit(html_entity_decode(trim($m[1])), 300);
        }

        // Amazon title cleaning fallback
        if ($platform === 'amazon' && $name) {
            if (preg_match('/^Amazon\.[a-z\.]+\s*:\s*(.*)$/i', $name, $m)) {
                $name = trim($m[1]);
            }
        }

        if (! $name) {
            return null;
        }

        return [
            'name'        => $name,
            'image_url'   => $imageUrl,
            'price'       => $price,
            'description' => $description,
            'platform'    => $platform,
        ];
    }
}

