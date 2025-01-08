<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OllamaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_generate_method()
    {
        // Mock the HTTP response
        Http::fake([
            'http://localhost:11434/api/generate' => Http::response(['result' => 'test response'], 200),
        ]);

        // Make a POST request to the generate endpoint
        $response = $this->postJson('/api/generate', ['prompt' => 'test prompt']);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'result' => 'test response',
            ]);
    }

    public function test_generate_returns_expected_response()
    {
        // Arrange
        $prompt = 'Hello, world!';
        $expectedResponse = ['data' => 'Generated response'];

        Http::fake([
            'http://localhost:11434/api/generate' => Http::response($expectedResponse, 200),
        ]);

        // Act
        $response = $this->postJson('/api/generate', ['prompt' => $prompt]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson($expectedResponse);
    }
}
