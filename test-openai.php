<?php

require __DIR__.'/vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get OpenAI configuration from .env
$apiKey = $_ENV['OPENAI_API_KEY'];
$baseUrl = $_ENV['OPENAI_BASE_URL'];
$model = $_ENV['OPENAI_MODEL'];
$endpoint = $_ENV['OPENAI_ENDPOINT'];
$maxTokens = (int) $_ENV['OPENAI_MAX_TOKENS'];
$temperature = (float) $_ENV['OPENAI_TEMPERATURE'];

echo "Testing OpenAI API with PHP\n";
echo "Base URL: {$baseUrl}\n";
echo "Endpoint: {$endpoint}\n";
echo "Model: {$model}\n";
echo "API Key: " . substr($apiKey, 0, 10) . "...\n\n";

// Prepare the request data
$requestData = [
    'model' => $model,
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are Jana, a helpful personal assistant for people with ADHD.'
        ],
        [
            'role' => 'user',
            'content' => 'Hello, can you introduce yourself?'
        ]
    ],
    'max_tokens' => $maxTokens,
    'temperature' => $temperature
];

// Initialize cURL session
$ch = curl_init($baseUrl . $endpoint);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

// Execute the cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    // Check if the request was successful
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "API request successful!\n";
        echo "Response:\n";
        echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
    } else {
        echo "API request failed with status code: {$httpCode}\n";
        echo "Error message: {$response}\n";
    }
}

// Close cURL session
curl_close($ch);
