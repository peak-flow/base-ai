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
            // Format the request data according to your local LLM API requirements
            $data = [
                'prompt' => $message,
                'max_tokens' => 500,
                'temperature' => 0.7,
                // Add any other parameters your LLM API expects
            ];
            
            // Add context if provided
            if (!empty($context)) {
                $data['context'] = $context;
            }
            
            // Send request to the LLM API
            $response = $this->client->sendRequest('/v1/completions', $data);
            
            // Extract the response text from the API response
            // This will need to be adjusted based on your LLM API's response format
            return $response['choices'][0]['text'] ?? 'No response from LLM';
            
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
