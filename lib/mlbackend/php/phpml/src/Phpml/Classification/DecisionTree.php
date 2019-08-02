<?php

declare(strict_types=1);

namespace Phpml\Classification;

use Phpml\Classification\DecisionTree\DecisionTreeLeaf;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Helper\Predictable;
use Phpml\Helper\Trainable;
use Phpml\Math\Statistic\Mean;

class DecisionTree implements Classifier
{
    use Trainable;
    use Predictable;

    public const CONTINUOUS = 1;

    public const NOMINAL = 2;

    /**
     * @var int
     */
    public $actualDepth = 0;

    /**
     * @var array
     */
    protected $columnTypes = [];

    /**
     * @var DecisionTreeLeaf
     */
    protected $tree;

    /**
     * @var int
     */
    protected $maxDepth;

    /**
     * @var array
     */
    private $labels = [];

    /**
     * @var int
     */
    private $featureCount = 0;

    /**
     * @var int
     */
    private $numUsableFeatures = 0;

    /**
     * @var array
     */
    private $selectedFeatures = [];

    /**
     * @var array|null
     */
    private $featureImportances;

    /**
     * @var array
     */
    private $columnNames = [];

    public function __construct(int $maxDepth = 10)
    {
        $this->maxDepth = $maxDepth;
    }

    public function train(array $samples, array $targets): void
    {
        $this->samples = array_merge($this->samples, $samples);
        $this->targets = array_merge($this->targets, $targets);

        $this->featureCount = count($this->samples[0]);
        $this->columnTypes = self::getColumnTypes($this->samples);
        $this->labels = array_keys(array_count_values($this->targets));
        $this->tree = $this->getSplitLeaf(range(0, count($this->samples) - 1));

        // Each time the tree is trained, feature importances are reset so that
        // we will have to compute it again depending on the new data
        $this->featureImportances = null;

        // If column names are given or computed before, then there is no
        // need to init it and accidentally remove the previous given names
        if ($this->columnNames === []) {
            $this->columnNames = range(0, $this->featureCount - 1);
        } elseif (count($this->columnNames) > $this->featureCount) {
            $this->columnNames = array_slice($this->columnNames, 0, $this->featureCount);
        } elseif (count($this->columnNames) < $this->featureCount) {
            $this->columnNames = array_merge(
                $this->columnNames,
                range(count($this->columnNames), $this->featureCount - 1)
            );
        }
    }

    public static function getColumnTypes(array $samples): array
    {
        $types = [];
        $featureCount = count($samples[0]);
        for ($i = 0; $i < $featureCount; ++$i) {
            $values = array_column($samples, $i);
            $isCategorical = self::isCategoricalColumn($values);
            $types[] = $isCategorical ? self::NOMINAL : self::CONTINUOUS;
        }

        return $types;
    }

    /**
     * @param mixed $baseValue
     */
    public function getGiniIndex($baseValue, array $colValues, array $targets): float
    {
        $countMatrix = [];
        foreach ($this->labels as $label) {
            $countMatrix[$label] = [0, 0];
        }

        foreach ($colValues as $index => $value) {
            $label = $targets[$index];
            $rowIndex = $value === $baseValue ? 0 : 1;
            ++$countMatrix[$label][$rowIndex];
        }

        $giniParts = [0, 0];
        for ($i = 0; $i <= 1; ++$i) {
            $part = 0;
            $sum = array_sum(array_column($countMatrix, $i));
            if ($sum > 0) {
                foreach ($this->labels as $label) {
                    $part += ($countMatrix[$label][$i] / (float) $sum) ** 2;
                }
            }

            $giniParts[$i] = (1 - $part) * $sum;
        }

        return array_sum($giniParts) / count($colValues);
    }

