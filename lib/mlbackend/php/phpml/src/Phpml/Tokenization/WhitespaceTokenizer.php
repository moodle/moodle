<?php

declare(strict_types=1);

namespace Phpml\Tokenization;

use Phpml\Exception\InvalidArgumentException;

class WhitespaceTokenizer implements Tokenizer
{
    public function tokenize(string $text): array
    {
        $substrings = preg_split('/[\pZ\pC]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if ($substrings === false) {
            throw new InvalidArgumentException('preg_split failed on: '.$text);
        }

        return $substrings;
    }
}
