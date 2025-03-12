<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumMessage extends Model
{
    protected $fillable = ['course_forum_id', 'user_id', 'message'];

    public function forum()
    {
        return $this->belongsTo(CourseForum::class, 'course_forum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
