<?php

namespace App\Models;

use App\Models\Embedding;
use Smalot\PdfParser\Parser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'path', 
        'mime', 
        'size'
    ];

    public function getHumanReadableSizeAttribute(): string
    {
        $i = floor(log($this->size) / log(1024));
        return number_format(($this->size / pow(1024, $i)), 2) . ' ' . ['B', 'kB', 'MB', 'GB', 'TB'][$i] ;
    }

    public function getContentFromFile(): string|false
    {
        if (strtolower(pathinfo($this->path, PATHINFO_EXTENSION)) === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseFile(storage_path('app/' . $this->path));

            return $pdf->getText();
        }

        // if (strtolower(pathinfo($this->path, PATHINFO_EXTENSION)) === 'docx') {
        //     $phpWord = IOFactory::load($this->path);
        //     $fullText = '';
        //     foreach ($phpWord->getSections() as $section) {
        //         $fullText .= $this->extractTextFromDocxNode($section);
        //     }

        //     return $fullText;
        // }

        return file_get_contents($this->path);
    }

    public function embeddings(){
        return $this->hasMany(Embedding::class);
    }
}
