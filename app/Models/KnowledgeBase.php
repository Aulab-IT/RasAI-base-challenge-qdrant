<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'content', 
        'document_id', 
        'embedding'
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }
}
