<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // List all courses
    public function index(Request $request)
    {
        $query = Course::with('instructor');

        // If a category filter is provided, filter courses that have that category.
        if ($request->has('category')) {
            $category = $request->input('category');
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $courses = $query->get();

        return response()->json($courses);
    }

    // Show a specific course with lessons
    public function show($id)
    {
        $course = Course::with('lessons', 'instructor')->findOrFail($id);

        return response()->json($course);
    }

    // Create a new course (instructor only)
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Assume the authenticated user is an instructor
        $data['instructor_id'] = Auth::id();

        $course = Course::create($data);

        return response()->json($course, 201);
    }

    // Update a course (instructor only)
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
        ]);
        $course->update($data);

        return response()->json($course);
    }
}
