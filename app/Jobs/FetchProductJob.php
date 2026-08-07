<?php

namespace App\Jobs;

use App\Services\ProductFetchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class FetchProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries  = 2;
    public int $timeout = 30;

    public function __construct(
        public readonly string $url,
        public readonly string $jobKey,
    ) {}

    public function handle(ProductFetchService $service): void
    {
        $result = $service->fetch($this->url);

        // Store the result so the frontend can poll for it
        Cache::put(
            "fetch_job_{$this->jobKey}",
            [
                'status' => $result ? 'done' : 'failed',
                'data'   => $result,
            ],
            now()->addMinutes(10)
        );
    }

    public function failed(\Throwable $e): void
    {
        Cache::put(
            "fetch_job_{$this->jobKey}",
            ['status' => 'failed', 'data' => null],
            now()->addMinutes(10)
        );
    }
}
