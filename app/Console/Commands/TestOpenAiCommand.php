<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Llm\Models\OpenAiTransformer;

class TestOpenAiCommand extends Command
{
    protected $signature = 'test:openai';
    protected $description = 'Test the OpenAI transformer';

    public function handle(OpenAiTransformer $transformer)
    {
        $this->info('Testing OpenAI Transformer');
        $this->info('Transformer name: ' . $transformer->getName());
        
        try {
            $this->info('Sending test message...');
            $response = $transformer->sendMessage('Hello, can you introduce yourself?');
            $this->info('Response received:');
            $this->info($response);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')');
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}
