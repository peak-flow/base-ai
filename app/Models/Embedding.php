<?php

namespace App\Models;

use App\Services\Llm\EmbeddingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Embedding extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content_type',
        'content_id',
        'text',
        'model',
        'dimension',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dimension' => 'integer',
    ];
    
    /**
     * Get the embedding vector as an array.
     *
     * @return array|null
     */
    public function getEmbeddingArray(): ?array
    {
        if (empty($this->embedding)) {
            return null;
        }
        
        // Convert the PostgreSQL vector format to a PHP array
        // The format is typically '[0.1,0.2,0.3,...]'
        $vector = $this->embedding;
        if (is_string($vector)) {
            // Remove brackets and split by comma
            $vector = trim($vector, '[]');
            $vector = explode(',', $vector);
            
            // Convert to float values
            return array_map('floatval', $vector);
        }
        
        return $vector;
    }
    
    /**
     * Set the embedding vector from an array.
     *
     * @param array $vector
     * @return void
     */
    public function setEmbeddingArray(array $vector): void
    {
        // Store the vector in the database
        // PostgreSQL expects a string in the format '[0.1,0.2,0.3,...]'
        $this->embedding = static::arrayToVector($vector);
    }
    
    /**
     * Scope a query to filter by content type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $contentType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $contentType)
    {
        return $query->where('content_type', $contentType);
    }
    
    /**
     * Scope a query to filter by content ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $contentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfContent($query, string $contentId)
    {
        return $query->where('content_id', $contentId);
    }
    
    /**
     * Find similar embeddings for the given vector.
     *
     * @param  array  $vector
     * @param  string|null  $contentType
     * @param  int  $limit
     * @return \Illuminate\Support\Collection
     */
    public static function findSimilar(array $vector, ?string $contentType = null, int $limit = 5): Collection
    {
        // Convert the PHP array to a PostgreSQL vector string
        $queryVector = static::arrayToVector($vector);
        
        // Build the query
        $query = DB::table('embeddings')
            ->select([
                'id',
                'content_type',
                'content_id',
                'text',
                'model',
                'dimension',
                'created_at',
                'updated_at',
                DB::raw("embedding <=> '$queryVector' as distance")
            ])
            ->orderBy('distance', 'asc')
            ->limit($limit);
        
        // Filter by content type if provided
        if ($contentType) {
            $query->where('content_type', $contentType);
        }
        
        // Execute the query
        $results = $query->get();
        
        // Convert the results to Embedding models
        return $results->map(function ($result) {
            $embedding = new static([
                'content_type' => $result->content_type,
                'content_id' => $result->content_id,
                'text' => $result->text,
                'model' => $result->model,
                'dimension' => $result->dimension,
            ]);
            
            $embedding->id = $result->id;
            $embedding->created_at = $result->created_at;
            $embedding->updated_at = $result->updated_at;
            
            // Add the distance as a custom attribute
            $embedding->distance = $result->distance;
            
            return $embedding;
        });
    }
    
    /**
     * Convert a PHP array to a PostgreSQL vector string.
     *
     * @param  array  $array
     * @return string
     */
    protected static function arrayToVector(array $array): string
    {
        return '[' . implode(',', $array) . ']';
    }
}
