<?php

namespace App\Services\Llm;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

/**
 * Base client for LLM API calls
 */
class LlmClient
{
    /**
     * The base URL for the LLM API.
     *
     * @var string
     */
    protected $baseUrl;
    
    /**
     * Create a new LLM client instance.
     *
     * @param string $baseUrl
     * @return void
     */
    public function __construct(string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? config('services.llm.base_url', 'http://192.168.5.119:1234');
    }
    
    /**
     * Send a request to the LLM API.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    public function sendRequest(string $endpoint, array $data = [], array $headers = []): array
    {
        try {
            $request = Http::timeout(30);
            
            // Add custom headers if provided
            if (!empty($headers)) {
                foreach ($headers as $key => $value) {
                    $request = $request->withHeader($key, $value);
                }
            }
            
            $response = $request->post($this->baseUrl . $endpoint, $data);
                
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('LLM API error: ' . $response->body());
        } catch (RequestException $e) {
            throw new \Exception('Failed to connect to LLM API: ' . $e->getMessage());
        }
    }
}
