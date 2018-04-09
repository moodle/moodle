<?php

declare(strict_types=1);

namespace Phpml\Helper;

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
     *
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
    {
        // Clears previous stuff.
        $this->reset();

        $this->trainBylabel($samples, $targets);
    }

    /**
     * @param array $samples
     * @param array $targets
     * @param array $allLabels All training set labels
     *
     * @return void
     */
    protected function trainByLabel(array $samples, array $targets, array $allLabels = [])
    {
        // Overwrites the current value if it exist. $allLabels must be provided for each partialTrain run.
        if (!empty($allLabels)) {
            $this->allLabels = $allLabels;
        } else {
            $this->allLabels = array_keys(array_count_values($targets));
        }
        sort($this->allLabels, SORT_STRING);

        // If there are only two targets, then there is no need to perform OvR
        if (count($this->allLabels) == 2) {
            // Init classifier if required.
            if (empty($this->classifiers)) {
                $this->classifiers[0] = $this->getClassifierCopy();
            }

            $this->classifiers[0]->trainBinary($samples, $targets, $this->allLabels);
        } else {
            // Train a separate classifier for each label and memorize them

            foreach ($this->allLabels as $label) {
                // Init classifier if required.
                if (empty($this->classifiers[$label])) {
                    $this->classifiers[$label] = $this->getClassifierCopy();
                }

                list($binarizedTargets, $classifierLabels) = $this->binarizeTargets($targets, $label);
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
     * Resets the classifier and the vars internally used by OneVsRest to create multiple classifiers.
     */
    public function reset()
    {
        $this->classifiers = [];
        $this->allLabels = [];
        $this->costValues = [];

        $this->resetBinary();
    }

    /**
     * Returns an instance of the current class after cleaning up OneVsRest stuff.
     *
     * @return \Phpml\Estimator
     */
    protected function getClassifierCopy()
    {
        // Clone the current classifier, so that
        // we don't mess up its variables while training
        // multiple instances of this classifier
        $classifier = clone $this;
        $classifier->reset();
        return $classifier;
    }

    /**
     * Groups all targets into two groups: Targets equal to
     * the given label and the others
     *
     * $targets is not passed by reference nor contains objects so this method
     * changes will not affect the caller $targets array.
     *
     * @param array $targets
     * @param mixed $label
     * @return array Binarized targets and target's labels
     */
    private function binarizeTargets($targets, $label)
    {
        $notLabel = "not_$label";
        foreach ($targets as $key => $target) {
            $targets[$key] = $target == $label ? $label : $notLabel;
        }

        $labels = [$label, $notLabel];
        return [$targets, $labels];
    }


    /**
     * @param array $sample
     *
     * @return mixed
     */
    protected function predictSample(array $sample)
    {
        if (count($this->allLabels) == 2) {
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
     *
     * @param array $samples
     * @param array $targets
     * @param array $labels
     */
    abstract protected function trainBinary(array $samples, array $targets, array $labels);

    /**
     * To be overwritten by OneVsRest classifiers.
     *
     * @return void
     */
    abstract protected function resetBinary();

    /**
     * Each classifier that make use of OvR approach should be able to
     * return a probability for a sample to belong to the given label.
     *
     * @param array  $sample
     * @param string $label
     *
     * @return mixed
     */
    abstract protected function predictProbability(array $sample, string $label);

    /**
     * Each classifier should implement this method instead of predictSample()
     *
     * @param array $sample
     *
     * @return mixed
     */
    abstract protected function predictSampleBinary(array $sample);
}
