<?php

namespace Tests\Feature;

use Tests\TestCase;

class AgentRoutesTest extends TestCase
{
    // Test for AgentController executePromptChain route
    public function test_agent_execute_prompt_chain_route()
    {
        $response = $this->post('/api/agent/execute/prompt-chain', [
            'input' => 'test input',
            'steps' => ['step1', 'step2'],
        ]);

        dd(
            $response->status(),
            // $response->getContent()
        );

    }

    // Test for AgentController executeOrchestrator route
    public function test_agent_execute_orchestrator_route()
    {
        $response = $this->post('/api/agent/execute/orchestrator', [
            'input' => 'test input',
            'steps' => ['step1', 'step2'],
        ]);
        $response->assertStatus(500);
    }
}
