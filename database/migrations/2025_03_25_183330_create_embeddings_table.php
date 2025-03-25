<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, make sure the vector extension is enabled
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
        
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('content_type')->index(); // Type of content (e.g., 'chat_message', 'document')
            $table->string('content_id')->nullable()->index(); // ID of the content this embedding is for
            $table->text('text'); // The original text that was embedded
            $table->string('model')->default('text-embedding-3-large'); // The model used to generate the embedding
            $table->integer('dimension')->default(3072); // Dimension of the embedding vector
            $table->timestamps();
            
            // Create a composite index for content_type and content_id
            $table->index(['content_type', 'content_id']);
        });
        
        // Get the embedding dimension from config or use default
        $dimension = config('jana.embedding.dimension', 3072);
        
        // Add the vector column - can't be done with Blueprint
        DB::statement("ALTER TABLE embeddings ADD COLUMN embedding vector($dimension)");
        
        // We'll use the vector column without an index initially
        // For production, we would need to consider dimensionality reduction or
        // using a version of pgvector that supports larger dimensions with indexes
        
        // Let's check what operator classes are available
        DB::statement('COMMENT ON COLUMN embeddings.embedding IS \'Embedding vector for similarity search\'');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the index first
        DB::statement('DROP INDEX IF EXISTS embeddings_embedding_idx');
        
        Schema::dropIfExists('embeddings');
    }
};
