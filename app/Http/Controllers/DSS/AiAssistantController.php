<?php

declare(strict_types=1);

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Services\DSS\DssAiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function __construct(
        protected DssAiAssistantService $aiService
    ) {}

    /**
     * Display the full-screen Dedicated DSS AI Assistant Studio.
     */
    public function index()
    {
        $context = $this->aiService->getLiveInstitutionalContext();

        return view('dss.ai.index', compact('context'));
    }

    /**
     * Handle chat messages from the In-System DSS AI Assistant.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,model,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ]);

        $response = $this->aiService->respond(
            $validated['message'],
            $validated['history'] ?? []
        );

        return response()->json($response);
    }

    /**
     * Fetch quick institutional context / summary for the widget header.
     */
    public function summary(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->aiService->getLiveInstitutionalContext(),
        ]);
    }
}
