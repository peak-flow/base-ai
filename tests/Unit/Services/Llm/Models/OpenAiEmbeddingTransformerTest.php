<?php

namespace Tests\Unit\Services\Llm\Models;

use App\Services\Llm\LlmClient;
use App\Services\Llm\LlmLogger;
use App\Services\Llm\Models\OpenAiEmbeddingTransformer;
use Illuminate\Http\Client\Response;
use Mockery;
use Tests\TestCase;

class OpenAiEmbeddingTransformerTest extends TestCase
{
    protected $mockLlmClient;
    protected $mockLlmLogger;
    protected $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock for the LlmClient
        $this->mockLlmClient = Mockery::mock(LlmClient::class);
        
        // Create a mock for the LlmLogger
        $this->mockLlmLogger = Mockery::mock(LlmLogger::class);
        
        // Set up the logger mock to accept any logging calls
        $this->mockLlmLogger->shouldReceive('logRequest')->andReturn(null);
        $this->mockLlmLogger->shouldReceive('logResponse')->andReturn(null);
        
        // Create the transformer with the mock client and logger
        $this->transformer = new OpenAiEmbeddingTransformer(
            $this->mockLlmClient,
            $this->mockLlmLogger
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that the transformer returns the correct name.
     */
    public function test_get_name(): void
    {
        $this->assertEquals('openai', $this->transformer->getName());
    }

    /**
     * Test that the transformer returns the correct dimension.
     */
    public function test_get_dimension(): void
    {
        $dimension = $this->transformer->getDimension();
        $this->assertEquals(3072, $dimension);
    }

    /**
     * Test that the transformer can generate embeddings.
     */
    public function test_generate_embedding(): void
    {
        // Create a sample embedding vector
        $sampleEmbedding = array_fill(0, 3072, 0.1);
        
        // Create a sample response array (not a Response object)
        $responseArray = [
            'data' => [
                [
                    'embedding' => $sampleEmbedding
                ]
            ]
        ];
        
        // Set up the mock client to return our response array
        $this->mockLlmClient->shouldReceive('sendRequest')
            ->once()
            ->with(
                '/v1/embeddings',
                Mockery::on(function ($data) {
                    return isset($data['input']) && $data['input'] === 'Test text';
                }),
                Mockery::any()
            )
            ->andReturn($responseArray);
        
        // Call the method we're testing
        $embedding = $this->transformer->generateEmbedding('Test text');
        
        // Assert that the result is as expected
        $this->assertIsArray($embedding);
        $this->assertCount(3072, $embedding);
        $this->assertEquals($sampleEmbedding, $embedding);
    }

    /**
     * Test that the transformer handles API errors correctly.
     */
    public function test_generate_embedding_api_error(): void
    {
        // Set up the mock client to throw an exception
        $this->mockLlmClient->shouldReceive('sendRequest')
            ->once()
            ->andThrow(new \Exception('Test error message'));
        
        // Call the method we're testing - it should return an empty array as fallback
        $embedding = $this->transformer->generateEmbedding('Test text');
        
        // Assert that the result is an empty array
        $this->assertIsArray($embedding);
        $this->assertEmpty($embedding);
    }

    /**
     * Test that the transformer handles missing embedding data correctly.
     */
    public function test_generate_embedding_missing_data(): void
    {
        // Create a response array with missing embedding data
        $responseArray = ['data' => []];
        
        // Set up the mock client to return our response array
        $this->mockLlmClient->shouldReceive('sendRequest')
            ->once()
            ->andReturn($responseArray);
        
        // The method should throw an exception, which is caught internally
        // and an empty array is returned as fallback
        $embedding = $this->transformer->generateEmbedding('Test text');
        
        // Assert that the result is an empty array
        $this->assertIsArray($embedding);
        $this->assertEmpty($embedding);
    }
}
