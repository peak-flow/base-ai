# Embedding Architecture in Jana

This document outlines the architecture and implementation details of the embedding functionality in the Jana application.

## Overview

The embedding system in Jana provides a way to convert text into vector representations (embeddings) that capture semantic meaning. These embeddings enable powerful semantic search, similarity comparison, and context-aware retrieval capabilities.

## Key Components

### 1. EmbeddingTransformerInterface

The core of the embedding system is built around a pluggable architecture using the `EmbeddingTransformerInterface`. This interface defines the contract that any embedding implementation must follow:

```php
interface EmbeddingTransformerInterface
{
    public function getName(): string;
    public function getDimension(): int;
    public function generateEmbedding(string $text): array;
}
```

This design allows for easy swapping between different embedding providers or models.

### 2. OpenAiEmbeddingTransformer

The primary implementation is the `OpenAiEmbeddingTransformer`, which integrates with OpenAI's embedding API:

- Uses the `text-embedding-3-large` model (3072 dimensions)
- Handles API communication through the `LlmClient`
- Includes error handling and logging via `LlmLogger`
- Configurable through environment variables

### 3. EmbeddingService

The `EmbeddingService` provides high-level functionality for working with embeddings:

- Generates embeddings for text inputs
- Calculates cosine similarity between embedding vectors
- Finds similar embeddings from a collection
- Abstracts away the underlying transformer implementation

## Configuration

Embedding settings are configured in multiple places:

1. **Environment Variables**:
   - `OPENAI_EMBEDDING_MODEL`: Currently set to `text-embedding-3-large`
   - `OPENAI_EMBEDDING_DIMENSION`: Set to `3072`

2. **Config Files**:
   - `config/services.php`: Contains API connection details
   - `config/jana.php`: Contains application-specific embedding settings

## Testing

The embedding system is thoroughly tested with PHPUnit:

1. **EmbeddingServiceTest**:
   - Tests for generating embeddings
   - Tests for calculating similarity between text pairs
   - Tests for finding similar embeddings from a collection

2. **OpenAiEmbeddingTransformerTest**:
   - Tests for the transformer name and dimension
   - Tests for generating embeddings
   - Tests for error handling

## Usage Examples

### Generating Embeddings

```php
$embeddingService = app(EmbeddingService::class);
$embedding = $embeddingService->generateEmbedding("Your text here");
```

### Calculating Similarity

```php
$similarity = $embeddingService->calculateSimilarity($embedding1, $embedding2);
// Returns a value between 0 (completely different) and 1 (identical)
```

### Finding Similar Embeddings

```php
$results = $embeddingService->findSimilarEmbeddings($queryEmbedding, $embeddings, $limit);
// Returns an array of the most similar embeddings with their similarity scores
```

## Future Enhancements

1. **Vector Database Integration**: Store embeddings in PostgreSQL with pgvector for efficient similarity search
2. **Batch Processing**: Add support for generating embeddings in batches
3. **Caching**: Implement caching for frequently used embeddings
4. **Alternative Models**: Add support for additional embedding models and providers
5. **Hybrid Search**: Combine keyword and semantic search for improved results
