<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeExample extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'code',
        'language',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
