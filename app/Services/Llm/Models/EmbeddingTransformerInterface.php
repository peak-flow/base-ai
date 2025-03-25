<?php

namespace App\Services\Llm\Models;

/**
 * Interface that all embedding transformers must implement
 */
interface EmbeddingTransformerInterface
{
    /**
     * Generate an embedding for the given text
     *
     * @param string $text Text to generate embedding for
     * @return array The embedding vector
     */
    public function generateEmbedding(string $text): array;
    
    /**
     * Get the transformer name
     *
     * @return string
     */
    public function getName(): string;
    
    /**
     * Get the embedding dimension
     *
     * @return int
     */
    public function getDimension(): int;
}
