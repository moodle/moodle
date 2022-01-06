<?php

declare(strict_types=1);

namespace Phpml\Tokenization;

use Phpml\Exception\InvalidArgumentException;

class NGramTokenizer extends WordTokenizer
{
    /**
     * @var int
     */
    private $minGram;

    /**
     * @var int
     */
    private $maxGram;

    public function __construct(int $minGram = 1, int $maxGram = 2)
    {
        if ($minGram < 1 || $maxGram < 1 || $minGram > $maxGram) {
            throw new InvalidArgumentException(sprintf('Invalid (%s, %s) minGram and maxGram value combination', $minGram, $maxGram));
        }

        $this->minGram = $minGram;
        $this->maxGram = $maxGram;
    }

    /**
     * {@inheritdoc}
     */
    public function tokenize(string $text): array
    {
        $words = [];
        preg_match_all('/\w\w+/u', $text, $words);

        $nGrams = [];
        foreach ($words[0] as $word) {
            $this->generateNGrams($word, $nGrams);
        }

        return $nGrams;
    }

    private function generateNGrams(string $word, array &$nGrams): void
    {
        $length = mb_strlen($word);

        for ($j = 1; $j <= $this->maxGram; $j++) {
            for ($k = 0; $k < $length - $j + 1; $k++) {
                if ($j >= $this->minGram) {
                    $nGrams[] = mb_substr($word, $k, $j);
                }
            }
        }
    }
}
