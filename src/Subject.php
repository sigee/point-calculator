<?php

namespace PointCalculator;

class Subject
{
    private string $name;
    private string $type;
    private string $result;

    public function __construct(string $name, string $type, string $result)
    {
        $this->name = $name;
        $this->type = $type;
        $this->result = $result;
    }

    public function getResultAsNumber(): int
    {
        return (int)substr($this->result, 0, -1);
    }

    public function getName(): string
    {
        return strip_tags($this->name);
    }

    public function getType(): string
    {
        return $this->type === 'emelt' ? 'emelt' : 'közép';
    }
}