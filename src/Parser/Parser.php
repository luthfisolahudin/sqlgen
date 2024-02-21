<?php

namespace LuthfiSolahudin\Sqlgen\Parser;

use LuthfiSolahudin\Sqlgen\Parser\Block\BlockInterface;
use LuthfiSolahudin\Sqlgen\Parser\Block\Plain;
use LuthfiSolahudin\Sqlgen\Parser\Block\Table;
use LuthfiSolahudin\Sqlgen\Parser\Block\Variable;

class Parser implements ParserInterface
{
    /**
     * @var int
     */
    public const MAX_COMMENT_DISTANCE = 1;

    protected Table $root;

    /**
     * @var array<string, Table>
     */
    protected array $tables = [];

    /**
     * @var array<BlockInterface>
     */
    protected array $blocks = [];

    protected Table $currentTable;

    protected int $currentCommentDistance = 0;

    /**
     * @var array<string>
     */
    protected array $pendingComments = [];

    protected bool $prependBlankLineComment = false;

    protected string $originalLine;

    protected int $lineNumber = 0;

    public function __construct()
    {
        $this->setupRootTable();
    }

    protected function setupRootTable()
    {
        $this->root = new Table('root', true);
        $this->tables['root'] = $this->root;
        $this->blocks[] = $this->root;
        $this->currentTable = $this->root;
    }

    /**
     * @return array<BlockInterface>
     */
    public function parse(string $content): array
    {
        foreach (preg_split('/\R/', $content) as $i => $line) {
            $this->originalLine = $line;
            $this->lineNumber = $i + 1;

            $matches = [];
            $isSqlComment = preg_match('/^\h*(#|--)\h*(?<line>.*?)\h*\R$/', $line, $matches);

            if ($isSqlComment) {
                $this->processComment($matches['line']);
            } else {
                $this->processPlain(preg_replace('/\R$/', '', $line));
            }

            if ($this->currentCommentDistance > static::MAX_COMMENT_DISTANCE) {
                $this->setCommentToCurrentTable();
            }
        }

        return $this->blocks;
    }

    protected function processComment(string $line)
    {
        if ($this->currentTable->acceptPlain()) {
            $this->currentTable = $this->root;
        }

        if (empty($line)) {
            $this->currentCommentDistance += 1;
            $this->appendPendingCommentIfNeeded($line);

            return;
        }

        $this->currentCommentDistance = 0;

        $matches = [];
        $isTable = preg_match('/^\[(?<namespace>[[:alpha:]]+(?>[.-][[:alnum:]]+)*)](?<plain>>?)$/', $line, $matches);

        if ($isTable) {
            $namespace = $matches['namespace'];

            if (! isset($this->tables[$namespace])) {
                $this->tables[$namespace] = new Table($namespace, ! empty($matches['plain']));
                $this->blocks[] = $this->tables[$namespace];
            }

            $this->currentTable = $this->tables[$namespace];
            $this->setCommentToCurrentTable();

            return;
        }

        $matches = [];
        $isVar = preg_match('/^(?<key>[a-z]+): ?(?<value>.*)$/', $line, $matches);

        if ($isVar) {
            $this->currentTable->appendChildren(new Variable($matches['key'], $matches['value']));

            return;
        }

        $this->appendPendingCommentIfNeeded($line);
    }

    protected function processPlain(string $line)
    {
        if (! $this->currentTable->acceptPlain()) {
            $this->currentTable = $this->root;
        }

        if (empty($line)) {
            $this->currentCommentDistance += 2;
            $this->appendPendingCommentIfNeeded($line);

            return;
        }

        $this->currentCommentDistance = 0;

        if (($last = $this->currentTable->lastChild()) instanceof Plain) {
            /** @var Plain $last */
            $last->appendIfNeeded($line);
        } else {
            $this->currentTable->appendChildren(new Plain([$line]));
        }
    }

    protected function appendPendingCommentIfNeeded(string $line)
    {
        if (empty($line)) {
            $this->prependBlankLineComment = ! empty($this->pendingComments);

            return;
        }

        if ($this->prependBlankLineComment) {
            $this->pendingComments[] = '';
            $this->prependBlankLineComment = false;
        }

        $this->pendingComments[] = $line;
    }

    protected function setCommentToCurrentTable()
    {
        if (! empty($this->pendingComments)) {
            $prependBlankLine = $this->prependBlankLineComment && ! $this->currentTable->isCommentEmpty();
            $this->currentTable->appendComment($this->pendingComments, $prependBlankLine);
            $this->pendingComments = [];
        }

        $this->prependBlankLineComment = false;
    }
}
