<?php

declare(strict_types=1);

namespace Phpml\Helper;

use Phpml\Classification\Classifier;

trait OneVsRest
{
    /**
     * @var array
     */
    protected $classifiers = [];

    /**
     * All provided training targets' labels.
     *
     * @var array
     */
    protected $allLabels = [];

    /**
     * @var array
     */
    protected $costValues = [];

    /**
     * Train a binary classifier in the OvR style
     */
    public function train(array $samples, array $targets): void
    {
        // Clears previous stuff.
        $this->reset();

        $this->trainByLabel($samples, $targets);
    }

    /**
     * Resets the classifier and the vars internally used by OneVsRest to create multiple classifiers.
     */
    public function reset(): void
    {
        $this->classifiers = [];
        $this->allLabels = [];
        $this->costValues = [];

        $this->resetBinary();
    }

    protected function trainByLabel(array $samples, array $targets, array $allLabels = []): void
    {
        // Overwrites the current value if it exist. $allLabels must be provided for each partialTrain run.
        $this->allLabels = count($allLabels) === 0 ? array_keys(array_count_values($targets)) : $allLabels;
        sort($this->allLabels, SORT_STRING);

        // If there are only two targets, then there is no need to perform OvR
        if (count($this->allLabels) === 2) {
            // Init classifier if required.
            if (count($this->classifiers) === 0) {
                $this->classifiers[0] = $this->getClassifierCopy();
            }

            $this->classifiers[0]->trainBinary($samples, $targets, $this->allLabels);
        } else {
            // Train a separate classifier for each label and memorize them

            foreach ($this->allLabels as $label) {
                // Init classifier if required.
                if (!isset($this->classifiers[$label])) {
                    $this->classifiers[$label] = $this->getClassifierCopy();
                }

                [$binarizedTargets, $classifierLabels] = $this->binarizeTargets($targets, $label);
                $this->classifiers[$label]->trainBinary($samples, $binarizedTargets, $classifierLabels);
            }
        }

        // If the underlying classifier is capable of giving the cost values
        // during the training, then assign it to the relevant variable
        // Adding just the first classifier cost values to avoid complex average calculations.
        $classifierref = reset($this->classifiers);
        if (method_exists($classifierref, 'getCostValues')) {
            $this->costValues = $classifierref->getCostValues();
        }
    }

    /**
     * Returns an instance of the current class after cleaning up OneVsRest stuff.
     */
    protected function getClassifierCopy(): Classifier
    {
        // Clone the current classifier, so that
        // we don't mess up its variables while training
        // multiple instances of this classifier
        $classifier = clone $this;
        $classifier->reset();

        return $classifier;
    }

    /**
     * @return mixed
     */
    protected function predictSample(array $sample)
    {
        if (count($this->allLabels) === 2) {
            return $this->classifiers[0]->predictSampleBinary($sample);
        }

        $probs = [];

        foreach ($this->classifiers as $label => $predictor) {
            $probs[$label] = $predictor->predictProbability($sample, $label);
        }

        arsort($probs, SORT_NUMERIC);

        return key($probs);
    }

    /**
     * Each classifier should implement this method instead of train(samples, targets)
     */
    abstract protected function trainBinary(array $samples, array $targets, array $labels);

    /**
     * To be overwritten by OneVsRest classifiers.
     */
    abstract protected function resetBinary(): void;

    /**
     * Each classifier that make use of OvR approach should be able to
     * return a probability for a sample to belong to the given label.
     *
     * @return mixed
     */
    abstract protected function predictProbability(array $sample, string $label);

    /**
     * Each classifier should implement this method instead of predictSample()
     *
     * @return mixed
     */
    abstract protected function predictSampleBinary(array $sample);

    /**
     * Groups all targets into two groups: Targets equal to
     * the given label and the others
     *
     * $targets is not passed by reference nor contains objects so this method
     * changes will not affect the caller $targets array.
     *
     * @param mixed $label
     *
     * @return array Binarized targets and target's labels
     */
    private function binarizeTargets(array $targets, $label): array
    {
        $notLabel = "not_${label}";
        foreach ($targets as $key => $target) {
            $targets[$key] = $target == $label ? $label : $notLabel;
        }

        $labels = [$label, $notLabel];

        return [$targets, $labels];
    }
}
