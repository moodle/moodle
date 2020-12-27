<?php

declare(strict_types=1);

namespace Phpml\FeatureExtraction;

use Phpml\Transformer;

class TfIdfTransformer implements Transformer
{
    /**
     * @var array
     */
    private $idf = [];

    public function __construct(array $samples = [])
    {
        if (count($samples) > 0) {
            $this->fit($samples);
        }
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        $this->countTokensFrequency($samples);

        $count = count($samples);
        foreach ($this->idf as &$value) {
            $value = log((float) ($count / $value), 10.0);
        }
    }

    public function transform(array &$samples): void
    {
        foreach ($samples as &$sample) {
            foreach ($sample as $index => &$feature) {
                $feature *= $this->idf[$index];
            }
        }
    }

    private function countTokensFrequency(array $samples): void
    {
        $this->idf = array_fill_keys(array_keys($samples[0]), 0);

        foreach ($samples as $sample) {
            foreach ($sample as $index => $count) {
                if ($count > 0) {
                    ++$this->idf[$index];
                }
            }
        }
    }
}
