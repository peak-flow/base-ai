<?php

// Bootstrap Laravel application
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set error reporting to show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>OpenAI Transformer Debug</h1>";

try {
    // Get the OpenAiTransformer from the container
    $transformer = app(\App\Services\Llm\Models\OpenAiTransformer::class);
    
    echo "<p>OpenAiTransformer loaded successfully!</p>";
    echo "<p>Transformer name: " . $transformer->getName() . "</p>";
    
    // Test sending a message
    echo "<h2>Testing message sending</h2>";
    $response = $transformer->sendMessage("Hello, can you introduce yourself?");
    
    echo "<h3>Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
} catch (\Exception $e) {
    echo "<h2>Error occurred:</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
