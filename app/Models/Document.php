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

    // TODO>> Implement the getContentFromFile method
    
    public function getContentFromFile(): string
    {
        if($this->mime == 'application/pdf'){            
            $parser = new Parser();
            $pdf = $parser->parseFile(storage_path('app/' . $this->path));
    
            $text = $pdf->getText();

            return $text;
        }

        if($this->mime == 'text/plain'){
            return file_get_contents(storage_path('app/' . $this->path));
        }

        
        return '';
    }
}
