<?php

namespace App\Services\Llm;

use App\Services\Llm\Models\LlmTransformerInterface;

/**
 * Service for handling chat completions with LLM
 */
class ChatService
{
    protected $transformer;
    
    /**
     * Create a new chat service instance.
     *
     * @param LlmTransformerInterface $transformer
     * @return void
     */
    public function __construct(LlmTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }
    
    /**
     * Send a message to the LLM and get a response.
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    public function sendMessage(string $message, array $context = []): string
    {
        return $this->transformer->sendMessage($message, $context);
    }
}
