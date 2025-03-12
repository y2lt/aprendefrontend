<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CodeExample;
use Illuminate\Http\Request;

class CodeExampleController extends Controller
{
    /**
     * List code examples for a given lesson.
     *
     * @param  int  $lessonId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($lessonId)
    {
        $codeExamples = CodeExample::where('lesson_id', $lessonId)->get();

        return response()->json($codeExamples);
    }

    /**
     * Show a specific code example.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $codeExample = CodeExample::findOrFail($id);

        return response()->json($codeExample);
    }

    /**
     * Create a new code example.
     *
     * Only instructors should be able to create code examples.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string',
            'language' => 'required|string',
        ]);

        $codeExample = CodeExample::create($data);

        return response()->json($codeExample, 201);
    }

    /**
     * Update an existing code example.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $codeExample = CodeExample::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'code' => 'sometimes|required|string',
            'language' => 'sometimes|required|string',
        ]);

        $codeExample->update($data);

        return response()->json($codeExample);
    }

    /**
     * Simulate code execution.
     *
     * This endpoint accepts an optional code override (if the user edits the code)
     * and returns a simulated output.
     *
     * @param  int  $id  The code example ID.
     * @return \Illuminate\Http\JsonResponse
     */
    public function execute(Request $request, $id)
    {
        $codeExample = CodeExample::findOrFail($id);
        // Use the provided code if the user has modified it; otherwise, use the stored code.
        $inputCode = $request->input('code', $codeExample->code);

        // Simulate execution based on language and code content.
        $output = $this->simulateCodeExecution($inputCode, $codeExample->language);

        return response()->json([
            'output' => $output,
        ]);
    }

    /**
     * A simple simulation of code execution.
     *
     * For example, if the code contains "console.log", we return a fixed output.
     * In a production environment, integrate with a sandbox service.
     */
    protected function simulateCodeExecution(string $code, string $language): string
    {
        if ($language === 'javascript' && strpos($code, 'console.log') !== false) {
            return 'Output: Hello, world!';
        }

        return 'Code executed successfully.';
    }
}
