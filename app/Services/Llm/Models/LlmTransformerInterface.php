<?php

namespace App\Services\Llm\Models;

/**
 * Interface that all LLM transformers must implement
 */
interface LlmTransformerInterface
{
    /**
     * Send a message to the model and get a response
     */
    public function sendMessage(string $message, array $context = []): string;
    
    /**
     * Get the transformer name
     */
    public function getName(): string;
}
