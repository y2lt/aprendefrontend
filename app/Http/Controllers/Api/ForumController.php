<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseForum;
use App\Models\ForumMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Display all forum messages for a course.
     */
    public function index($courseId)
    {
        // Retrieve the course forum for the given course.
        $forum = CourseForum::where('course_id', $courseId)->first();

        if (! $forum) {
            return response()->json(['error' => 'Forum not found for this course.'], 404);
        }

        $messages = $forum->messages()->with('user')->orderBy('created_at')->get();

        return response()->json($messages);
    }

    /**
     * Post a new message in the course forum.
     */
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();

        // Check if the user is enrolled in the course.
        if (! $user->enrollments()->where('course_id', $courseId)->exists()) {
            return response()->json(['error' => 'You are not enrolled in this course.'], 403);
        }

        // Retrieve or create the forum channel for this course.
        $forum = CourseForum::firstOrCreate(
            ['course_id' => $courseId],
            ['title' => 'General Discussion']
        );

        $forumMessage = ForumMessage::create([
            'course_forum_id' => $forum->id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        return response()->json($forumMessage, 201);
    }
}
