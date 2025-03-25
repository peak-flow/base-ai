<?php

namespace App\Services\Llm\Models;

use App\Services\Llm\LlmClient;
use App\Services\Llm\LlmLogger;
use Illuminate\Support\Facades\Log;

/**
 * Implementation for the OpenAI API transformer
 */
class OpenAiTransformer implements LlmTransformerInterface
{
    /**
     * The LLM client instance.
     *
     * @var LlmClient
     */
    protected $client;
    
    /**
     * The LLM logger instance.
     *
     * @var LlmLogger
     */
    protected $logger;
    
    /**
     * Create a new OpenAI transformer instance.
     *
     * @param LlmClient $client
     * @param LlmLogger $logger
     * @return void
     */
    public function __construct(LlmClient $client, LlmLogger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }
    
    /**
     * Get the transformer name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'openai';
    }
    
    /**
     * Send a message to the OpenAI API and get a response.
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    public function sendMessage(string $message, array $context = []): string
    {
        try {
            // Prepare the messages array for the chat completions API
            $messages = [];
            
            // Add system message if provided in context
            if (isset($context['system_message'])) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $context['system_message']
                ];
            } else {
                // Default system message from jana.php config
                $messages[] = [
                    'role' => 'system',
                    'content' => config('jana.llm.system_message', 'You are Jana, a helpful assistant. Provide concise, accurate responses.')
                ];
            }
            
            // Add chat history if provided in context
            if (isset($context['chat_history']) && is_array($context['chat_history'])) {
                foreach ($context['chat_history'] as $chat) {
                    if (isset($chat['role']) && isset($chat['content'])) {
                        $messages[] = [
                            'role' => $chat['role'],
                            'content' => $chat['content']
                        ];
                    }
                }
            }
            
            // Add the current user message
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];
            
            // Prepare the data for the API request
            $data = [
                'model' => config('services.openai.model', config('jana.llm.openai.model', 'gpt-3.5-turbo')),
                'messages' => $messages,
                'max_tokens' => (int) config('services.openai.max_tokens', config('jana.llm.openai.max_tokens', 500)),
                'temperature' => (float) config('services.openai.temperature', config('jana.llm.openai.temperature', 0.7)),
            ];
            
            // Generate a conversation ID if not provided in context
            $conversationId = $context['conversation_id'] ?? null;
            if (!$conversationId) {
                $conversationId = (string) \Illuminate\Support\Str::uuid();
                $data['conversation_id'] = $conversationId;
            }
            
            // Add API key to headers from services.php config
            $headers = [
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ];
            
            // Log the request using the dedicated logger
            $endpoint = config('jana.llm.openai.endpoint', '/v1/chat/completions');
            $this->logger->logRequest($endpoint, $data);
            
            // Send request to the OpenAI API using chat completions endpoint
            $endpoint = config('jana.llm.openai.endpoint', '/v1/chat/completions');
            $response = $this->client->sendRequest($endpoint, $data, $headers);
            
            // Log the response using the dedicated logger
            $this->logger->logResponse($response, $conversationId);
            
            // Extract the response text from the API response
            return $response['choices'][0]['message']['content'] ?? 'No response from OpenAI';
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('OpenAI API Error: ' . $e->getMessage(), [
                'message' => $message,
                'context' => $context,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a fallback message
            return 'Sorry, I encountered an error while processing your request. Please try again later.';
        }
    }
}