    /**
     * This method is used to set number of columns to be used
     * when deciding a split at an internal node of the tree.  <br>
     * If the value is given 0, then all features are used (default behaviour),
     * otherwise the given value will be used as a maximum for number of columns
     * randomly selected for each split operation.
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setNumFeatures(int $numFeatures)
    {
        if ($numFeatures < 0) {
            throw new InvalidArgumentException('Selected column count should be greater or equal to zero');
        }

        $this->numUsableFeatures = $numFeatures;

        return $this;
    }

    /**
     * A string array to represent columns. Useful when HTML output or
     * column importances are desired to be inspected.
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setColumnNames(array $names)
    {
        if ($this->featureCount !== 0 && count($names) !== $this->featureCount) {
            throw new InvalidArgumentException(sprintf('Length of the given array should be equal to feature count %s', $this->featureCount));
        }

        $this->columnNames = $names;

        return $this;
    }

    public function getHtml(): string
    {
        return $this->tree->getHTML($this->columnNames);
    }

    /**
     * This will return an array including an importance value for
     * each column in the given dataset. The importance values are
     * normalized and their total makes 1.<br/>
     */
    public function getFeatureImportances(): array
    {
        if ($this->featureImportances !== null) {
            return $this->featureImportances;
        }

        $sampleCount = count($this->samples);
        $this->featureImportances = [];
        foreach ($this->columnNames as $column => $columnName) {
            $nodes = $this->getSplitNodesByColumn($column, $this->tree);

            $importance = 0;
            foreach ($nodes as $node) {
                $importance += $node->getNodeImpurityDecrease($sampleCount);
            }

            $this->featureImportances[$columnName] = $importance;
        }

        // Normalize & sort the importances
        $total = array_sum($this->featureImportances);
        if ($total > 0) {
            array_walk($this->featureImportances, function (&$importance) use ($total): void {
                $importance /= $total;
            });
            arsort($this->featureImportances);
        }

        return $this->featureImportances;
    }

    protected function getSplitLeaf(array $records, int $depth = 0): DecisionTreeLeaf
    {
        $split = $this->getBestSplit($records);
        $split->level = $depth;
        if ($this->actualDepth < $depth) {
            $this->actualDepth = $depth;
        }

        // Traverse all records to see if all records belong to the same class,
        // otherwise group the records so that we can classify the leaf
        // in case maximum depth is reached
        $leftRecords = [];
        $rightRecords = [];
        $remainingTargets = [];
        $prevRecord = null;
        $allSame = true;

        foreach ($records as $recordNo) {
            // Check if the previous record is the same with the current one
            $record = $this->samples[$recordNo];
            if ($prevRecord !== null && $prevRecord != $record) {
                $allSame = false;
            }

            $prevRecord = $record;

            // According to the split criteron, this record will
            // belong to either left or the right side in the next split
            if ($split->evaluate($record)) {
                $leftRecords[] = $recordNo;
            } else {
                $rightRecords[] = $recordNo;
            }

            // Group remaining targets
            $target = $this->targets[$recordNo];
            if (!array_key_exists($target, $remainingTargets)) {
                $remainingTargets[$target] = 1;
            } else {
                ++$remainingTargets[$target];
            }
        }

        if ($allSame || $depth >= $this->maxDepth || count($remainingTargets) === 1) {
            $split->isTerminal = true;
            arsort($remainingTargets);
            $split->classValue = (string) key($remainingTargets);
        } else {
            if (isset($leftRecords[0])) {
                $split->leftLeaf = $this->getSplitLeaf($leftRecords, $depth + 1);
            }

            if (isset($rightRecords[0])) {
                $split->rightLeaf = $this->getSplitLeaf($rightRecords, $depth + 1);
            }
        }

        return $split;
    }

    protected function getBestSplit(array $records): DecisionTreeLeaf
    {
        $targets = array_intersect_key($this->targets, array_flip($records));
        $samples = (array) array_combine(
            $records,
            $this->preprocess(array_intersect_key($this->samples, array_flip($records)))
        );
        $bestGiniVal = 1;
        $bestSplit = null;
        $features = $this->getSelectedFeatures();
        foreach ($features as $i) {
            $colValues = [];
            foreach ($samples as $index => $row) {
                $colValues[$index] = $row[$i];
            }

            $counts = array_count_values($colValues);
            arsort($counts);
            $baseValue = key($counts);
            if ($baseValue === null) {
                continue;
            }

            $gini = $this->getGiniIndex($baseValue, $colValues, $targets);
            if ($bestSplit === null || $bestGiniVal > $gini) {
                $split = new DecisionTreeLeaf();
                $split->value = $baseValue;
                $split->giniIndex = $gini;
                $split->columnIndex = $i;
                $split->isContinuous = $this->columnTypes[$i] === self::CONTINUOUS;
                $split->records = $records;

                // If a numeric column is to be selected, then
                // the original numeric value and the selected operator
                // will also be saved into the leaf for future access
                if ($this->columnTypes[$i] === self::CONTINUOUS) {
                    $matches = [];
                    preg_match("/^([<>=]{1,2})\s*(.*)/", (string) $split->value, $matches);
                    $split->operator = $matches[1];
                    $split->numericValue = (float) $matches[2];
                }

                $bestSplit = $split;
                $bestGiniVal = $gini;
            }
        }

        return $bestSplit;
    }

