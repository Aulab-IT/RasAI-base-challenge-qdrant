<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'path', 
        'mime', 
        'size'
    ];

    public function getHumanReadableSizeAttribute()
    {
        $i = floor(log($this->size) / log(1024));
        return number_format(($this->size / pow(1024, $i)), 2) . ' ' . ['B', 'kB', 'MB', 'GB', 'TB'][$i] ;
    }
}
