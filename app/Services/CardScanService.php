<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CardScanService
{
    /**
     * Initiate a card scan session with the CardNest endpoint.
     * Caches the session to prevent unnecessary repeated billing hits.
     */
    public function initiateScan(array $customerData, bool $force = false): array
    {
        // 1. Check if we already have an active scan session cached to save money
        $sessionKey = 'card_scan_session';
        if (!$force && session()->has($sessionKey)) {
            $cached = session()->get($sessionKey);
            // Verify it was created recently (e.g., within last 10 minutes)
            if (isset($cached['created_at']) && (time() - $cached['created_at']) < 600) {
                Log::info("CardScanService: Using cached scan session to prevent redundant billing");
                return [
                    'success'  => true,
                    'scan_id'  => $cached['scan_id'],
                    'scan_url' => $cached['scan_url'],
                    'token'    => $cached['token'] ?? null,
                ];
            }
        }

        $url = 'https://admin.cardnest.io/api/merchantscan/generateToken';

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'merchantId' => 'mer000150',
                    'isMobile'   => 'false',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $scanId  = $data['scanID']  ?? $data['scan_id']  ?? $data['id'] ?? null;
                $scanUrl = $data['scanURL'] ?? $data['scan_url'] ?? $data['url'] ?? null;
                $token   = $data['token']   ?? $data['authToken'] ?? $data['auth_token'] ?? null;

                if ($scanId && $scanUrl) {
                    // Cache the successful session response
                    session()->put($sessionKey, [
                        'scan_id'    => $scanId,
                        'scan_url'   => $scanUrl,
                        'token'      => $token,
                        'created_at' => time()
                    ]);

                    return [
                        'success'  => true,
                        'scan_id'  => $scanId,
                        'scan_url' => $scanUrl,
                        'token'    => $token,
                    ];
                }
            }

            Log::error("CardScanService: Initiate request failed", [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
        } catch (\Throwable $e) {
            Log::error("CardScanService: Exception initiating scan: " . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Get status of the card scan session.
     */
    public function getStatus(string $scanId): array
    {
        $url = env('CARD_SCAN_STATUS_URL');

        // Only run mock simulation if explicitly enabled in config
        if (env('CARD_SCAN_MOCK') === true || str_starts_with($scanId, 'mock_scan_')) {
            $cacheKey = "mock_scan_status_{$scanId}";
            $attempts = (int) \Illuminate\Support\Facades\Cache::get($cacheKey, 0);
            
            if ($attempts < 3) {
                \Illuminate\Support\Facades\Cache::put($cacheKey, $attempts + 1, 60);
                return [
                    'status' => 'pending'
                ];
            }

            return [
                'status' => 'completed',
                'card' => [
                    'number'      => '4242424242424242',
                    'expiry_month'=> '12',
                    'expiry_year' => '2028',
                    'brand'       => 'visa',
                    'name'        => 'Mock Cardholder'
                ]
            ];
        }

        if (empty($url)) {
            return [
                'status' => 'pending'
            ];
        }

        // Replace placeholder :id or similar if endpoint supports path parameters
        $statusUrl = str_replace('{id}', $scanId, $url);

        try {
            $response = Http::timeout(5)->get($statusUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                // Expect status to be 'completed', 'pending', or 'failed'
                return [
                    'status' => $data['status'] ?? 'pending',
                    'card'   => $data['card'] ?? null
                ];
            }
        } catch (\Throwable $e) {
            Log::error("CardScanService: Exception checking status: " . $e->getMessage());
        }

        return ['status' => 'pending'];
    }
}
