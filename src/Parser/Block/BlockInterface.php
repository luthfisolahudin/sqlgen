<?php

namespace LuthfiSolahudin\Sqlgen\Parser\Block;

interface BlockInterface extends \JsonSerializable
{
    public function type(): string;
}
