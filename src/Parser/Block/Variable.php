<?php

namespace LuthfiSolahudin\Sqlgen\Parser\Block;

class Variable implements BlockInterface
{
    protected string $key;
    protected string $value;

    public function __construct(string $key, string $value = '')
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function type(): string
    {
        return 'var';
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }
}
