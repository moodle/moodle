<?php

declare(strict_types=1);

namespace Phpml\Classification\Linear;

use Phpml\Classification\DecisionTree;
use Phpml\Classification\WeightedClassifier;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Helper\OneVsRest;
use Phpml\Helper\Predictable;
use Phpml\Math\Comparison;

class DecisionStump extends WeightedClassifier
{
    use Predictable;
    use OneVsRest;

    public const AUTO_SELECT = -1;

    /**
     * @var int
     */
    protected $givenColumnIndex;

    /**
     * @var array
     */
    protected $binaryLabels = [];

    /**
     * Lowest error rate obtained while training/optimizing the model
     *
     * @var float
     */
    protected $trainingErrorRate;

    /**
     * @var int
     */
    protected $column;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var array
     */
    protected $columnTypes = [];

    /**
     * @var int
     */
    protected $featureCount;

    /**
     * @var float
     */
    protected $numSplitCount = 100.0;

    /**
     * Distribution of samples in the leaves
     *
     * @var array
     */
    protected $prob = [];

    /**
     * A DecisionStump classifier is a one-level deep DecisionTree. It is generally
     * used with ensemble algorithms as in the weak classifier role. <br>
     *
     * If columnIndex is given, then the stump tries to produce a decision node
     * on this column, otherwise in cases given the value of -1, the stump itself
     * decides which column to take for the decision (Default DecisionTree behaviour)
     */
    public function __construct(int $columnIndex = self::AUTO_SELECT)
    {
        $this->givenColumnIndex = $columnIndex;
    }

    public function __toString(): string
    {
        return "IF ${this}->column ${this}->operator ${this}->value ".
            'THEN '.$this->binaryLabels[0].' '.
            'ELSE '.$this->binaryLabels[1];
    }

