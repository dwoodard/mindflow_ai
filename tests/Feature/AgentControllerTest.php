<?php

namespace Tests\Feature;

use App\Services\LLMClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AgentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $llmClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->llmClient = $this->createMock(LLMClient::class);
        $this->app->instance(LLMClient::class, $this->llmClient);
    }

    public function test_execute_orchestrator()
    {
        $this->llmClient->method('call')->willReturn('mocked response');

        $response = $this->postJson(route('agent.execute'), [
            'workflow' => 'orchestrator',
            'input' => 'test input',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'task_id',
                'result',
            ]);

        $this->assertDatabaseHas('tasks', [
            'input' => 'test input',
            'output' => 'mocked response',
            'status' => 'completed',
            'pattern' => 'orchestrator',
        ]);
    }

    public function test_execute_invalid_workflow()
    {
        $response = $this->postJson(route('agent.execute'), [
            'workflow' => 'invalid-workflow',
            'input' => 'test input',
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => "Workflow 'invalid-workflow' is not supported.",
            ]);
    }

    /** @test */
    public function it_can_execute_agent_workflow()
    {
        $response = $this->postJson(route('agent.execute'), [
            'workflow' => 'test-workflow',
            'input' => 'test input',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'task_id' => 1,
                'result' => 'Test workflow executed with input: test input',
            ]);
    }

    /** @test */
    public function it_can_execute_prompt_chain()
    {
        $response = $this->postJson(route('agent.execute.prompt-chain'), [
            'input' => 'test input',
            'steps' => [
                ['prompt' => 'Step 1: {input}'],
                ['prompt' => 'Step 2: {input}'],
            ],
        ]);

        $response->assertStatus(200);
        // Add more assertions as needed
    }

    /** @test */
    public function it_can_execute_orchestrator()
    {
        $response = $this->postJson(route('agent.execute.orchestrator'), [
            'input' => 'test input',
        ]);

        $response->assertStatus(200);
        // Add more assertions as needed
    }
}
