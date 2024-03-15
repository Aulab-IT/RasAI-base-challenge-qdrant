<?php

namespace App\Services;

use App\Models\Document;

final class DocumentSplitter 
{
    public static function splitDocument(string $text, int $maxLength = 1000, string $separator = ' ', int $overlapping = 20): array
    {
        // Check if the text is empty
        if (empty($text)) {
            return [];
        }
        
        // Check if maxLength is valid
        if ($maxLength <= 0) {
            return [];
        }

        // Check if the separator is empty
        if ($separator === '') {
            return [];
        }

        // If the text length is less than or equal to maxLength, return the text as a single chunk
        if (strlen($text) <= $maxLength) {
            return [$text];
        }

        $chunks = [];
        $words = explode($separator, $text);
        $currentChunk = '';

        // Iterate through each word in the text
        foreach ($words as $word) {
            // Check if adding the current word to the current chunk exceeds the maxLength
            if (strlen($currentChunk.$separator.$word) <= $maxLength || empty($currentChunk)) {
                // Add the current word to the current chunk
                if (empty($currentChunk)) {
                    $currentChunk = $word;
                } else {
                    $currentChunk .= $separator.$word;
                }
            } else {
                // Add the current chunk to the chunks array
                $chunks[] = trim($currentChunk);
                if ($overlapping > 0) {
                    // If overlapping is enabled, update the current chunk by removing the first few words
                    $lastWords = explode($separator, $currentChunk);
                    $currentChunk = implode($separator, array_slice($lastWords, -$overlapping));
                } else {
                    // If overlapping is disabled, set the current chunk to the current word
                    $currentChunk = $word;
                }
            }
        }

        // Add the last chunk to the chunks array
        if (! empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    /**
     * @param  Document[]  $documents
     * @return Document[]
     */
    public static function splitDocuments(array $documents, int $maxLength = 1000, string $separator = '.'): array
    {
        $splittedDocuments = [];
        foreach ($documents as $document) {
            $splittedDocuments = array_merge($splittedDocuments, DocumentSplitter::splitDocument($document, $maxLength, $separator));
        }

        return $splittedDocuments;
    }
}