    /**
     * While finding best split point for a numerical valued column,
     * DecisionStump looks for equally distanced values between minimum and maximum
     * values in the column. Given <i>$count</i> value determines how many split
     * points to be probed. The more split counts, the better performance but
     * worse processing time (Default value is 10.0)
     */
    public function setNumericalSplitCount(float $count): void
    {
        $this->numSplitCount = $count;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function trainBinary(array $samples, array $targets, array $labels): void
    {
        $this->binaryLabels = $labels;
        $this->featureCount = count($samples[0]);

        // If a column index is given, it should be among the existing columns
        if ($this->givenColumnIndex > count($samples[0]) - 1) {
            $this->givenColumnIndex = self::AUTO_SELECT;
        }

        // Check the size of the weights given.
        // If none given, then assign 1 as a weight to each sample
        if (count($this->weights) === 0) {
            $this->weights = array_fill(0, count($samples), 1);
        } else {
            $numWeights = count($this->weights);
            if ($numWeights !== count($samples)) {
                throw new InvalidArgumentException('Number of sample weights does not match with number of samples');
            }
        }

        // Determine type of each column as either "continuous" or "nominal"
        $this->columnTypes = DecisionTree::getColumnTypes($samples);

        // Try to find the best split in the columns of the dataset
        // by calculating error rate for each split point in each column
        $columns = range(0, count($samples[0]) - 1);
        if ($this->givenColumnIndex !== self::AUTO_SELECT) {
            $columns = [$this->givenColumnIndex];
        }

        $bestSplit = [
            'value' => 0,
            'operator' => '',
            'prob' => [],
            'column' => 0,
            'trainingErrorRate' => 1.0,
        ];
        foreach ($columns as $col) {
            if ($this->columnTypes[$col] == DecisionTree::CONTINUOUS) {
                $split = $this->getBestNumericalSplit($samples, $targets, $col);
            } else {
                $split = $this->getBestNominalSplit($samples, $targets, $col);
            }

            if ($split['trainingErrorRate'] < $bestSplit['trainingErrorRate']) {
                $bestSplit = $split;
            }
        }

        // Assign determined best values to the stump
        foreach ($bestSplit as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * Determines best split point for the given column
     */
    protected function getBestNumericalSplit(array $samples, array $targets, int $col): array
    {
        $values = array_column($samples, $col);
        // Trying all possible points may be accomplished in two general ways:
        // 1- Try all values in the $samples array ($values)
        // 2- Artificially split the range of values into several parts and try them
        // We choose the second one because it is faster in larger datasets
        $minValue = min($values);
        $maxValue = max($values);
        $stepSize = ($maxValue - $minValue) / $this->numSplitCount;

        $split = [];

        foreach (['<=', '>'] as $operator) {
            // Before trying all possible split points, let's first try
            // the average value for the cut point
            $threshold = array_sum($values) / (float) count($values);
            [$errorRate, $prob] = $this->calculateErrorRate($targets, $threshold, $operator, $values);
            if (!isset($split['trainingErrorRate']) || $errorRate < $split['trainingErrorRate']) {
                $split = [
                    'value' => $threshold,
                    'operator' => $operator,
                    'prob' => $prob,
                    'column' => $col,
                    'trainingErrorRate' => $errorRate,
                ];
            }

            // Try other possible points one by one
            for ($step = $minValue; $step <= $maxValue; $step += $stepSize) {
                $threshold = (float) $step;
                [$errorRate, $prob] = $this->calculateErrorRate($targets, $threshold, $operator, $values);
                if ($errorRate < $split['trainingErrorRate']) {
                    $split = [
                        'value' => $threshold,
                        'operator' => $operator,
                        'prob' => $prob,
                        'column' => $col,
                        'trainingErrorRate' => $errorRate,
                    ];
                }
            }// for
        }

        return $split;
    }

    protected function getBestNominalSplit(array $samples, array $targets, int $col): array
    {
        $values = array_column($samples, $col);
        $valueCounts = array_count_values($values);
        $distinctVals = array_keys($valueCounts);

        $split = [];

        foreach (['=', '!='] as $operator) {
            foreach ($distinctVals as $val) {
                [$errorRate, $prob] = $this->calculateErrorRate($targets, $val, $operator, $values);
                if (!isset($split['trainingErrorRate']) || $split['trainingErrorRate'] < $errorRate) {
                    $split = [
                        'value' => $val,
                        'operator' => $operator,
                        'prob' => $prob,
                        'column' => $col,
                        'trainingErrorRate' => $errorRate,
                    ];
                }
            }
        }

        return $split;
    }

    /**
     * Calculates the ratio of wrong predictions based on the new threshold
     * value given as the parameter
     */
    protected function calculateErrorRate(array $targets, float $threshold, string $operator, array $values): array
    {
        $wrong = 0.0;
        $prob = [];
        $leftLabel = $this->binaryLabels[0];
        $rightLabel = $this->binaryLabels[1];

        foreach ($values as $index => $value) {
            if (Comparison::compare($value, $threshold, $operator)) {
                $predicted = $leftLabel;
            } else {
                $predicted = $rightLabel;
            }

            $target = $targets[$index];
            if ((string) $predicted != (string) $targets[$index]) {
                $wrong += $this->weights[$index];
            }

            if (!isset($prob[$predicted][$target])) {
                $prob[$predicted][$target] = 0;
            }

            ++$prob[$predicted][$target];
        }

        // Calculate probabilities: Proportion of labels in each leaf
        $dist = array_combine($this->binaryLabels, array_fill(0, 2, 0.0));
        foreach ($prob as $leaf => $counts) {
            $leafTotal = (float) array_sum($prob[$leaf]);
            foreach ($counts as $label => $count) {
                if ((string) $leaf == (string) $label) {
                    $dist[$leaf] = $count / $leafTotal;
                }
            }
        }

        return [$wrong / (float) array_sum($this->weights), $dist];
    }

    /**
     * Returns the probability of the sample of belonging to the given label
     *
     * Probability of a sample is calculated as the proportion of the label
     * within the labels of the training samples in the decision node
     *
     * @param mixed $label
     */
    protected function predictProbability(array $sample, $label): float
    {
        $predicted = $this->predictSampleBinary($sample);
        if ((string) $predicted == (string) $label) {
            return $this->prob[$label];
        }

        return 0.0;
    }

    /**
     * @return mixed
     */
    protected function predictSampleBinary(array $sample)
    {
        if (Comparison::compare($sample[$this->column], $this->value, $this->operator)) {
            return $this->binaryLabels[0];
        }

        return $this->binaryLabels[1];
    }

    protected function resetBinary(): void
    {
    }
}
