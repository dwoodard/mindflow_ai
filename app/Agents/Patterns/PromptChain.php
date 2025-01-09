<?php

namespace App\Agents\Patterns;

use App\Services\LLMClient;

class PromptChain
{
    protected $steps = [];

    protected $client;

    public function __construct(LLMClient $client)
    {
        $this->client = $client;
    }

    public function addStep(callable $step): self
    {
        $this->steps[] = $step;

        return $this;
    }

    public function execute(string $initialInput): string
    {
        $input = $initialInput;

        foreach ($this->steps as $step) {
            $input = $step($this->client, $input);
        }

        return $input;
    }
}
