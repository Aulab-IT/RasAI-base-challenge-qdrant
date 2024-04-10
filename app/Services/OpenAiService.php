<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAiService {
    public static function createEmbedding($content)
    {
        $embedding = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $content
        ]);

        return $embedding->embeddings[0]->embedding;
    }

    public static function speechToText($audioPath)
    {
        $response = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($audioPath , 'r'),
            'response_format' => 'verbose_json',
        ]);

        return $response->text;
    }
}