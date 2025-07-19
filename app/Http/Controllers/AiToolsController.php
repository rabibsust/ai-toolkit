<?php

namespace App\Http\Controllers;

use App\Models\CodeAnalysis;
use Illuminate\Http\Request;
use App\Services\AiAnalyzerService;
use Inertia\Inertia;

class AiToolsController extends Controller
{
    public function __construct(private AiAnalyzerService $aiAnalyzer)
    {
    }

    public function dashboard()
    {
        $recentAnalyses = CodeAnalysis::latest()
            ->take(5)
            ->get(['id', 'score', 'file_name', 'created_at']);
        return inertia('AiTools/Dashboard', [
            'recentAnalyses' => $recentAnalyses
        ]);
    }

    public function analyzeCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:10'
        ]);

        $analysis = $this->aiAnalyzer->analyzeController($request->code);

        return response()->json($analysis);
    }

    public function saveAnalysis(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'analysis' => 'required|string',
            'suggestions' => 'sometimes|array',  // Changed to 'sometimes' instead of 'required'
            'score' => 'required|integer|min:1|max:10',
            'file_name' => 'nullable|string|max:255'
        ]);

        $codeAnalysis = CodeAnalysis::create([
            'code' => $request->code,
            'analysis' => $request->analysis,
            'suggestions' => $request->suggestions ?? [],  // Default to empty array
            'score' => $request->score,
            'file_name' => $request->file_name ?? 'Untitled Analysis'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Analysis saved successfully!',
            'id' => $codeAnalysis->id
        ]);
    }

    public function history()
    {
        $analyses = CodeAnalysis::latest()
            ->get(['id', 'file_name', 'score', 'created_at']);

        return Inertia::render('AiTools/History', [
            'analyses' => $analyses
        ]);
    }

    public function getAnalysis($id)
    {
        $analysis = CodeAnalysis::find($id);

        if (!$analysis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Analysis not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'analysis' => $analysis
        ]);
    }
}
