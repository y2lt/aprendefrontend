<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    // Retrieve a lesson
    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);

        return response()->json($lesson);
    }

    // Create a new lesson (instructor only, typically part of a course)
    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
        ]);

        $lesson = Lesson::create($data);

        return response()->json($lesson, 201);
    }
}