    /**
     * Returns available features/columns to the tree for the decision making
     * process. <br>
     *
     * If a number is given with setNumFeatures() method, then a random selection
     * of features up to this number is returned. <br>
     *
     * If some features are manually selected by use of setSelectedFeatures(),
     * then only these features are returned <br>
     *
     * If any of above methods were not called beforehand, then all features
     * are returned by default.
     */
    protected function getSelectedFeatures(): array
    {
        $allFeatures = range(0, $this->featureCount - 1);
        if ($this->numUsableFeatures === 0 && count($this->selectedFeatures) === 0) {
            return $allFeatures;
        }

        if (count($this->selectedFeatures) > 0) {
            return $this->selectedFeatures;
        }

        $numFeatures = $this->numUsableFeatures;
        if ($numFeatures > $this->featureCount) {
            $numFeatures = $this->featureCount;
        }

        shuffle($allFeatures);
        $selectedFeatures = array_slice($allFeatures, 0, $numFeatures);
        sort($selectedFeatures);

        return $selectedFeatures;
    }

    protected function preprocess(array $samples): array
    {
        // Detect and convert continuous data column values into
        // discrete values by using the median as a threshold value
        $columns = [];
        for ($i = 0; $i < $this->featureCount; ++$i) {
            $values = array_column($samples, $i);
            if ($this->columnTypes[$i] == self::CONTINUOUS) {
                $median = Mean::median($values);
                foreach ($values as &$value) {
                    if ($value <= $median) {
                        $value = "<= ${median}";
                    } else {
                        $value = "> ${median}";
                    }
                }
            }

            $columns[] = $values;
        }

        // Below method is a strange yet very simple & efficient method
        // to get the transpose of a 2D array
        return array_map(null, ...$columns);
    }

    protected static function isCategoricalColumn(array $columnValues): bool
    {
        $count = count($columnValues);

        // There are two main indicators that *may* show whether a
        // column is composed of discrete set of values:
        // 1- Column may contain string values and non-float values
        // 2- Number of unique values in the column is only a small fraction of
        //	  all values in that column (Lower than or equal to %20 of all values)
        $numericValues = array_filter($columnValues, 'is_numeric');
        $floatValues = array_filter($columnValues, 'is_float');
        if (count($floatValues) > 0) {
            return false;
        }

        if (count($numericValues) !== $count) {
            return true;
        }

        $distinctValues = array_count_values($columnValues);

        return count($distinctValues) <= $count / 5;
    }

    /**
     * Used to set predefined features to consider while deciding which column to use for a split
     */
    protected function setSelectedFeatures(array $selectedFeatures): void
    {
        $this->selectedFeatures = $selectedFeatures;
    }

    /**
     * Collects and returns an array of internal nodes that use the given
     * column as a split criterion
     */
    protected function getSplitNodesByColumn(int $column, DecisionTreeLeaf $node): array
    {
        if ($node->isTerminal) {
            return [];
        }

        $nodes = [];
        if ($node->columnIndex === $column) {
            $nodes[] = $node;
        }

        $lNodes = [];
        $rNodes = [];
        if ($node->leftLeaf !== null) {
            $lNodes = $this->getSplitNodesByColumn($column, $node->leftLeaf);
        }

        if ($node->rightLeaf !== null) {
            $rNodes = $this->getSplitNodesByColumn($column, $node->rightLeaf);
        }

        return array_merge($nodes, $lNodes, $rNodes);
    }

    /**
     * @return mixed
     */
    protected function predictSample(array $sample)
    {
        $node = $this->tree;
        do {
            if ($node->isTerminal) {
                return $node->classValue;
            }

            if ($node->evaluate($sample)) {
                $node = $node->leftLeaf;
            } else {
                $node = $node->rightLeaf;
            }
        } while ($node);

        return $this->labels[0];
    }
}
