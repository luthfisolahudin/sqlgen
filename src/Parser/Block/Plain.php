<?php

namespace LuthfiSolahudin\Sqlgen\Parser\Block;

class Plain implements BlockInterface
{
    protected array $values;

    protected bool $prependBlankLine = false;

    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    public function type(): string
    {
        return 'plain';
    }

    public function values(): array
    {
        return $this->values;
    }

    /**
     * @param string|array<string> $line
     * @return $this
     */
    public function append($line): self
    {
        if (is_array($line)) {
            array_push($this->values, ...$line);
        } else {
            $this->values[] = $line;
        }

        return $this;
    }

    /**
     * @param string|array<string> $line
     * @return $this
     */
    public function appendIfNeeded($line): self
    {
        if (empty($line)) {
            $this->prependBlankLine = ! empty($this->values);

            return $this;
        }

        if ($this->prependBlankLine) {
            $this->append('');
            $this->prependBlankLine = false;
        }

        return $this->append($line);
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type(),
            'values' => $this->values(),
        ];
    }
}
