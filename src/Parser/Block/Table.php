<?php

namespace LuthfiSolahudin\Sqlgen\Parser\Block;

class Table implements BlockInterface
{
    protected string $namespace;
    protected bool $isAcceptPlain;

    /**
     * @var array<string>
     */
    protected array $comments;

    /**
     * @var array<BlockInterface>
     */
    protected array $children;

    public function __construct(
        string $namespace,
        bool   $isAcceptPlain = false,
        array  $comments = [],
        array  $children = []
    ) {
        $this->namespace = $namespace;
        $this->isAcceptPlain = $isAcceptPlain;
        $this->comments = $comments;
        $this->children = $children;
    }

    public function type(): string
    {
        return 'table';
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function acceptPlain(): bool
    {
        return $this->isAcceptPlain;
    }

    public function comments(): array
    {
        return $this->comments;
    }

    /**
     * @param string|array<string> $comment
     * @return $this
     */
    public function appendComment($comment, bool $prependBlankLine = false): self
    {
        if ($prependBlankLine) {
            $this->comments[] = '';
        }

        if (is_array($comment)) {
            array_push($this->comments, ...$comment);
        } else {
            $this->comments[] = $comment;
        }

        return $this;
    }

    public function isCommentEmpty(): bool
    {
        return empty($this->comments);
    }

    public function children(): array
    {
        return $this->children;
    }

    public function lastChild(): ?BlockInterface
    {
        if (empty($this->children)) {
            return null;
        }

        return end($this->children);
    }

    /**
     * @param BlockInterface|array<BlockInterface> $child
     * @return $this
     */
    public function appendChildren($child): self
    {
        if (is_array($child)) {
            array_push($this->children, ...$child);
        } else {
            $this->children[] = $child;
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type(),
            'namespace' => $this->namespace(),
            'accept_plain' => $this->acceptPlain(),
            'comments' => $this->comments(),
            'children' => $this->children(),
        ];
    }
}
