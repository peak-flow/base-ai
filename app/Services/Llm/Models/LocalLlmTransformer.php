<?php

namespace App\Services\Llm\Models;

use App\Services\Llm\LlmClient;
use Illuminate\Support\Facades\Log;

/**
 * Implementation for the local LLM transformer
 */
class LocalLlmTransformer implements LlmTransformerInterface
{
    /**
     * The LLM client instance.
     *
     * @var LlmClient
     */
    protected $client;
    
    /**
     * Create a new local LLM transformer instance.
     *
     * @param LlmClient $client
     * @return void
     */
    public function __construct(LlmClient $client)
    {
        $this->client = $client;
    }
    
    /**
     * Send a message to the local LLM and get a response
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    public function sendMessage(string $message, array $context = []): string
    {
        try {
            // Format the request data for chat completions
            $messages = [];
            
            // Add system message if provided in context
            if (!empty($context['system_message'])) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $context['system_message']
                ];
            } else {
                // Default system message
                $messages[] = [
                    'role' => 'system',
                    'content' => 'You are Jana, a helpful personal assistant. Be concise, friendly, and provide accurate information.'
                ];
            }
            
            // Add chat history if provided in context
            if (!empty($context['history']) && is_array($context['history'])) {
                foreach ($context['history'] as $historyMessage) {
                    if (isset($historyMessage['role']) && isset($historyMessage['content'])) {
                        $messages[] = $historyMessage;
                    }
                }
            }
            
            // Add the current user message
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];
            
            // Prepare the request data
            $data = [
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.7,
                // Add any other parameters your LLM API expects
            ];
            
            // Send request to the LLM API using chat completions endpoint
            $response = $this->client->sendRequest('/v1/chat/completions', $data);
            
            // Extract the response text from the API response
            // This format is typical for chat completion APIs
            return $response['choices'][0]['message']['content'] ?? 'No response from LLM';
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('LLM Error: ' . $e->getMessage());
            
            // Return a fallback response
            return 'I apologize, but I am having trouble connecting to my brain at the moment. Please try again later.';
        }
    }
    
    /**
     * Get the transformer name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Local LLM';
    }
}
