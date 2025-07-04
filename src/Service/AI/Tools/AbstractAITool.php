<?php

namespace App\Service\AI\Tools;

abstract class AbstractAITool
{
    abstract public function getName(): string;
    abstract public function getDescription(): string;
    abstract public function execute(array $parameters): array;
    
    public function getSchema(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'parameters' => $this->getParameters()
        ];
    }
    
    abstract protected function getParameters(): array;
} 