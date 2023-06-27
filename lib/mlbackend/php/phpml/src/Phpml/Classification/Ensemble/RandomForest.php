<?php

declare(strict_types=1);

namespace Phpml\Classification\Ensemble;

use Phpml\Classification\Classifier;
use Phpml\Classification\DecisionTree;
use Phpml\Exception\InvalidArgumentException;

class RandomForest extends Bagging
{
    /**
     * @var float|string
     */
    protected $featureSubsetRatio = 'log';

    /**
     * @var array|null
     */
    protected $columnNames;

    /**
     * Initializes RandomForest with the given number of trees. More trees
     * may increase the prediction performance while it will also substantially
     * increase the processing time and the required memory
     */
    public function __construct(int $numClassifier = 50)
    {
        parent::__construct($numClassifier);

        $this->setSubsetRatio(1.0);
    }

    /**
     * This method is used to determine how many of the original columns (features)
     * will be used to construct subsets to train base classifiers.<br>
     *
     * Allowed values: 'sqrt', 'log' or any float number between 0.1 and 1.0 <br>
     *
     * Default value for the ratio is 'log' which results in log(numFeatures, 2) + 1
     * features to be taken into consideration while selecting subspace of features
     *
     * @param string|float $ratio
     */
    public function setFeatureSubsetRatio($ratio): self
    {
        if (!is_string($ratio) && !is_float($ratio)) {
            throw new InvalidArgumentException('Feature subset ratio must be a string or a float');
        }

        if (is_float($ratio) && ($ratio < 0.1 || $ratio > 1.0)) {
            throw new InvalidArgumentException('When a float is given, feature subset ratio should be between 0.1 and 1.0');
        }

        if (is_string($ratio) && $ratio !== 'sqrt' && $ratio !== 'log') {
            throw new InvalidArgumentException("When a string is given, feature subset ratio can only be 'sqrt' or 'log'");
        }

        $this->featureSubsetRatio = $ratio;

        return $this;
    }

    /**
     * RandomForest algorithm is usable *only* with DecisionTree
     *
     * @return $this
     */
    public function setClassifer(string $classifier, array $classifierOptions = [])
    {
        if ($classifier !== DecisionTree::class) {
            throw new InvalidArgumentException('RandomForest can only use DecisionTree as base classifier');
        }

        return parent::setClassifer($classifier, $classifierOptions);
    }

    /**
     * This will return an array including an importance value for
     * each column in the given dataset. Importance values for a column
     * is the average importance of that column in all trees in the forest
     */
    public function getFeatureImportances(): array
    {
        // Traverse each tree and sum importance of the columns
        $sum = [];
        foreach ($this->classifiers as $tree) {
            /** @var DecisionTree $tree */
            $importances = $tree->getFeatureImportances();

            foreach ($importances as $column => $importance) {
                if (array_key_exists($column, $sum)) {
                    $sum[$column] += $importance;
                } else {
                    $sum[$column] = $importance;
                }
            }
        }

        // Normalize & sort the importance values
        $total = array_sum($sum);
        array_walk($sum, function (&$importance) use ($total): void {
            $importance /= $total;
        });
        arsort($sum);

        return $sum;
    }

    /**
     * A string array to represent the columns is given. They are useful
     * when trying to print some information about the trees such as feature importances
     *
     * @return $this
     */
    public function setColumnNames(array $names)
    {
        $this->columnNames = $names;

        return $this;
    }

    /**
     * @param DecisionTree $classifier
     *
     * @return DecisionTree
     */
    protected function initSingleClassifier(Classifier $classifier): Classifier
    {
        if (is_float($this->featureSubsetRatio)) {
            $featureCount = (int) ($this->featureSubsetRatio * $this->featureCount);
        } elseif ($this->featureSubsetRatio === 'sqrt') {
            $featureCount = (int) ($this->featureCount ** .5) + 1;
        } else {
            $featureCount = (int) log($this->featureCount, 2) + 1;
        }

        if ($featureCount >= $this->featureCount) {
            $featureCount = $this->featureCount;
        }

        if ($this->columnNames === null) {
            $this->columnNames = range(0, $this->featureCount - 1);
        }

        return $classifier
            ->setColumnNames($this->columnNames)
            ->setNumFeatures($featureCount);
    }
}
