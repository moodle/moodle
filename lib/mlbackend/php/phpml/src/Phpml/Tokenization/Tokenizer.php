<?php

declare(strict_types=1);

namespace Phpml\Tokenization;

interface Tokenizer
{
    public function tokenize(string $text): array;
}
