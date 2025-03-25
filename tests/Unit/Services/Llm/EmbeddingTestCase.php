<?php

namespace Tests\Unit\Services\Llm;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

abstract class EmbeddingTestCase extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Define the test environment setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Drop the embeddings table if it exists
        DB::statement('DROP TABLE IF EXISTS embeddings_test');
        
        // Create a test embeddings table with a smaller vector dimension
        DB::statement('CREATE TABLE embeddings_test (
            id SERIAL PRIMARY KEY,
            content_type VARCHAR(255) NOT NULL,
            content_id VARCHAR(255),
            text TEXT NOT NULL,
            model VARCHAR(255) NOT NULL,
            dimension INTEGER NOT NULL,
            embedding vector(10),
            created_at TIMESTAMP,
            updated_at TIMESTAMP
        )');
        
        // Create indexes
        DB::statement('CREATE INDEX embeddings_test_content_type_index ON embeddings_test (content_type)');
        DB::statement('CREATE INDEX embeddings_test_content_type_content_id_index ON embeddings_test (content_type, content_id)');
    }
    
    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Drop the test table
        DB::statement('DROP TABLE IF EXISTS embeddings_test');
        
        parent::tearDown();
    }
}
