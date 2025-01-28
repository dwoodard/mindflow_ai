<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OllamaController extends Controller
{
    // generate
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'model' => 'string',
            'system' => 'string',
        ]);

        $ollamaBaseUrl = env('OLLAMA_BASE_URL');





        $response = Http::post("{$ollamaBaseUrl}/api/generate", [
            'model' => $validated['model'] ?? 'llama3.2',
            'prompt' => $validated['prompt'],
            'system' => $validated['system'] ?? 'you are a helpful assistant',
            'format' => $validated['format'] ?? null,
            'stream' => false,
        ]);

        return response()->json($response->json());
    }
}
