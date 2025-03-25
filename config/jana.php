<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jana Application Settings
    |--------------------------------------------------------------------------
    |
    | This file contains configuration specific to the Jana application.
    |
    */

    // Application name and basic info
    'name' => env('JANA_NAME', 'Jana'),
    'description' => env('JANA_DESCRIPTION', 'Personal Assistant for ADHD'),
    'version' => env('JANA_VERSION', '0.1.0'),
    
    // LLM settings
    'llm' => [
        // Which LLM provider to use ('local' or 'openai')
        'provider' => env('LLM_PROVIDER', 'local'),
        
        // Local LLM settings
        'local' => [
            'base_url' => env('LLM_BASE_URL', 'http://192.168.5.119:1234'),
            'endpoint' => env('LLM_ENDPOINT', '/v1/chat/completions'),
        ],
        
        // OpenAI settings
        'openai' => [
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com'),
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
            'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 500),
            'temperature' => (float) env('OPENAI_TEMPERATURE', 0.7),
            'endpoint' => env('OPENAI_ENDPOINT', '/v1/chat/completions'),
        ],
        
        // Common LLM settings
        'system_message' => env('LLM_SYSTEM_MESSAGE', 'You are Jana, a helpful personal assistant for people with ADHD.'),
        'max_history' => (int) env('LLM_MAX_HISTORY', 10),
    ],
    
    // Embedding settings
    'embedding' => [
        // Which embedding provider to use (currently only 'openai' is supported)
        'provider' => env('EMBEDDING_PROVIDER', 'openai'),
        
        // OpenAI embedding settings
        'openai' => [
            'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-large'),
            'dimension' => (int) env('OPENAI_EMBEDDING_DIMENSION', 3072),
        ],
        
        // Similarity search settings
        'similarity' => [
            'min_score' => (float) env('EMBEDDING_MIN_SIMILARITY', 0.7),
            'max_results' => (int) env('EMBEDDING_MAX_RESULTS', 5),
        ],
    ],

    // Logging settings
    'logging' => [
        'llm' => [
            'enabled' => (bool) env('JANA_LLM_LOGGING_ENABLED', true),
            'truncate_length' => (int) env('JANA_LLM_LOG_TRUNCATE', 500),
        ],
    ],
];
