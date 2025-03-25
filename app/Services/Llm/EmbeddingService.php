<?php

namespace App\Services\Llm;

use App\Services\Llm\Models\EmbeddingTransformerInterface;
use Illuminate\Support\Facades\Log;

/**
 * Service for generating text embeddings
 */
class EmbeddingService
{
    /**
     * The embedding transformer instance.
     *
     * @var EmbeddingTransformerInterface
     */
    protected $transformer;
    
    /**
     * Create a new embedding service instance.
     *
     * @param EmbeddingTransformerInterface $transformer
     * @return void
     */
    public function __construct(EmbeddingTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }
    
    /**
     * Generate an embedding for the given text.
     *
     * @param string $text
     * @return array
     */
    public function generateEmbedding(string $text): array
    {
        try {
            return $this->transformer->generateEmbedding($text);
        } catch (\Exception $e) {
            Log::error('Embedding generation error: ' . $e->getMessage(), [
                'text' => $text,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [];
        }
    }
    
    /**
     * Get the dimension of the embeddings.
     *
     * @return int
     */
    public function getDimension(): int
    {
        return $this->transformer->getDimension();
    }
    
    /**
     * Get the name of the transformer being used.
     *
     * @return string
     */
    public function getTransformerName(): string
    {
        return $this->transformer->getName();
    }
    
    /**
     * Calculate cosine similarity between two embedding vectors.
     *
     * @param array $embedding1
     * @param array $embedding2
     * @return float
     */
    public function calculateSimilarity(array $embedding1, array $embedding2): float
    {
        if (empty($embedding1) || empty($embedding2)) {
            return 0.0;
        }
        
        // Calculate dot product
        $dotProduct = 0;
        foreach ($embedding1 as $i => $value) {
            if (isset($embedding2[$i])) {
                $dotProduct += $value * $embedding2[$i];
            }
        }
        
        // Calculate magnitudes
        $magnitude1 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $embedding1)));
        $magnitude2 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $embedding2)));
        
        // Avoid division by zero
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }
        
        // Calculate cosine similarity
        return $dotProduct / ($magnitude1 * $magnitude2);
    }
    
    /**
     * Find the most similar embeddings from a collection.
     *
     * @param array $queryEmbedding
     * @param array $embeddings Array of arrays, each containing 'id' and 'embedding' keys
     * @param int $limit
     * @return array
     */
    public function findSimilarEmbeddings(array $queryEmbedding, array $embeddings, int $limit = 5): array
    {
        if (empty($queryEmbedding) || empty($embeddings)) {
            return [];
        }
        
        $similarities = [];
        
        foreach ($embeddings as $item) {
            if (!isset($item['embedding']) || !isset($item['id'])) {
                continue;
            }
            
            $similarity = $this->calculateSimilarity($queryEmbedding, $item['embedding']);
            
            $similarities[] = [
                'id' => $item['id'],
                'similarity' => $similarity,
                'data' => $item['data'] ?? null,
            ];
        }
        
        // Sort by similarity (highest first)
        usort($similarities, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });
        
        // Return top results
        return array_slice($similarities, 0, $limit);
    }
}
