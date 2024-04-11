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

    public static function createChatTitle($content)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sei un creatore di titoli'
                ],
                [
                    'role' => 'user',
                    'content' =>  "Crea un titolo di al massimo 30 caratteri per una chat con questo messaggio: {$content}"
                ]
            ]
        ]);

        return $response->choices[0]->message->content;
    }

    public static function createStreamedChat($systemPrompt, $userPrompt)
    {
        $stream = Openai::chat()->createStreamed([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.8,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        return $stream;
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