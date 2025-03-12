<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::get('/lessons/{id}', [LessonController::class, 'show']);

// Public routes for viewing code examples
Route::get('/lessons/{lessonId}/code-examples', [CodeExampleController::class, 'index']);
Route::get('/code-examples/{id}', [CodeExampleController::class, 'show']);

// Routes for instructors (assume protected by middleware checking role)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::post('/lessons', [LessonController::class, 'store']);

    Route::post('/enroll', [EnrollmentController::class, 'enroll']);
    Route::get('/my-courses', [EnrollmentController::class, 'myCourses']);

    Route::get('/courses/{courseId}/forum', [ForumController::class, 'index']);
    Route::post('/courses/{courseId}/forum', [ForumController::class, 'store']);

    Route::post('/code-examples', [CodeExampleController::class, 'store']);
    Route::put('/code-examples/{id}', [CodeExampleController::class, 'update']);

    // Endpoint to simulate code execution
    Route::post('/code-examples/{id}/execute', [CodeExampleController::class, 'execute']);

    // Retrieve exam details including questions and options.
    Route::get('/exams/{examId}', [ExamController::class, 'show']);
    // Submit an exam attempt.
    Route::post('/exams/{examId}/submit', [ExamController::class, 'submit']);
});
