<?php

declare(strict_types=1);

namespace Phpml\Classification\Ensemble;

use Phpml\Helper\Predictable;
use Phpml\Helper\Trainable;
use Phpml\Classification\Classifier;
use Phpml\Classification\DecisionTree;

class Bagging implements Classifier
{
    use Trainable, Predictable;

    /**
     * @var int
     */
    protected $numSamples;

    /**
     * @var array
     */
    private $targets = [];

    /**
     * @var int
     */
    protected $featureCount = 0;

    /**
     * @var int
     */
    protected $numClassifier;

    /**
     * @var Classifier
     */
    protected $classifier = DecisionTree::class;

    /**
     * @var array
     */
    protected $classifierOptions = ['depth' => 20];

    /**
     * @var array
     */
    protected $classifiers;

    /**
     * @var float
     */
    protected $subsetRatio = 0.7;

    /**
     * @var array
     */
    private $samples = [];

    /**
     * Creates an ensemble classifier with given number of base classifiers
     * Default number of base classifiers is 50.
     * The more number of base classifiers, the better performance but at the cost of procesing time
     *
     * @param int $numClassifier
     */
    public function __construct(int $numClassifier = 50)
    {
        $this->numClassifier = $numClassifier;
    }

    /**
     * This method determines the ratio of samples used to create the 'bootstrap' subset,
     * e.g., random samples drawn from the original dataset with replacement (allow repeats),
     * to train each base classifier.
     *
     * @param float $ratio
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setSubsetRatio(float $ratio)
    {
        if ($ratio < 0.1 || $ratio > 1.0) {
            throw new \Exception("Subset ratio should be between 0.1 and 1.0");
        }

        $this->subsetRatio = $ratio;
        return $this;
    }

    /**
     * This method is used to set the base classifier. Default value is
     * DecisionTree::class, but any class that implements the <i>Classifier</i>
     * can be used. <br>
     * While giving the parameters of the classifier, the values should be
     * given in the order they are in the constructor of the classifier and parameter
     * names are neglected.
     *
     * @param string $classifier
     * @param array $classifierOptions
     *
     * @return $this
     */
    public function setClassifer(string $classifier, array $classifierOptions = [])
    {
        $this->classifier = $classifier;
        $this->classifierOptions = $classifierOptions;

        return $this;
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
    {
        $this->samples = array_merge($this->samples, $samples);
        $this->targets = array_merge($this->targets, $targets);
        $this->featureCount = count($samples[0]);
        $this->numSamples = count($this->samples);

        // Init classifiers and train them with bootstrap samples
        $this->classifiers = $this->initClassifiers();
        $index = 0;
        foreach ($this->classifiers as $classifier) {
            list($samples, $targets) = $this->getRandomSubset($index);
            $classifier->train($samples, $targets);
            ++$index;
        }
    }

    /**
     * @param int $index
     * @return array
     */
    protected function getRandomSubset(int $index)
    {
        $samples = [];
        $targets = [];
        srand($index);
        $bootstrapSize = $this->subsetRatio * $this->numSamples;
        for ($i = 0; $i < $bootstrapSize; ++$i) {
            $rand = rand(0, $this->numSamples - 1);
            $samples[] = $this->samples[$rand];
            $targets[] = $this->targets[$rand];
        }

        return [$samples, $targets];
    }

    /**
     * @return array
     */
    protected function initClassifiers()
    {
        $classifiers = [];
        for ($i = 0; $i < $this->numClassifier; ++$i) {
            $ref = new \ReflectionClass($this->classifier);
            if ($this->classifierOptions) {
                $obj = $ref->newInstanceArgs($this->classifierOptions);
            } else {
                $obj = $ref->newInstance();
            }

            $classifiers[] = $this->initSingleClassifier($obj);
        }
        return $classifiers;
    }

    /**
     * @param Classifier $classifier
     *
     * @return Classifier
     */
    protected function initSingleClassifier($classifier)
    {
        return $classifier;
    }

    /**
     * @param array $sample
     * @return mixed
     */
    protected function predictSample(array $sample)
    {
        $predictions = [];
        foreach ($this->classifiers as $classifier) {
            /* @var $classifier Classifier */
            $predictions[] = $classifier->predict($sample);
        }

        $counts = array_count_values($predictions);
        arsort($counts);
        reset($counts);
        return key($counts);
    }
}
