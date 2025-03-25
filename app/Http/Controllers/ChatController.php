<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Llm\ChatService;

class ChatController extends Controller
{
    protected $chatService;

    /**
     * Create a new controller instance.
     *
     * @param ChatService $chatService
     * @return void
     */
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Display the chat interface.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get messages from session if they exist
        $messages = session('chat_messages', []);
        
        return view('chat.index', compact('messages'));
    }

    /**
     * Process a chat message and return the response.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        
        // Get existing messages from session
        $messages = session('chat_messages', []);
        
        // Add user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toIso8601String(),
        ];
        
        try {
            // Get response from LLM
            $llmResponse = $this->chatService->sendMessage($userMessage);
            
            // Add assistant message
            $messages[] = [
                'role' => 'assistant',
                'content' => $llmResponse,
                'timestamp' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            // Add error message
            $messages[] = [
                'role' => 'assistant',
                'content' => 'Sorry, I encountered an error: ' . $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ];
        }
        
        // Save messages to session
        session(['chat_messages' => $messages]);
        
        // Redirect back to chat
        return redirect()->route('chat.index');
    }
    
    /**
     * Clear chat history.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearChat(Request $request)
    {
        // Clear chat messages from session
        session()->forget('chat_messages');
        
        return redirect()->route('chat.index');
    }
}
