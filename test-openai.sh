#!/bin/bash

# Get API key from .env file
API_KEY=$(grep OPENAI_API_KEY .env | cut -d '=' -f2)
MODEL=$(grep OPENAI_MODEL .env | grep -v '#' | cut -d '=' -f2)

echo "Testing OpenAI API with model: $MODEL"
echo "Using API key: ${API_KEY:0:10}..."

# Make the curl request to OpenAI API
curl -s https://api.openai.com/v1/chat/completions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $API_KEY" \
  -d "{
    \"model\": \"$MODEL\",
    \"messages\": [
      {
        \"role\": \"system\",
        \"content\": \"You are Jana, a helpful personal assistant for people with ADHD.\"
      },
      {
        \"role\": \"user\",
        \"content\": \"Hello, can you introduce yourself?\"
      }
    ],
    \"max_tokens\": 150,
    \"temperature\": 0.7
  }" | jq .

echo ""
echo "If you see a proper JSON response above with 'choices' containing a message, your API key is working correctly."
echo "If you see an error, check your API key and model name."
