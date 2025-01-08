<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
        ]);

        $response = Http::post('http://localhost:11434/api/generate', [
            'model' => 'llama3.2',
            'prompt' => $validated['prompt'],
            'stream' => false,
            'format' => [
                'temperature' => 0.7,
                'max_tokens' => 100,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
                'stop_sequence' => ['\n'],
            ],

        ]);

        dd(
            $response->json()
        );

        return response()->json($response->json());
    }
}
