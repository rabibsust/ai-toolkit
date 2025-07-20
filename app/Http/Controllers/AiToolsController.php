<?php

namespace App\Http\Controllers;

use App\Models\CodeAnalysis;
use Illuminate\Http\Request;
use App\Services\LLMProviderFactory;
use Inertia\Inertia;

class AiToolsController extends Controller
{
    public function __construct(private LLMProviderFactory $providerFactory)
    {
    }

    public function dashboard()
    {
        $recentAnalyses = CodeAnalysis::latest()
            ->take(5)
            ->get(['id', 'score', 'file_name', 'created_at']);

        $availableProviders = $this->providerFactory->getAvailableProviders();

        return inertia('AiTools/Dashboard', [
            'recentAnalyses' => $recentAnalyses,
            'availableProviders' => $availableProviders
        ]);
    }

    public function analyzeCode(Request $request)
    {
        $availableProviders = implode(',', $this->providerFactory->getRegisteredProviders());

        $request->validate([
            'code' => 'required|string|min:10',
            'provider' => "sometimes|string|in:{$availableProviders}",
            'model' => 'sometimes|string',
            'options' => 'sometimes|array'
        ]);

        $providerName = $request->input('provider');
        if (!$providerName || !$this->providerFactory->hasProvider($providerName)) {
            $registered = $this->providerFactory->getRegisteredProviders();
            $providerName = $registered[0] ?? config('ai.default_provider');
        }

        try {
            $provider = $this->providerFactory->create($providerName);

            // Include model in options
            $options = $request->input('options', []);
            if ($request->has('model')) {
                $options['model'] = $request->input('model');
            }

            $analysis = $provider->analyzeCode($request->code, $options);

            return response()->json($analysis);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Provider error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveAnalysis(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'analysis' => 'required|string',
            'suggestions' => 'sometimes|array',  // Changed to 'sometimes' instead of 'required'
            'score' => 'required|integer|min:1|max:10',
            'file_name' => 'nullable|string|max:255',
            'provider' => 'sometimes|string'
        ]);

        $codeAnalysis = CodeAnalysis::create([
            'code' => $request->code,
            'analysis' => $request->analysis,
            'suggestions' => $request->suggestions ?? [],
            'score' => $request->score,
            'file_name' => $request->file_name ?? 'Untitled Analysis',
            'provider' => $request->provider ?? 'gemini', // Add provider tracking
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
            ->get(['id', 'file_name', 'score', 'provider', 'created_at']);

        return inertia('AiTools/History', [
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

     public function getProviders()
    {
        $providers = $this->providerFactory->getAvailableProviders();

        return response()->json([
            'status' => 'success',
            'providers' => $providers
        ]);
    }
}
