<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Llm\EmbeddingService;

class EmbeddingController extends Controller
{
    /**
     * The embedding service instance.
     *
     * @var EmbeddingService
     */
    protected $embeddingService;
    
    /**
     * Create a new controller instance.
     *
     * @param EmbeddingService $embeddingService
     * @return void
     */
    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }
    
    /**
     * Show the embedding test form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('embedding.index', [
            'transformerName' => $this->embeddingService->getTransformerName(),
            'dimension' => $this->embeddingService->getDimension(),
        ]);
    }
    
    /**
     * Generate an embedding for the given text.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function generate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
            'compare_text' => 'nullable|string|max:1000',
        ]);
        
        $text = $request->input('text');
        $compareText = $request->input('compare_text');
        
        // Generate embedding for the primary text
        $embedding = $this->embeddingService->generateEmbedding($text);
        
        // Initialize variables for comparison
        $compareEmbedding = [];
        $similarity = null;
        
        // If comparison text is provided, generate embedding and calculate similarity
        if ($compareText) {
            $compareEmbedding = $this->embeddingService->generateEmbedding($compareText);
            $similarity = $this->embeddingService->calculateSimilarity($embedding, $compareEmbedding);
        }
        
        return view('embedding.index', [
            'transformerName' => $this->embeddingService->getTransformerName(),
            'dimension' => $this->embeddingService->getDimension(),
            'text' => $text,
            'compareText' => $compareText,
            'embedding' => $embedding,
            'compareEmbedding' => $compareEmbedding,
            'similarity' => $similarity,
            'embeddingPreview' => array_slice($embedding, 0, 10),
            'compareEmbeddingPreview' => !empty($compareEmbedding) ? array_slice($compareEmbedding, 0, 10) : [],
        ]);
    }
}
