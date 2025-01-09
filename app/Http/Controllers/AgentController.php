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

    /*
    |--------------------------------------------------------------------------
    | Agent Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management of agents. It is responsible for
    | listing, creating, updating, and deleting agents. The controller also
    | provides methods for assigning and removing agents from teams.
    */

    /*
        management means the ability to define ai agents,
        their main purpose is to generate text based on a prompt
    */

    /**
     * Execute a generic workflow based on the request payload.
     */
    public function execute(Request $request)
    {
        $validated = $request->validate([
            'workflow' => 'required|string',
            'input' => 'required|string',
            'options' => 'array',
        ]);

        $workflow = strtolower($validated['workflow']);
        $input = $validated['input'];
        $options = $validated['options'] ?? [];

        switch ($workflow) {
            case 'prompt-chain':
                return $this->executePromptChain($request);

            case 'orchestrator':
                return $this->executeOrchestrator($request);

            default:
                return response()->json([
                    'error' => "Workflow '{$workflow}' is not supported.",
                ], 400);
        }
    }

    /**
     * Execute the PromptChain workflow.
     */
    public function executePromptChain(Request $request)
    {
        $validated = $request->validate([
            'input' => 'required|string',
            'steps' => 'required|array',
            'options' => 'array',
        ]);

        $input = $validated['input'];
        $steps = $validated['steps']; // Array of steps
        $options = $validated['options'] ?? [];

        $promptChain = new PromptChain($this->llmClient);

        // Dynamically add steps from the request payload
        foreach ($steps as $step) {
            if (! isset($step['prompt'])) {
                return response()->json([
                    'error' => "Each step must include a 'prompt' key.",
                ], 400);
            }

            $prompt = $step['prompt'];

            $promptChain->addStep(function ($client, $input) use ($prompt, $options) {
                $preparedPrompt = str_replace('{input}', $input, $prompt);

                return $client->call($preparedPrompt, $options);
            });
        }

        $result = $promptChain->execute($input);

        // Optionally log the task or save it to the database
        $task = Task::create([
            'input' => $input,
            'output' => $result,
            'status' => 'completed',
            'pattern' => 'prompt-chain',
        ]);

        return response()->json([
            'task_id' => $task->id,
            'result' => $result,
        ]);
    }

    /**
     * Execute the Orchestrator workflow.
     */
    public function executeOrchestrator(Request $request)
    {
        $validated = $request->validate([
            'input' => 'required|string',
            'options' => 'array',
        ]);

        $input = $validated['input'];
        $options = $validated['options'] ?? [];

        $orchestrator = new Orchestrator($this->llmClient);

        $result = $orchestrator->execute($input, $options);

        // Log or save the task
        $task = Task::create([
            'input' => $input,
            'output' => $result,
            'status' => 'completed',
            'pattern' => 'orchestrator',
        ]);

        return response()->json([
            'task_id' => $task->id,
            'result' => $result,
        ]);
    }
}
