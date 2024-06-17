<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    use HasFactory;

    protected $fillable = [
        'content', 
        'document_id', 
        'embedding'
    ];

    // TODO>> Define the relationship with the Document model
    public function document(){
        return $this->belongsTo(Document::class);
    }
}
