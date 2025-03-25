<?php

namespace Tests\Unit\Services\Llm;

use App\Models\Embedding;
use App\Services\Llm\EmbeddingService;
use App\Services\Llm\EmbeddingStorageService;
use App\Services\Llm\Models\EmbeddingTransformerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmbeddingStorageServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private EmbeddingService $embeddingService;
    private EmbeddingStorageService $embeddingStorageService;
    private $transformer; // Mock of EmbeddingTransformerInterface
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // We'll use the default dimension from the config
        // This ensures our tests work with the existing database schema
        
        // Mock the embedding transformer
        $this->transformer = $this->getMockBuilder(EmbeddingTransformerInterface::class)
            ->getMock();
        
        // Create the embedding service with the mock transformer
        $this->embeddingService = new EmbeddingService($this->transformer);
        
        // Create the embedding storage service
        $this->embeddingStorageService = new EmbeddingStorageService($this->embeddingService);
    }
    
    public function testStoreEmbedding(): void
    {
        // Sample text to embed
        $text = 'This is a test embedding';
        $contentType = 'test';
        $contentId = '123';
        
        // Mock the generateEmbedding method to return a sample embedding
        // Use the full 3072 dimensions to match the database schema
        $sampleEmbedding = array_fill(0, 3072, 0.1); // Full-sized vector for testing
        $this->transformer->expects($this->any())
            ->method('generateEmbedding')
            ->willReturn($sampleEmbedding);
        
        $this->transformer->expects($this->any())
            ->method('getName')
            ->willReturn('test-embedding-model');
            
        $this->transformer->expects($this->any())
            ->method('getDimension')
            ->willReturn(3072);
        
        // Store the embedding
        $embedding = $this->embeddingStorageService->storeEmbedding($text, $contentType, $contentId);
        
        // Assert the embedding was stored correctly
        $this->assertInstanceOf(Embedding::class, $embedding);
        $this->assertEquals($text, $embedding->text);
        $this->assertEquals($contentType, $embedding->content_type);
        $this->assertEquals($contentId, $embedding->content_id);
        $this->assertEquals('test-embedding-model', $embedding->model);
        $this->assertEquals(3072, $embedding->dimension);
        
        // Assert the embedding vector was stored
        $this->assertNotNull($embedding->embedding);
        
        // Check the database directly
        $this->assertDatabaseHas('embeddings', [
            'text' => $text,
            'content_type' => $contentType,
            'content_id' => $contentId,
        ]);
    }
    
    public function testFindSimilarByText(): void
    {
        // Create some test embeddings in the database
        $texts = [
            'apple fruit red delicious',
            'banana yellow fruit tropical',
            'orange citrus fruit',
            'computer technology device',
            'smartphone mobile technology'
        ];
        
        // Sample embeddings - in a real scenario these would be meaningful vectors
        // For testing, we'll create vectors that make 'computer' and 'smartphone' similar
        // We need to use full 3072-dimension vectors to match the database schema
        $embeddings = [
            // Create fruit cluster (first 1536 dimensions have values, rest are 0)
            array_fill(0, 1536, 0.1) + array_fill(1536, 1536, 0.0), // apple
            array_fill(0, 1536, 0.2) + array_fill(1536, 1536, 0.0), // banana
            array_fill(0, 1536, 0.3) + array_fill(1536, 1536, 0.0), // orange
            // Create tech cluster (first 1536 dimensions are 0, rest have values)
            array_fill(0, 1536, 0.0) + array_fill(1536, 1536, 0.2), // computer
            array_fill(0, 1536, 0.0) + array_fill(1536, 1536, 0.3)  // smartphone
        ];
        
        // Store the embeddings
        for ($i = 0; $i < count($texts); $i++) {
            $embedding = new Embedding([
                'content_type' => 'test',
                'content_id' => (string)($i + 1),
                'text' => $texts[$i],
                'model' => 'test-model',
                'dimension' => 3072, // Match the database schema
            ]);
            
            // Set the embedding vector
            $embedding->setEmbeddingArray($embeddings[$i]);
            $embedding->save();
        }
        
        // Mock the transformer to return a vector similar to the tech cluster
        // First 1536 dimensions are 0, rest have values (similar to tech cluster)
        $queryVector = array_fill(0, 1536, 0.0) + array_fill(1536, 1536, 0.9); // Should be closest to computer and smartphone
        $this->transformer->expects($this->any())
            ->method('generateEmbedding')
            ->willReturn($queryVector);
        
        // Search for similar embeddings
        $queryText = 'technology device';
        $results = $this->embeddingStorageService->findSimilarByText($queryText, 'test', 2);
        
        // Assert we got the expected results
        $this->assertCount(2, $results);
        
        // The results should be the tech cluster (computer and smartphone)
        $resultTexts = $results->pluck('text')->toArray();
        $this->assertContains('computer technology device', $resultTexts);
        $this->assertContains('smartphone mobile technology', $resultTexts);
        
        // Fruit texts should not be in the results
        $this->assertNotContains('apple fruit red delicious', $resultTexts);
        $this->assertNotContains('banana yellow fruit tropical', $resultTexts);
        $this->assertNotContains('orange citrus fruit', $resultTexts);
    }
}
