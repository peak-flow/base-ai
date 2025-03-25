<?php

namespace Tests\Unit\Services\Llm;

use App\Services\Llm\EmbeddingService;
use App\Services\Llm\Models\EmbeddingTransformerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class EmbeddingServiceTest extends TestCase
{
    protected $embeddingService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->embeddingService = app(EmbeddingService::class);
    }
    
    /**
     * Test that the embedding service returns the correct transformer name.
     */
    public function test_get_transformer_name(): void
    {
        $transformerName = $this->embeddingService->getTransformerName();
        $this->assertNotEmpty($transformerName);
        $this->assertIsString($transformerName);
    }
    
    /**
     * Test that the embedding service returns the correct dimension.
     */
    public function test_get_dimension(): void
    {
        $dimension = $this->embeddingService->getDimension();
        $this->assertIsInt($dimension);
        $this->assertGreaterThan(0, $dimension);
    }
    
    /**
     * Test that the embedding service can generate embeddings.
     */
    public function test_generate_embedding(): void
    {
        $text = "This is a test of the embedding functionality.";
        $embedding = $this->embeddingService->generateEmbedding($text);
        
        $this->assertIsArray($embedding);
        $this->assertCount($this->embeddingService->getDimension(), $embedding);
        $this->assertIsFloat($embedding[0]);
    }
    
    /**
     * Test similarity calculation between nearly identical texts.
     */
    public function test_similarity_nearly_identical_texts(): void
    {
        $text1 = "The quick brown fox jumps over the lazy dog.";
        $text2 = "The quick brown fox jumped over the lazy dog.";
        
        $embedding1 = $this->embeddingService->generateEmbedding($text1);
        $embedding2 = $this->embeddingService->generateEmbedding($text2);
        
        $similarity = $this->embeddingService->calculateSimilarity($embedding1, $embedding2);
        
        $this->assertIsFloat($similarity);
        $this->assertGreaterThan(0.9, $similarity);
    }
    
    /**
     * Test similarity calculation between related but different texts.
     */
    public function test_similarity_related_texts(): void
    {
        $text1 = "Artificial intelligence is transforming how we interact with technology.";
        $text2 = "Machine learning systems are changing the way humans use computers.";
        
        $embedding1 = $this->embeddingService->generateEmbedding($text1);
        $embedding2 = $this->embeddingService->generateEmbedding($text2);
        
        $similarity = $this->embeddingService->calculateSimilarity($embedding1, $embedding2);
        
        $this->assertIsFloat($similarity);
        $this->assertGreaterThan(0.5, $similarity);
        $this->assertLessThan(0.9, $similarity);
    }
    
    /**
     * Test similarity calculation between somewhat related texts.
     */
    public function test_similarity_somewhat_related_texts(): void
    {
        $text1 = "PostgreSQL is a powerful open-source relational database.";
        $text2 = "MongoDB is a popular NoSQL document database.";
        
        $embedding1 = $this->embeddingService->generateEmbedding($text1);
        $embedding2 = $this->embeddingService->generateEmbedding($text2);
        
        $similarity = $this->embeddingService->calculateSimilarity($embedding1, $embedding2);
        
        $this->assertIsFloat($similarity);
        $this->assertGreaterThan(0.3, $similarity);
        $this->assertLessThan(0.7, $similarity);
    }
    
    /**
     * Test similarity calculation between completely different texts.
     */
    public function test_similarity_different_texts(): void
    {
        $text1 = "The recipe calls for two cups of flour and one teaspoon of salt.";
        $text2 = "The spacecraft launched successfully and reached orbit around Mars.";
        
        $embedding1 = $this->embeddingService->generateEmbedding($text1);
        $embedding2 = $this->embeddingService->generateEmbedding($text2);
        
        $similarity = $this->embeddingService->calculateSimilarity($embedding1, $embedding2);
        
        $this->assertIsFloat($similarity);
        $this->assertLessThan(0.3, $similarity);
    }
    
    /**
     * Test finding similar embeddings from a collection.
     */
    public function test_find_similar_embeddings(): void
    {
        $texts = [
            "The quick brown fox jumps over the lazy dog.",
            "A fast auburn fox leaps above the sleepy canine.",
            "The rapid red fox hops over the tired hound.",
            "Artificial intelligence is transforming how we interact with technology.",
            "Machine learning systems are changing the way humans use computers.",
            "The recipe calls for two cups of flour and one teaspoon of salt.",
            "The spacecraft launched successfully and reached orbit around Mars."
        ];
        
        $formattedEmbeddings = [];
        foreach ($texts as $index => $text) {
            $formattedEmbeddings[] = [
                'id' => $index,
                'embedding' => $this->embeddingService->generateEmbedding($text),
                'data' => ['text' => $text]
            ];
        }
        
        $queryText = "The swift brown fox jumped over the lazy dog.";
        $queryEmbedding = $this->embeddingService->generateEmbedding($queryText);
        
        $results = $this->embeddingService->findSimilarEmbeddings($queryEmbedding, $formattedEmbeddings, 3);
        
        $this->assertCount(3, $results);
        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArrayHasKey('similarity', $results[0]);
        $this->assertGreaterThanOrEqual($results[1]['similarity'], $results[0]['similarity']);
        $this->assertGreaterThanOrEqual($results[2]['similarity'], $results[1]['similarity']);
        
        // The first three texts should be the most similar (fox-related texts)
        $this->assertContains($results[0]['id'], [0, 1, 2]);
        $this->assertContains($results[1]['id'], [0, 1, 2]);
        $this->assertContains($results[2]['id'], [0, 1, 2]);
    }
}
