<?php

declare(strict_types=1);

namespace Phpml\Tokenization;

class WhitespaceTokenizer implements Tokenizer
{
    /**
     * @param string $text
     *
     * @return array
     */
    public function tokenize(string $text): array
    {
        return preg_split('/[\pZ\pC]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
}
