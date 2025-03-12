<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    // Enroll the authenticated user in a course
    public function enroll(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = Auth::user();

        // Prevent duplicate enrollments by checking first.
        if ($user->enrollments()->where('course_id', $data['course_id'])->exists()) {
            return response()->json(['message' => 'Already enrolled in this course.'], 409);
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $data['course_id'],
        ]);

        return response()->json(['message' => 'Enrolled successfully.', 'enrollment' => $enrollment], 201);
    }

    // Optionally, list enrolled courses for the user
    public function myCourses()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course')->get();

        return response()->json($enrollments);
    }
}
