<?php

namespace LuthfiSolahudin\Sqlgen\Parser;

use LuthfiSolahudin\Sqlgen\Parser\Block\BlockInterface;

interface ParserInterface
{
    /**
     * @return array<BlockInterface>
     */
    public function parse(string $content): array;
}
