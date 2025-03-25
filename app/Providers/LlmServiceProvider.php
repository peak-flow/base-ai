<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Llm\LlmClient;
use App\Services\Llm\Models\LlmTransformerInterface;
use App\Services\Llm\Models\LocalLlmTransformer;

class LlmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LlmClient::class, function ($app) {
            return new LlmClient(config('services.llm.base_url'));
        });

        $this->app->bind(LlmTransformerInterface::class, LocalLlmTransformer::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
