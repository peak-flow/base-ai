<?php

namespace App\Services\Llm;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service for logging LLM interactions
 */
class LlmLogger
{
    /**
     * Log an LLM request
     *
     * @param string $endpoint
     * @param array $data
     * @return void
     */
    public function logRequest(string $endpoint, array $data): void
    {
        // Create a sanitized copy of the data for logging
        $logData = $this->sanitizeData($data);
        
        // Generate a unique ID for this conversation
        $conversationId = $data['conversation_id'] ?? Str::uuid()->toString();
        
        // Log the request
        Log::channel('llm')->info('LLM Request', [
            'conversation_id' => $conversationId,
            'endpoint' => $endpoint,
            'data' => $logData,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
    
    /**
     * Log an LLM response
     *
     * @param array $response
     * @param string|null $conversationId
     * @return void
     */
    public function logResponse(array $response, ?string $conversationId = null): void
    {
        // Create a sanitized copy of the response for logging
        $logResponse = $this->sanitizeResponse($response);
        
        // Log the response
        Log::channel('llm')->info('LLM Response', [
            'conversation_id' => $conversationId ?? ($response['id'] ?? Str::uuid()->toString()),
            'response' => $logResponse,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
    
    /**
     * Sanitize request data for logging
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeData(array $data): array
    {
        $sanitized = $data;
        
        // Truncate long messages for log readability
        if (isset($sanitized['messages']) && is_array($sanitized['messages'])) {
            foreach ($sanitized['messages'] as &$msg) {
                if (isset($msg['content']) && strlen($msg['content']) > 100) {
                    $msg['content'] = substr($msg['content'], 0, 100) . '... [truncated]';
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize response data for logging
     *
     * @param array $response
     * @return array
     */
    protected function sanitizeResponse(array $response): array
    {
        $sanitized = $response;
        
        // Truncate long content in the response
        if (isset($sanitized['choices']) && is_array($sanitized['choices'])) {
            foreach ($sanitized['choices'] as &$choice) {
                if (isset($choice['message']['content'])) {
                    $content = $choice['message']['content'];
                    if (strlen($content) > 100) {
                        $choice['message']['content'] = substr($content, 0, 100) . '... [truncated]';
                    }
                }
                
                if (isset($choice['text']) && strlen($choice['text']) > 100) {
                    $choice['text'] = substr($choice['text'], 0, 100) . '... [truncated]';
                }
            }
        }
        
        return $sanitized;
    }
}
