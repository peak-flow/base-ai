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
        // Get chat history from session if not provided in context
        if (empty($context['history'])) {
            $messages = session('chat_messages', []);
            // Only include messages that have both role and content
            $history = array_filter($messages, function($msg) {
                return isset($msg['role']) && isset($msg['content']);
            });
            
            // Add history to context
            $context['history'] = $history;
        }
        
        return $this->transformer->sendMessage($message, $context);
    }
}
