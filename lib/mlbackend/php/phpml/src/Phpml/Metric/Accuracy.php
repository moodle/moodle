<?php

declare(strict_types=1);

namespace Phpml\Metric;

use Phpml\Exception\InvalidArgumentException;

class Accuracy
{
    /**
     * @return float|int
     *
     * @throws InvalidArgumentException
     */
    public static function score(array $actualLabels, array $predictedLabels, bool $normalize = true)
    {
        if (count($actualLabels) != count($predictedLabels)) {
            throw new InvalidArgumentException('Size of given arrays does not match');
        }

        $score = 0;
        foreach ($actualLabels as $index => $label) {
            if ($label == $predictedLabels[$index]) {
                ++$score;
            }
        }

        if ($normalize) {
            $score /= count($actualLabels);
        }

        return $score;
    }
}
