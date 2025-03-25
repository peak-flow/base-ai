@extends('layouts.app')

@section('header', 'Chat with Jana')

@section('content')
<div class="py-6 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Chat header -->
            <div class="bg-indigo-700 text-white px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Chat with Jana</h2>
                <form action="{{ route('chat.clear') }}" method="POST" class="inline">
                    @csrf
                    <button 
                        type="submit"
                        class="text-white hover:text-indigo-200 focus:outline-none"
                        onclick="return confirm('Are you sure you want to clear the chat history?')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
            </div>
            
            <!-- Chat messages -->
            <div class="px-6 py-4 h-96 overflow-y-auto" id="chat-messages">
                @if(count($messages) === 0)
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500 text-center">
                            No messages yet. Start a conversation with Jana!
                        </p>
                    </div>
                @else
                    @foreach($messages as $message)
                        <div 
                            class="mb-4 p-4 rounded-lg shadow-sm {{ $message['role'] === 'user' ? 'bg-indigo-50 ml-12' : 'bg-white mr-12' }}"
                        >
                            <div class="flex items-start">
                                <div 
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-white mr-3 flex-shrink-0 {{ $message['role'] === 'user' ? 'bg-indigo-600' : 'bg-gray-600' }}"
                                >
                                    <span>{{ $message['role'] === 'user' ? 'U' : 'J' }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-800">{{ $message['content'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($message['timestamp'])->format('g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <!-- Chat input -->
            <div class="border-t border-gray-200 px-6 py-4">
                <form id="chat-form" action="{{ route('chat.send') }}" method="POST" class="flex items-center">
                    @csrf
                    <input 
                        type="text" 
                        name="message"
                        placeholder="Type your message..." 
                        class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                        autofocus
                    >
                    <button 
                        type="submit" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-r-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-scroll to the bottom of the chat messages when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endsection
