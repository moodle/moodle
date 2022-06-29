<?php

declare(strict_types=1);

namespace Phpml\Tokenization;

use Phpml\Exception\InvalidArgumentException;

class NGramWordTokenizer extends WordTokenizer
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
        preg_match_all('/\w\w+/u', $text, $words);

        $words = $words[0];

        $nGrams = [];
        for ($j = $this->minGram; $j <= $this->maxGram; $j++) {
            $nGrams = array_merge($nGrams, $this->getNgrams($words, $j));
        }

        return $nGrams;
    }

    private function getNgrams(array $match, int $n = 2): array
    {
        $ngrams = [];
        $len = count($match);
        for ($i = 0; $i < $len; $i++) {
            if ($i > ($n - 2)) {
                $ng = '';
                for ($j = $n - 1; $j >= 0; $j--) {
                    $ng .= ' '.$match[$i - $j];
                }
                $ngrams[] = trim($ng);
            }
        }

        return $ngrams;
    }
}
