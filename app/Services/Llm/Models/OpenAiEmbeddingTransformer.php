<?php

namespace App\Services\Llm\Models;

use App\Services\Llm\LlmClient;
use App\Services\Llm\LlmLogger;
use Illuminate\Support\Facades\Log;

/**
 * Implementation for the OpenAI API embedding transformer
 */
class OpenAiEmbeddingTransformer implements EmbeddingTransformerInterface
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
     * The embedding model to use.
     *
     * @var string
     */
    protected $model;
    
    /**
     * The dimension of the embedding vectors.
     *
     * @var int
     */
    protected $dimension;
    
    /**
     * Create a new OpenAI embedding transformer instance.
     *
     * @param LlmClient $client
     * @param LlmLogger $logger
     * @return void
     */
    public function __construct(LlmClient $client, LlmLogger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->model = config('services.openai.embedding_model', 'text-embedding-ada-002');
        
        // Set dimension based on model
        // text-embedding-ada-002 has 1536 dimensions
        // text-embedding-3-small has 1536 dimensions
        // text-embedding-3-large has 3072 dimensions
        $this->dimension = match($this->model) {
            'text-embedding-3-large' => 3072,
            default => 1536,
        };
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
     * Get the embedding dimension
     *
     * @return int
     */
    public function getDimension(): int
    {
        return $this->dimension;
    }
    
    /**
     * Generate an embedding for the given text using OpenAI's embedding API.
     *
     * @param string $text
     * @return array
     */
    public function generateEmbedding(string $text): array
    {
        try {
            // Prepare the data for the API request
            $data = [
                'model' => $this->model,
                'input' => $text,
            ];
            
            // Add API key to headers from services.php config
            $headers = [
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ];
            
            // Log the request using the dedicated logger
            $endpoint = '/v1/embeddings';
            $this->logger->logRequest($endpoint, $data);
            
            // Send request to the OpenAI API
            $response = $this->client->sendRequest($endpoint, $data, $headers);
            
            // Log the response using the dedicated logger
            $this->logger->logResponse($response);
            
            // Extract the embedding vector from the API response
            if (isset($response['data'][0]['embedding']) && is_array($response['data'][0]['embedding'])) {
                return $response['data'][0]['embedding'];
            }
            
            throw new \Exception('Invalid embedding response format from OpenAI API');
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('OpenAI Embedding API Error: ' . $e->getMessage(), [
                'text' => $text,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return an empty array as fallback
            return [];
        }
    }
}
