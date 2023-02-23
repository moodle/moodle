<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Network;

use Phpml\Estimator;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Helper\Predictable;
use Phpml\IncrementalEstimator;
use Phpml\NeuralNetwork\ActivationFunction;
use Phpml\NeuralNetwork\ActivationFunction\Sigmoid;
use Phpml\NeuralNetwork\Layer;
use Phpml\NeuralNetwork\Node\Bias;
use Phpml\NeuralNetwork\Node\Input;
use Phpml\NeuralNetwork\Node\Neuron;
use Phpml\NeuralNetwork\Node\Neuron\Synapse;
use Phpml\NeuralNetwork\Training\Backpropagation;

abstract class MultilayerPerceptron extends LayeredNetwork implements Estimator, IncrementalEstimator
{
    use Predictable;

    /**
     * @var array
     */
    protected $classes = [];

    /**
     * @var ActivationFunction|null
     */
    protected $activationFunction;

    /**
     * @var Backpropagation
     */
    protected $backpropagation;

    /**
     * @var int
     */
    private $inputLayerFeatures;

    /**
     * @var array
     */
    private $hiddenLayers = [];

    /**
     * @var float
     */
    private $learningRate;

    /**
     * @var int
     */
    private $iterations;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        int $inputLayerFeatures,
        array $hiddenLayers,
        array $classes,
        int $iterations = 10000,
        ?ActivationFunction $activationFunction = null,
        float $learningRate = 1.
    ) {
        if (count($hiddenLayers) === 0) {
            throw new InvalidArgumentException('Provide at least 1 hidden layer');
        }

        if (count($classes) < 2) {
            throw new InvalidArgumentException('Provide at least 2 different classes');
        }

        if (count($classes) !== count(array_unique($classes))) {
            throw new InvalidArgumentException('Classes must be unique');
        }

        $this->classes = array_values($classes);
        $this->iterations = $iterations;
        $this->inputLayerFeatures = $inputLayerFeatures;
        $this->hiddenLayers = $hiddenLayers;
        $this->activationFunction = $activationFunction;
        $this->learningRate = $learningRate;

        $this->initNetwork();
    }

    public function train(array $samples, array $targets): void
    {
        $this->reset();
        $this->initNetwork();
        $this->partialTrain($samples, $targets, $this->classes);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function partialTrain(array $samples, array $targets, array $classes = []): void
    {
        if (count($classes) > 0 && array_values($classes) !== $this->classes) {
            // We require the list of classes in the constructor.
            throw new InvalidArgumentException(
                'The provided classes don\'t match the classes provided in the constructor'
            );
        }

        for ($i = 0; $i < $this->iterations; ++$i) {
            $this->trainSamples($samples, $targets);
        }
    }

    public function setLearningRate(float $learningRate): void
    {
        $this->learningRate = $learningRate;
        $this->backpropagation->setLearningRate($this->learningRate);
    }

    public function getOutput(): array
    {
        $result = [];
        foreach ($this->getOutputLayer()->getNodes() as $i => $neuron) {
            $result[$this->classes[$i]] = $neuron->getOutput();
        }

        return $result;
    }

    public function getLearningRate(): float
    {
        return $this->learningRate;
    }

    public function getBackpropagation(): Backpropagation
    {
        return $this->backpropagation;
    }

    /**
     * @param mixed $target
     */
    abstract protected function trainSample(array $sample, $target): void;

    /**
     * @return mixed
     */
    abstract protected function predictSample(array $sample);

    protected function reset(): void
    {
        $this->removeLayers();
    }

    private function initNetwork(): void
    {
        $this->addInputLayer($this->inputLayerFeatures);
        $this->addNeuronLayers($this->hiddenLayers, $this->activationFunction);

        // Sigmoid function for the output layer as we want a value from 0 to 1.
        $sigmoid = new Sigmoid();
        $this->addNeuronLayers([count($this->classes)], $sigmoid);

        $this->addBiasNodes();
        $this->generateSynapses();

        $this->backpropagation = new Backpropagation($this->learningRate);
    }

    private function addInputLayer(int $nodes): void
    {
        $this->addLayer(new Layer($nodes, Input::class));
    }

    private function addNeuronLayers(array $layers, ?ActivationFunction $defaultActivationFunction = null): void
    {
        foreach ($layers as $layer) {
            if (is_array($layer)) {
                $function = $layer[1] instanceof ActivationFunction ? $layer[1] : $defaultActivationFunction;
                $this->addLayer(new Layer($layer[0], Neuron::class, $function));
            } elseif ($layer instanceof Layer) {
                $this->addLayer($layer);
            } else {
                $this->addLayer(new Layer($layer, Neuron::class, $defaultActivationFunction));
            }
        }
    }

    private function generateSynapses(): void
    {
        $layersNumber = count($this->layers) - 1;
        for ($i = 0; $i < $layersNumber; ++$i) {
            $currentLayer = $this->layers[$i];
            $nextLayer = $this->layers[$i + 1];
            $this->generateLayerSynapses($nextLayer, $currentLayer);
        }
    }

    private function addBiasNodes(): void
    {
        $biasLayers = count($this->layers) - 1;
        for ($i = 0; $i < $biasLayers; ++$i) {
            $this->layers[$i]->addNode(new Bias());
        }
    }

    private function generateLayerSynapses(Layer $nextLayer, Layer $currentLayer): void
    {
        foreach ($nextLayer->getNodes() as $nextNeuron) {
            if ($nextNeuron instanceof Neuron) {
                $this->generateNeuronSynapses($currentLayer, $nextNeuron);
            }
        }
    }

    private function generateNeuronSynapses(Layer $currentLayer, Neuron $nextNeuron): void
    {
        foreach ($currentLayer->getNodes() as $currentNeuron) {
            $nextNeuron->addSynapse(new Synapse($currentNeuron));
        }
    }

    private function trainSamples(array $samples, array $targets): void
    {
        foreach ($targets as $key => $target) {
            $this->trainSample($samples[$key], $target);
        }
    }
}
