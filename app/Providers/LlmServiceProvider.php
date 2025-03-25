<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Llm\LlmClient;
use App\Services\Llm\Models\LlmTransformerInterface;
use App\Services\Llm\Models\LocalLlmTransformer;
use App\Services\Llm\Models\OpenAiTransformer;
use App\Services\Llm\Models\EmbeddingTransformerInterface;
use App\Services\Llm\Models\OpenAiEmbeddingTransformer;
use App\Services\Llm\EmbeddingService;

class LlmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the appropriate LLM client based on the provider setting
        $this->app->singleton(LlmClient::class, function ($app) {
            $provider = config('jana.llm.provider', 'local');
            
            if ($provider === 'openai') {
                return new LlmClient(config('jana.llm.openai.base_url'));
            }
            
            return new LlmClient(config('jana.llm.local.base_url'));
        });

        // Bind the appropriate transformer based on the provider setting
        $this->app->bind(LlmTransformerInterface::class, function ($app) {
            $provider = config('jana.llm.provider', 'local');
            
            if ($provider === 'openai') {
                return $app->make(OpenAiTransformer::class);
            }
            
            return $app->make(LocalLlmTransformer::class);
        });
        
        // Bind the embedding transformer
        $this->app->bind(EmbeddingTransformerInterface::class, function ($app) {
            $embeddingProvider = config('jana.embedding.provider', 'openai');
            
            // Currently only OpenAI embedding is supported
            // In the future, we can add more providers here
            return $app->make(OpenAiEmbeddingTransformer::class);
        });
        
        // Register the embedding service
        $this->app->singleton(EmbeddingService::class, function ($app) {
            return new EmbeddingService(
                $app->make(EmbeddingTransformerInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
