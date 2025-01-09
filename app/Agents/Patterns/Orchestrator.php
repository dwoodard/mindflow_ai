<?php

namespace App\Agents\Patterns;

use App\Services\LLMClient;

class Orchestrator
{
    protected $client;

    public function __construct(LLMClient $client)
    {
        $this->client = $client;
    }

    /**
     * Execute the orchestrator workflow.
     *
     * @param  string  $input  The initial input or task description.
     * @param  array  $options  Additional options for the workflow.
     * @return string The synthesized result of all subtasks.
     */
    public function execute(string $input, array $options = []): string
    {
        // Step 1: Dynamically plan subtasks based on the input
        $subtasks = $this->planSubtasks($input, $options);

        // Step 2: Execute each subtask and gather results
        $results = [];
        foreach ($subtasks as $subtask) {
            $results[] = $this->executeSubtask($subtask, $options);
        }

        // Step 3: Synthesize the results into a final output
        return $this->synthesizeResults($results, $options);
    }

    /**
     * Dynamically plan subtasks based on the input.
     *
     * @return array An array of subtasks.
     */
    protected function planSubtasks(string $input, array $options): array
    {
        // Use the LLM to generate a plan for subtasks
        $prompt = "Break down the following task into a list of subtasks:\n\nTask: {$input}\n\nSubtasks:";
        $subtasksResponse = $this->client->call($prompt, $options);

        // Assume the response is a newline-separated list of subtasks
        return array_filter(array_map('trim', explode("\n", $subtasksResponse)));
    }

    /**
     * Execute a single subtask.
     *
     * @return string The result of the subtask execution.
     */
    protected function executeSubtask(string $subtask, array $options): string
    {
        // Use the LLM to handle each subtask
        $prompt = "Complete the following subtask: {$subtask}";

        return $this->client->call($prompt, $options);
    }

    /**
     * Synthesize the results of all subtasks into a final output.
     *
     * @return string The synthesized final result.
     */
    protected function synthesizeResults(array $results, array $options): string
    {
        // Use the LLM to synthesize results into a cohesive output
        $prompt = "Combine the following results into a cohesive output:\n\n".implode("\n", $results);

        return $this->client->call($prompt, $options);
    }
}
