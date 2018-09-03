<?php

declare(strict_types=1);

namespace Phpml\Classification;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Helper\Predictable;
use Phpml\Helper\Trainable;
use Phpml\Math\Statistic\Mean;
use Phpml\Classification\DecisionTree\DecisionTreeLeaf;

class DecisionTree implements Classifier
{
    use Trainable, Predictable;

    const CONTINUOUS = 1;
    const NOMINAL = 2;

    /**
     * @var array
     */
    protected $columnTypes;

    /**
     * @var array
     */
    private $labels = [];

    /**
     * @var int
     */
    private $featureCount = 0;

    /**
     * @var DecisionTreeLeaf
     */
    protected $tree = null;

    /**
     * @var int
     */
    protected $maxDepth;

    /**
     * @var int
     */
    public $actualDepth = 0;

    /**
     * @var int
     */
    private $numUsableFeatures = 0;

    /**
     * @var array
     */
    private $selectedFeatures;

    /**
     * @var array
     */
    private $featureImportances = null;

    /**
     *
     * @var array
     */
    private $columnNames = null;

    /**
     * @param int $maxDepth
     */
    public function __construct(int $maxDepth = 10)
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
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
        if ($this->columnNames === null) {
            $this->columnNames = range(0, $this->featureCount - 1);
        } elseif (count($this->columnNames) > $this->featureCount) {
            $this->columnNames = array_slice($this->columnNames, 0, $this->featureCount);
        } elseif (count($this->columnNames) < $this->featureCount) {
            $this->columnNames = array_merge($this->columnNames,
                range(count($this->columnNames), $this->featureCount - 1)
            );
        }
    }

    /**
     * @param array $samples
     *
     * @return array
     */
    public static function getColumnTypes(array $samples) : array
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
     * @param array $records
     * @param int   $depth
     *
     * @return DecisionTreeLeaf
     */
    protected function getSplitLeaf(array $records, int $depth = 0) : DecisionTreeLeaf
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
        $rightRecords= [];
        $remainingTargets = [];
        $prevRecord = null;
        $allSame = true;

        foreach ($records as $recordNo) {
            // Check if the previous record is the same with the current one
            $record = $this->samples[$recordNo];
            if ($prevRecord && $prevRecord != $record) {
                $allSame = false;
            }
            $prevRecord = $record;

            // According to the split criteron, this record will
            // belong to either left or the right side in the next split
            if ($split->evaluate($record)) {
                $leftRecords[] = $recordNo;
            } else {
                $rightRecords[]= $recordNo;
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
            $split->isTerminal = 1;
            arsort($remainingTargets);
            $split->classValue = key($remainingTargets);
        } else {
            if ($leftRecords) {
                $split->leftLeaf = $this->getSplitLeaf($leftRecords, $depth + 1);
            }
            if ($rightRecords) {
                $split->rightLeaf= $this->getSplitLeaf($rightRecords, $depth + 1);
            }
        }

        return $split;
    }

    /**
     * @param array $records
     *
     * @return DecisionTreeLeaf
     */
    protected function getBestSplit(array $records) : DecisionTreeLeaf
    {
        $targets = array_intersect_key($this->targets, array_flip($records));
        $samples = array_intersect_key($this->samples, array_flip($records));
        $samples = array_combine($records, $this->preprocess($samples));
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
            $gini = $this->getGiniIndex($baseValue, $colValues, $targets);
            if ($bestSplit === null || $bestGiniVal > $gini) {
                $split = new DecisionTreeLeaf();
                $split->value = $baseValue;
                $split->giniIndex = $gini;
                $split->columnIndex = $i;
                $split->isContinuous = $this->columnTypes[$i] == self::CONTINUOUS;
                $split->records = $records;

                // If a numeric column is to be selected, then
                // the original numeric value and the selected operator
                // will also be saved into the leaf for future access
                if ($this->columnTypes[$i] == self::CONTINUOUS) {
                    $matches = [];
                    preg_match("/^([<>=]{1,2})\s*(.*)/", strval($split->value), $matches);
                    $split->operator = $matches[1];
                    $split->numericValue = floatval($matches[2]);
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
     *
     * @return array
     */
    protected function getSelectedFeatures() : array
    {
        $allFeatures = range(0, $this->featureCount - 1);
        if ($this->numUsableFeatures === 0 && !$this->selectedFeatures) {
            return $allFeatures;
        }

        if ($this->selectedFeatures) {
            return $this->selectedFeatures;
        }

        $numFeatures = $this->numUsableFeatures;
        if ($numFeatures > $this->featureCount) {
            $numFeatures = $this->featureCount;
        }
        shuffle($allFeatures);
        $selectedFeatures = array_slice($allFeatures, 0, $numFeatures, false);
        sort($selectedFeatures);

        return $selectedFeatures;
    }

    /**
     * @param mixed $baseValue
     * @param array $colValues
     * @param array $targets
     *
     * @return float
     */
    public function getGiniIndex($baseValue, array $colValues, array $targets) : float
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
                    $part += pow($countMatrix[$label][$i] / floatval($sum), 2);
                }
            }

            $giniParts[$i] = (1 - $part) * $sum;
        }

        return array_sum($giniParts) / count($colValues);
    }

    /**
     * @param array $samples
     *
     * @return array
     */
    protected function preprocess(array $samples) : array
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
                        $value = "<= $median";
                    } else {
                        $value = "> $median";
                    }
                }
            }
            $columns[] = $values;
        }
        // Below method is a strange yet very simple & efficient method
        // to get the transpose of a 2D array
        return array_map(null, ...$columns);
    }

    /**
     * @param array $columnValues
     *
     * @return bool
     */
    protected static function isCategoricalColumn(array $columnValues) : bool
    {
        $count = count($columnValues);

        // There are two main indicators that *may* show whether a
        // column is composed of discrete set of values:
        // 1- Column may contain string values and non-float values
        // 2- Number of unique values in the column is only a small fraction of
        //	  all values in that column (Lower than or equal to %20 of all values)
        $numericValues = array_filter($columnValues, 'is_numeric');
        $floatValues = array_filter($columnValues, 'is_float');
        if ($floatValues) {
            return false;
        }

        if (count($numericValues) !== $count) {
            return true;
        }

        $distinctValues = array_count_values($columnValues);

        return count($distinctValues) <= $count / 5;
    }

    /**
     * This method is used to set number of columns to be used
     * when deciding a split at an internal node of the tree.  <br>
     * If the value is given 0, then all features are used (default behaviour),
     * otherwise the given value will be used as a maximum for number of columns
     * randomly selected for each split operation.
     *
     * @param int $numFeatures
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
     * Used to set predefined features to consider while deciding which column to use for a split
     *
     * @param array $selectedFeatures
     */
    protected function setSelectedFeatures(array $selectedFeatures)
    {
        $this->selectedFeatures = $selectedFeatures;
    }

    /**
     * A string array to represent columns. Useful when HTML output or
     * column importances are desired to be inspected.
     *
     * @param array $names
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

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->tree->getHTML($this->columnNames);
    }

    /**
     * This will return an array including an importance value for
     * each column in the given dataset. The importance values are
     * normalized and their total makes 1.<br/>
     *
     * @return array
     */
    public function getFeatureImportances()
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
            foreach ($this->featureImportances as &$importance) {
                $importance /= $total;
            }
            arsort($this->featureImportances);
        }

        return $this->featureImportances;
    }

    /**
     * Collects and returns an array of internal nodes that use the given
     * column as a split criterion
     *
     * @param int              $column
     * @param DecisionTreeLeaf $node
     *
     * @return array
     */
    protected function getSplitNodesByColumn(int $column, DecisionTreeLeaf $node) : array
    {
        if (!$node || $node->isTerminal) {
            return [];
        }

        $nodes = [];
        if ($node->columnIndex === $column) {
            $nodes[] = $node;
        }

        $lNodes = [];
        $rNodes = [];
        if ($node->leftLeaf) {
            $lNodes = $this->getSplitNodesByColumn($column, $node->leftLeaf);
        }

        if ($node->rightLeaf) {
            $rNodes = $this->getSplitNodesByColumn($column, $node->rightLeaf);
        }

        $nodes = array_merge($nodes, $lNodes, $rNodes);

        return $nodes;
    }

    /**
     * @param array $sample
     *
     * @return mixed
     */
    protected function predictSample(array $sample)
    {
        $node = $this->tree;
        do {
            if ($node->isTerminal) {
                break;
            }

            if ($node->evaluate($sample)) {
                $node = $node->leftLeaf;
            } else {
                $node = $node->rightLeaf;
            }
        } while ($node);

        return $node ? $node->classValue : $this->labels[0];
    }
}
