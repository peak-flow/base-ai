# Application Architecture

This document outlines the architecture of the personal assistant and project management application.

## System Overview

The application is designed as a personal assistant and coach to help users with ADHD stay on task and complete projects. It integrates with a local LLM for conversational assistance and provides project management, task tracking, and diary functionality.

## Tech Stack

### Backend
- **Framework**: Laravel 10+
- **Database**: 
  - PostgreSQL for relational data
  - pgvector extension for vector embeddings (RAG)
- **LLM Integration**: 
  - Pluggable architecture supporting multiple LLM providers
  - Local LLM API (http://192.168.5.119:1234)
  - OpenAI API integration
- **Authentication**: Laravel Breeze/Fortify

### Frontend
- **Primary**: Blade templates
- **Interactive Components**: 
  - Alpine.js for reactive UI elements
  - Livewire for complex dynamic interfaces (chat, model comparison)
- **Styling**: Tailwind CSS

## Design Decisions

### Frontend Approach
- **Plain Blade Templates**: Using standard Blade templates without complex JavaScript frameworks for initial development to ensure simplicity and maintainability.
- **Progressive Enhancement**: Starting with basic functionality and adding interactivity only where needed, rather than building a full SPA from the start.
- **Feature-First Development**: Implementing each feature completely with a simple approach before adding complexity or optimizations.

### Backend Architecture
- **No Repository Pattern**: Deliberately avoiding the repository pattern to keep the codebase simpler and more approachable. Using direct model methods and service classes instead.
- **Service-Based Design**: Organizing functionality into focused service classes with clear responsibilities rather than complex abstraction layers.
- **Dependency Injection**: Using Laravel's service container for dependency management rather than static facades where possible.

### Vector Database Implementation
- **Cosine Similarity for Semantic Search**: Using cosine similarity (`<=>` operator) instead of L2 distance (`<->` operator) for embedding comparisons because:
  - Cosine similarity is better suited for NLP tasks as it measures the angle between vectors, not their absolute distance.
  - It's less affected by vector magnitude, making it more reliable for comparing text semantics.
  - Performance is comparable to L2 distance for our use case.

- **OpenAI Embedding Model**: Using the `text-embedding-3-large` model which produces 3072-dimension vectors, providing high-quality semantic representations.

- **PostgreSQL with pgvector**: Using PostgreSQL's pgvector extension rather than a dedicated vector database for simplicity and to avoid additional infrastructure requirements.

- **Dimension Handling**: The high dimensionality (3072) of OpenAI's embeddings presents challenges for indexing, so we're using:
  - Direct cosine similarity searches without complex indexing initially
  - Configurable dimension size to allow for future optimizations

- **Embedding Storage**: Storing full embedding vectors in the database rather than using dimensionality reduction techniques to maintain full semantic fidelity.

## Component Architecture

### Core Components

1. **Authentication System**
   - User registration, login, and profile management
   - Role-based access control

2. **Project Management Module**
   - Project CRUD operations
   - Status and priority management
   - Progress tracking

3. **Task Management Module**
   - Task CRUD operations
   - Task assignment to projects (optional)
   - Status, priority, and deadline management

4. **Personal Diary Module**
   - Entry creation and management
   - Mood tracking
   - Search functionality

5. **Chat Assistant Module**
   - Conversation management using session storage
   - Integration with local LLM via HTTP client
   - Standard form submissions for simplicity and reliability
   - Chat completions API format with support for:
     - System messages for persona definition
     - Chat history context for coherent conversations
     - Proper error handling and logging
   - Service-oriented architecture with the following components:
     - `LlmClient`: Handles HTTP communication with the local LLM API
     - `LlmTransformerInterface`: Interface for different LLM implementations
     - `LocalLlmTransformer`: Implementation for the local LLM
     - `ChatService`: Service for handling chat message processing and history management
   - Context-aware responses using RAG (planned)
   - Accountability features (planned)

6. **Model Comparison Module**
   - Testing interface for different LLM models
   - Performance metrics and comparison
   - Response storage and management

7. **Vector Database Module**
   - Embedding generation and storage using PostgreSQL with pgvector extension
   - Cosine similarity search for semantic retrieval of relevant content
   - Integration with OpenAI's text-embedding-3-large model
   - Simple, direct model approach without repository pattern for clarity
   - Configurable dimension size (default: 3072 for text-embedding-3-large)

## Data Flow

1. **User Interaction**
   - User interacts with the application through the web interface
   - Authentication system validates user access

2. **Data Management**
   - CRUD operations for projects, tasks, and diary entries
   - Data stored in PostgreSQL database

3. **LLM Integration**
   - User messages sent to local LLM API
   - Relevant context from vector database added to prompts
   - Responses processed and displayed to user
   
   **Example Request Format:**
   ```php
   // From LocalLlmTransformer.php
   $data = [
       'messages' => [
           [
               'role' => 'system',
               'content' => 'You are Jana, a helpful personal assistant. Be concise, friendly, and provide accurate information.'
           ],
           [
               'role' => 'user',
               'content' => 'What is the weather today?'
           ],
           [
               'role' => 'assistant',
               'content' => 'I don\'t have access to real-time weather data. Would you like me to help you find a weather service?'
           ],
           [
               'role' => 'user',
               'content' => 'Yes, please recommend one.'
           ]
       ],
       'max_tokens' => 500,
       'temperature' => 0.7
   ];
   
   // HTTP POST to: http://192.168.5.119:1234/v1/chat/completions
   ```
   
   **Code Flow Example:**
   ```
   1. User submits message via form in chat/index.blade.php
   2. Form submits to ChatController@sendMessage
   3. ChatController gets existing messages from session
   4. ChatController calls ChatService->sendMessage()
   5. ChatService adds chat history from session to context
   6. ChatService calls LocalLlmTransformer->sendMessage()
   7. LocalLlmTransformer formats request with messages array
   8. LocalLlmTransformer uses LlmLogger to log the request
   9. LocalLlmTransformer calls LlmClient->sendRequest()
   10. LlmClient makes HTTP POST request to LLM API
   11. LocalLlmTransformer uses LlmLogger to log the response
   12. Response is processed back through the chain
   13. ChatController stores the new message in session
   12. User is redirected back to chat view with updated messages
   ```
   
### Multi-Provider LLM Architecture

The application implements a pluggable architecture for LLM integration, allowing easy switching between different LLM providers. This design follows the strategy pattern, where different LLM implementations can be swapped without changing the client code.

**Components:**

1. **LlmTransformerInterface**
   - Interface that all LLM implementations must implement
   - Defines `sendMessage()` and `getName()` methods

2. **LocalLlmTransformer**
   - Implementation for local LLM API
   - Communicates with a self-hosted LLM at http://192.168.5.119:1234

3. **OpenAiTransformer**
   - Implementation for OpenAI API
   - Handles authentication and communication with OpenAI's chat completions endpoint

4. **LlmClient**
   - Generic HTTP client for LLM API communication
   - Supports custom headers for API authentication
   - Handles request/response formatting

5. **LlmServiceProvider**
   - Dynamically selects the appropriate transformer based on configuration
   - Binds the selected implementation to the interface in the service container

**Configuration:**

The LLM provider can be switched by changing the `LLM_PROVIDER` environment variable:

```
# Use local LLM
LLM_PROVIDER=local

# Use OpenAI
LLM_PROVIDER=openai
```

**Provider-Specific Settings:**

```php
// Local LLM settings
'llm' => [
    'base_url' => env('LLM_BASE_URL', 'http://192.168.5.119:1234'),
    'provider' => env('LLM_PROVIDER', 'local'),
],

// OpenAI settings
'openai' => [
    'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com'),
    'api_key' => env('OPENAI_API_KEY'),
    'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
    'temperature' => env('OPENAI_TEMPERATURE', 0.7),
],
```

### LLM Logging Architecture

The application includes a dedicated logging system for LLM interactions to track requests and responses. This is integrated with the vector database for embeddings.

### Vector Database Architecture

The application implements a vector database using PostgreSQL with the pgvector extension to store and retrieve embeddings for semantic search capabilities.

**Components:**

1. **Embedding Model**
   - Represents stored embeddings in the database
   - Handles vector operations and similarity searches
   - Uses cosine similarity (`<=>` operator) for semantic search

2. **EmbeddingService**
   - Generates embeddings using the configured transformer
   - Abstracts the embedding generation process

3. **EmbeddingStorageService**
   - Manages the storage and retrieval of embeddings
   - Provides methods for finding similar content by text or vector

4. **EmbeddingTransformerInterface**
   - Interface for embedding generation implementations
   - Defines methods for generating embeddings and getting model information

**Database Schema:**

```sql
CREATE TABLE embeddings (
    id SERIAL PRIMARY KEY,
    content_type VARCHAR(255) NOT NULL,
    content_id VARCHAR(255),
    text TEXT NOT NULL,
    model VARCHAR(255) NOT NULL,
    dimension INTEGER NOT NULL,
    embedding vector(3072),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX embeddings_content_type_index ON embeddings (content_type);
CREATE INDEX embeddings_content_type_content_id_index ON embeddings (content_type, content_id);
```

**Usage Example:**

```php
// Store an embedding
$embeddingStorage->storeEmbedding(
    'This is a sample text to embed',
    'message',
    '123'
);

// Find similar content
$similarItems = $embeddingStorage->findSimilarByText(
    'What was that sample text again?',
    'message',
    5
);
```

**Implementation Notes:**

- Uses cosine similarity for NLP-style semantic search
- Direct model approach without repository pattern for simplicity
- Configurable dimension size based on the embedding model used
- Designed to work with OpenAI's text-embedding-3-large model (3072 dimensions)

### Embedding Architecture

The application implements a pluggable architecture for text embeddings, similar to the LLM integration. This allows for easy switching between different embedding providers while maintaining a consistent interface.

**Components:**

1. **EmbeddingTransformerInterface**
   - Interface that all embedding implementations must implement
   - Defines `generateEmbedding()`, `getName()`, and `getDimension()` methods

2. **OpenAiEmbeddingTransformer**
   - Implementation for OpenAI's embedding API
   - Handles authentication and communication with OpenAI's embeddings endpoint
   - Supports different embedding models with automatic dimension detection

3. **EmbeddingService**
   - High-level service for generating and working with embeddings
   - Provides vector similarity calculation using cosine similarity
   - Includes methods for finding similar embeddings from a collection

4. **LlmClient** (shared with LLM architecture)
   - Generic HTTP client for API communication
   - Reused for embedding API requests

5. **LlmServiceProvider**
   - Registers and binds embedding services in the service container
   - Configures the appropriate transformer based on settings

**Configuration:**

Embedding settings are configured in both `services.php` and `jana.php` configuration files:

```php
// In services.php
'openai' => [
    // ... other OpenAI settings
    'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-ada-002'),
],

// In jana.php
'embedding' => [
    'provider' => env('EMBEDDING_PROVIDER', 'openai'),
    'openai' => [
        'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-ada-002'),
        'dimension' => (int) env('OPENAI_EMBEDDING_DIMENSION', 1536),
    ],
    'similarity' => [
        'min_score' => (float) env('EMBEDDING_MIN_SIMILARITY', 0.7),
        'max_results' => (int) env('EMBEDDING_MAX_RESULTS', 5),
    ],
],
```

**Usage Example:**

```php
// Inject the EmbeddingService into your class
public function __construct(EmbeddingService $embeddingService)
{
    $this->embeddingService = $embeddingService;
}

// Generate an embedding for a text
$embedding = $this->embeddingService->generateEmbedding('This is some text to embed');

// Calculate similarity between two embeddings
$similarity = $this->embeddingService->calculateSimilarity($embedding1, $embedding2);

// Find similar embeddings from a collection
$similarItems = $this->embeddingService->findSimilarEmbeddings($queryEmbedding, $embeddings, 5);
```

The embedding architecture is designed to be extensible, allowing for additional embedding providers to be added in the future without changing the client code. The current implementation uses OpenAI's embedding API, but other providers can be easily integrated by implementing the `EmbeddingTransformerInterface`.

**Components:**

1. **LlmLogger Service**
   - Handles logging of LLM requests and responses
   - Uses Laravel's logging system with a dedicated 'llm' channel
   - Sanitizes and truncates long content for readability
   - Tracks conversation IDs to associate related messages

**Log Format:**

```
// Request Log
{
    "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
    "endpoint": "/v1/chat/completions",
    "data": {
        "messages": [
            {"role": "system", "content": "You are Jana, a helpful assistant... [truncated]"},
            {"role": "user", "content": "What is the weather today?"}
        ],
        "max_tokens": 500,
        "temperature": 0.7
    },
    "timestamp": "2025-03-25T02:30:00-04:00"
}

// Response Log
{
    "conversation_id": "550e8400-e29b-41d4-a716-446655440000",
    "response": {
        "id": "chatcmpl-123",
        "choices": [
            {
                "message": {
                    "role": "assistant",
                    "content": "I don't have access to real-time weather data... [truncated]"
                }
            }
        ]
    },
    "timestamp": "2025-03-25T02:30:01-04:00"
}
```

**Configuration:**

The logging system uses a dedicated daily log channel configured in `config/logging.php`:

```php
'llm' => [
    'driver' => 'daily',
    'path' => storage_path('logs/llm.log'),
    'level' => 'info',
    'days' => 30,
    'replace_placeholders' => true,
],
```
   
   **Example Response Format:**
   ```json
   {
       "id": "chatcmpl-123456789",
       "object": "chat.completion",
       "created": 1679351234,
       "model": "local-model",
       "choices": [
           {
               "index": 0,
               "message": {
                   "role": "assistant",
                   "content": "I recommend checking Weather.gov for accurate weather forecasts in the US. Other good options include AccuWeather, The Weather Channel, or Dark Sky. Most smartphones also have built-in weather apps that provide reliable forecasts."
               },
               "finish_reason": "stop"
           }
       ],
       "usage": {
           "prompt_tokens": 124,
           "completion_tokens": 57,
           "total_tokens": 181
       }
   }
   ```

4. **RAG Implementation**
   - Chat messages converted to embeddings
   - Stored in vector database using pgvector
   - Retrieved based on semantic similarity to enhance conversation context

5. **Model Comparison**
   - User prompts sent to multiple LLM models
   - Responses collected and displayed for comparison
   - Performance metrics tracked and stored

## Security Considerations

- User authentication and authorization
- Secure API communication with local LLM
- Data encryption for sensitive information
- Input validation and sanitization
- CSRF protection

## Scalability Considerations

- Queue system for handling longer processing tasks
- Efficient vector search implementation
- Database indexing for performance
- Caching strategies for frequently accessed data
