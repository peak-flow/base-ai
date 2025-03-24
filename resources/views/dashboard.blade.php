@extends('layouts.app')

@section('header', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Introduction Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="md:flex">
            <div class="md:flex-shrink-0 bg-indigo-600 md:w-48 flex items-center justify-center p-6">
                <div class="h-32 w-32 rounded-full bg-white flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="p-8">
                <div class="uppercase tracking-wide text-sm text-indigo-600 font-semibold">Personal Assistant</div>
                <h2 class="mt-1 text-3xl font-bold text-gray-900">Meet Jana</h2>
                <p class="mt-2 text-gray-600">
                    Jana is your AI assistant and personal coach designed specifically to help you stay on task and complete your projects.
                    With features tailored for individuals with ADHD, Jana provides the structure and accountability you need.
                </p>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Project Management -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 rounded-md bg-blue-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Project Management</h3>
            <p class="text-gray-600">
                Keep track of all your projects in one place. Set priorities, deadlines, and monitor progress.
            </p>
        </div>

        <!-- Task Tracking -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 rounded-md bg-green-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Task Tracking</h3>
            <p class="text-gray-600">
                Break down projects into manageable tasks. Check them off as you complete them for a sense of accomplishment.
            </p>
        </div>

        <!-- Personal Diary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 rounded-md bg-yellow-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Personal Diary</h3>
            <p class="text-gray-600">
                Record your thoughts, track your mood, and reflect on your progress over time.
            </p>
        </div>

        <!-- AI Chat Assistant -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 rounded-md bg-purple-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">AI Chat Assistant</h3>
            <p class="text-gray-600">
                Chat with Jana for guidance, motivation, and accountability. Get help staying on track with your goals.
            </p>
        </div>

        <!-- Accountability -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 rounded-md bg-red-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Accountability</h3>
            <p class="text-gray-600">
                Jana helps you stay accountable to your commitments and deadlines, providing gentle reminders when needed.
            </p>
        </div>

        <!-- Model Comparison -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="w-12 h-12 rounded-md bg-indigo-100 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Model Comparison</h3>
            <p class="text-gray-600">
                Compare different AI models to see which one provides the most helpful responses for your specific needs.
            </p>
        </div>
    </div>

    <!-- Get Started Section -->
    <div class="bg-indigo-700 rounded-lg shadow-md p-8 text-white">
        <h2 class="text-2xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="mb-6">
            Jana is here to help you stay focused, organized, and productive. Start by creating your first project or having a chat with Jana.
        </p>
        <div class="flex flex-wrap gap-4">
            <a href="#" class="inline-block px-6 py-3 bg-white text-indigo-700 font-medium rounded-md hover:bg-indigo-50 transition duration-200">
                Create Project
            </a>
            <a href="#" class="inline-block px-6 py-3 border border-white text-white font-medium rounded-md hover:bg-indigo-600 transition duration-200">
                Chat with Jana
            </a>
        </div>
    </div>
</div>
@endsection
