<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

// TOFO : RINOMINARE EmbeddingService in OpenAiService
class EmbeddingService {
    public static function createEmbedding($content)
    {
        $embedding = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $content
        ]);

        return $embedding->embeddings[0]->embedding;
    }
}