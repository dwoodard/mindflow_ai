<?php

namespace Tests\Feature;

use Tests\TestCase;

class OllamaRoutesTest extends TestCase
{
    // Test for OllamaController generate route
    public function test_generate_route()
    {
        $response = $this->post('/api/generate');
        $response->assertStatus(302);
        $response = $this->followRedirects($response);
        $response->assertStatus(200);
    }
}
