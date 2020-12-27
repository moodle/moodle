<?php

declare(strict_types=1);

namespace Phpml\FeatureSelection;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Exception\InvalidOperationException;
use Phpml\FeatureSelection\ScoringFunction\ANOVAFValue;
use Phpml\Transformer;

final class SelectKBest implements Transformer
{
    /**
     * @var ScoringFunction
     */
    private $scoringFunction;

    /**
     * @var int
     */
    private $k;

    /**
     * @var array|null
     */
    private $scores = null;

    /**
     * @var array|null
     */
    private $keepColumns = null;

    public function __construct(int $k = 10, ?ScoringFunction $scoringFunction = null)
    {
        if ($scoringFunction === null) {
            $scoringFunction = new ANOVAFValue();
        }

        $this->scoringFunction = $scoringFunction;
        $this->k = $k;
    }

    public function fit(array $samples, ?array $targets = null): void
    {
        if ($targets === null || count($targets) === 0) {
            throw new InvalidArgumentException('The array has zero elements');
        }

        $this->scores = $sorted = $this->scoringFunction->score($samples, $targets);
        if ($this->k >= count($sorted)) {
            return;
        }

        arsort($sorted);
        $this->keepColumns = array_slice($sorted, 0, $this->k, true);
    }

    public function transform(array &$samples): void
    {
        if ($this->keepColumns === null) {
            return;
        }

        foreach ($samples as &$sample) {
            $sample = array_values(array_intersect_key($sample, $this->keepColumns));
        }
    }

    public function scores(): array
    {
        if ($this->scores === null) {
            throw new InvalidOperationException('SelectKBest require to fit first to get scores');
        }

        return $this->scores;
    }
}
