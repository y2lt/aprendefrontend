<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExamAttempt extends Model
{
    protected $fillable = [
        'exam_id', 'user_id', 'score', 'passed', 'answers',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
