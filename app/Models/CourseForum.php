<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseForum extends Model
{
    protected $fillable = ['course_id', 'title'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function messages()
    {
        return $this->hasMany(ForumMessage::class);
    }
}
