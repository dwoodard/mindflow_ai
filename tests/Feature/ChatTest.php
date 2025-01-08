<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChatTest extends TestCase
{
    /**
     * /Chat route should return 200 status
     */
    public function test_example(): void
    {
        $response = $this->get('/chat');

        $response->assertStatus(200);
    }
}
