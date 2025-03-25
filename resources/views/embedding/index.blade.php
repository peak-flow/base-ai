@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold mb-2">Embedding Test Tool</h1>
        <p class="text-gray-600">
            Current transformer: <span class="font-semibold">{{ $transformerName }}</span> |
            Embedding dimension: <span class="font-semibold">{{ $dimension }}</span>
        </p>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <form action="{{ route('embedding.generate') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="text" class="block text-sm font-medium text-gray-700 mb-2">Text to embed</label>
                <textarea 
                    id="text" 
                    name="text" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >{{ $text ?? '' }}</textarea>
            </div>
            
            <div class="mb-4">
                <label for="compare_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Compare with (optional)
                </label>
                <textarea 
                    id="compare_text" 
                    name="compare_text" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >{{ $compareText ?? '' }}</textarea>
                <p class="text-sm text-gray-500 mt-1">
                    If provided, similarity will be calculated between the two texts.
                </p>
            </div>
            
            <div>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Generate Embedding
                </button>
            </div>
        </form>
    </div>

    @if(isset($embedding) && !empty($embedding))
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Embedding Results</h2>
            
            @if(isset($similarity))
                <div class="mb-6 p-4 bg-gray-50 rounded-md">
                    <h3 class="text-lg font-medium mb-2">Similarity Score</h3>
                    <div class="text-3xl font-bold {{ $similarity > 0.8 ? 'text-green-600' : ($similarity > 0.5 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($similarity, 4) }}
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        Cosine similarity between the two texts (1.0 = identical, 0.0 = completely different)
                    </p>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Primary Text Embedding</h3>
                    <p class="text-sm text-gray-500 mb-2">
                        Showing first 10 of {{ count($embedding) }} dimensions
                    </p>
                    <div class="bg-gray-50 p-3 rounded-md overflow-x-auto">
                        <pre class="text-xs">{{ json_encode($embeddingPreview, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                
                @if(!empty($compareEmbedding))
                    <div>
                        <h3 class="text-lg font-medium mb-2">Comparison Text Embedding</h3>
                        <p class="text-sm text-gray-500 mb-2">
                            Showing first 10 of {{ count($compareEmbedding) }} dimensions
                        </p>
                        <div class="bg-gray-50 p-3 rounded-md overflow-x-auto">
                            <pre class="text-xs">{{ json_encode($compareEmbeddingPreview, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mt-6">
                <p class="text-sm text-gray-500">
                    <strong>Note:</strong> These are high-dimensional vectors used for semantic similarity calculations.
                    The values themselves don't have direct interpretations.
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
