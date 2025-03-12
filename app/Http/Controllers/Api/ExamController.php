<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Exam;
use App\Models\UserExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    /**
     * Retrieve exam details with questions and options.
     */
    public function show($examId)
    {
        $exam = Exam::with('questions.options')->findOrFail($examId);

        return response()->json($exam);
    }

    /**
     * Submit an exam attempt.
     *
     * Expected input format:
     * {
     *   "answers": {
     *       "question_id": "selected_option_id",
     *       ...
     *   }
     * }
     */
    public function submit(Request $request, $examId)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $exam = Exam::with('questions.options')->findOrFail($examId);
        $user = Auth::user();
        $answers = $request->input('answers');
        $totalQuestions = $exam->questions->count();
        $correctCount = 0;

        // Loop through each question to verify answers.
        foreach ($exam->questions as $question) {
            // Check if the user provided an answer for this question.
            if (isset($answers[$question->id])) {
                $selectedOptionId = $answers[$question->id];
                // Check if the selected option is correct.
                $isCorrect = $question->options->where('id', $selectedOptionId)
                    ->first() ? $question->options->where('id', $selectedOptionId)
                    ->first()->is_correct : false;
                if ($isCorrect) {
                    $correctCount++;
                }
            }
        }

        // Calculate score as a percentage.
        $score = ($totalQuestions > 0) ? intval(($correctCount / $totalQuestions) * 100) : 0;
        $passed = $score >= $exam->passing_score;

        // Save the user's exam attempt.
        $attempt = UserExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $answers,
        ]);

        $response = [
            'attempt' => $attempt,
            'score' => $score,
            'passed' => $passed,
        ];

        // If passed, create a certificate record.
        if ($passed) {
            $certificate = Certification::create([
                'exam_id' => $exam->id,
                'course_id' => $exam->course_id,
                'user_id' => $user->id,
                // Generate a certificate number or use a UUID.
                'certificate_number' => strtoupper(Str::random(10)),
                'issued_at' => now(),
            ]);
            $response['certificate'] = $certificate;
        }

        return response()->json($response);
    }
}
