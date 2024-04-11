<?php

namespace App\Models;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content', 
        'chat_id' , 
        'role' , 
        'audio_path' , 
        'is_image_content'
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
