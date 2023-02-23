<?php

declare(strict_types=1);

namespace Phpml\FeatureExtraction;

use Phpml\Tokenization\Tokenizer;
use Phpml\Transformer;

class TokenCountVectorizer implements Transformer
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     * @var StopWords|null
     */
    private $stopWords;

    /**
     * @var float
     */
    private $minDF;

    /**
     * @var array
     */
    private $vocabulary = [];

    /**
     * @var array
     */
    private $frequencies = [];

    public function __construct(Tokenizer $tokenizer, ?StopWords $stopWords = null, float $minDF = 0.0)
    {
        $this->tokenizer = $tokenizer;
        $this->stopWords = $stopWords;
        $this->minDF = $minDF;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        $this->buildVocabulary($samples);
    }

    public function transform(array &$samples, ?array &$targets = null): void
    {
        array_walk($samples, function (string &$sample): void {
            $this->transformSample($sample);
        });

        $this->checkDocumentFrequency($samples);
    }

    public function getVocabulary(): array
    {
        return array_flip($this->vocabulary);
    }

    private function buildVocabulary(array &$samples): void
    {
        foreach ($samples as $sample) {
            $tokens = $this->tokenizer->tokenize($sample);
            foreach ($tokens as $token) {
                $this->addTokenToVocabulary($token);
            }
        }
    }

    private function transformSample(string &$sample): void
    {
        $counts = [];
        $tokens = $this->tokenizer->tokenize($sample);

        foreach ($tokens as $token) {
            $index = $this->getTokenIndex($token);
            if ($index !== false) {
                $this->updateFrequency($token);
                if (!isset($counts[$index])) {
                    $counts[$index] = 0;
                }

                ++$counts[$index];
            }
        }

        foreach ($this->vocabulary as $index) {
            if (!isset($counts[$index])) {
                $counts[$index] = 0;
            }
        }

        ksort($counts);

        $sample = $counts;
    }

    /**
     * @return int|bool
     */
    private function getTokenIndex(string $token)
    {
        if ($this->isStopWord($token)) {
            return false;
        }

        return $this->vocabulary[$token] ?? false;
    }

    private function addTokenToVocabulary(string $token): void
    {
        if ($this->isStopWord($token)) {
            return;
        }

        if (!isset($this->vocabulary[$token])) {
            $this->vocabulary[$token] = count($this->vocabulary);
        }
    }

    private function isStopWord(string $token): bool
    {
        return $this->stopWords !== null && $this->stopWords->isStopWord($token);
    }

    private function updateFrequency(string $token): void
    {
        if (!isset($this->frequencies[$token])) {
            $this->frequencies[$token] = 0;
        }

        ++$this->frequencies[$token];
    }

    private function checkDocumentFrequency(array &$samples): void
    {
        if ($this->minDF > 0) {
            $beyondMinimum = $this->getBeyondMinimumIndexes(count($samples));
            foreach ($samples as &$sample) {
                $this->resetBeyondMinimum($sample, $beyondMinimum);
            }
        }
    }

    private function resetBeyondMinimum(array &$sample, array $beyondMinimum): void
    {
        foreach ($beyondMinimum as $index) {
            $sample[$index] = 0;
        }
    }

    private function getBeyondMinimumIndexes(int $samplesCount): array
    {
        $indexes = [];
        foreach ($this->frequencies as $token => $frequency) {
            if (($frequency / $samplesCount) < $this->minDF) {
                $indexes[] = $this->getTokenIndex((string) $token);
            }
        }

        return $indexes;
    }
}
