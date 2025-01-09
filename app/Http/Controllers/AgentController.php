<?php

namespace App\Http\Controllers;

use App\Agents\Patterns\Orchestrator;
use App\Agents\Patterns\PromptChain;
use App\Models\Task;
use App\Services\LLMClient;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    protected $llmClient;

    public function __construct(LLMClient $llmClient)
    {
        $this->llmClient = $llmClient;
    }

    /**
     * Execute a generic workflow based on the request payload.
     */
    public function execute(Request $request)
    {
        $validated = $this->validateRequest($request);

        $workflow = strtolower($validated['workflow']);
        $input = $validated['input'];
        $options = $validated['options'] ?? [];

        switch ($workflow) {
            case 'prompt-chain':
                return $this->executePromptChain($input, $validated['steps'], $options);

            case 'orchestrator':
                return $this->executeOrchestrator($input, $options);

            default:
                return $this->unsupportedWorkflowResponse($workflow);
        }
    }

    /**
     * Execute the PromptChain workflow.
     */
    protected function executePromptChain(string $input, array $steps, array $options)
    {
        $promptChain = new PromptChain($this->llmClient);

        foreach ($steps as $step) {
            if (!isset($step['prompt'])) {
                return response()->json([
                    'error' => "Each step must include a 'prompt' key.",
                ], 400);
            }

            $promptChain->addStep(function ($client, $currentInput) use ($step, $options) {
                $preparedPrompt = str_replace('{input}', $currentInput, $step['prompt']);
                return $client->call($preparedPrompt, $options);
            });
        }

        $result = $promptChain->execute($input);

        $task = $this->storeTask($input, $result, 'prompt-chain');

        return response()->json([
            'task_id' => $task->id,
            'result' => $result,
        ]);
    }

    /**
     * Execute the Orchestrator workflow.
     */
    protected function executeOrchestrator(string $input, array $options)
    {
        $orchestrator = new Orchestrator($this->llmClient);
        $result = $orchestrator->execute($input, $options);

        $task = $this->storeTask($input, $result, 'orchestrator');

        return response()->json([
            'task_id' => $task->id,
            'result' => $result,
        ]);
    }

    /**
     * Centralized validation for incoming requests.
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'workflow' => 'required|string',
            'input' => 'required|string',
            'steps' => 'array|required_if:workflow,prompt-chain',
            'options' => 'array',
        ]);
    }

    /**
     * Store a task in the database.
     */
    protected function storeTask(string $input, string $output, string $pattern): Task
    {
        return Task::create([
            'input' => $input,
            'output' => $output,
            'status' => 'completed',
            'pattern' => $pattern,
        ]);
    }

    /**
     * Return a standardized response for unsupported workflows.
     */
    protected function unsupportedWorkflowResponse(string $workflow)
    {
        return response()->json([
            'error' => "Workflow '{$workflow}' is not supported.",
        ], 400);
    }
}
