<?php

declare(strict_types=1);

namespace Phpml\Tokenization;

interface Tokenizer
{
    /**
     * @param string $text
     *
     * @return array
     */
    public function tokenize(string $text): array;
}
