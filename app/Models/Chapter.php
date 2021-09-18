<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_page',
        'end_page',
        'serial_number',
        'department'
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
