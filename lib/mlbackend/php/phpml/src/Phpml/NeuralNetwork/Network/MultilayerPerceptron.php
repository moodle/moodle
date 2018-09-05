<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Network;

use Phpml\Estimator;
use Phpml\IncrementalEstimator;
use Phpml\Exception\InvalidArgumentException;
use Phpml\NeuralNetwork\Training\Backpropagation;
use Phpml\NeuralNetwork\ActivationFunction;
use Phpml\NeuralNetwork\Layer;
use Phpml\NeuralNetwork\Node\Bias;
use Phpml\NeuralNetwork\Node\Input;
use Phpml\NeuralNetwork\Node\Neuron;
use Phpml\NeuralNetwork\Node\Neuron\Synapse;
use Phpml\Helper\Predictable;

abstract class MultilayerPerceptron extends LayeredNetwork implements Estimator, IncrementalEstimator
{
    use Predictable;

    /**
     * @var int
     */
    private $inputLayerFeatures;

    /**
     * @var array
     */
    private $hiddenLayers;

    /**
     * @var array
     */
    protected $classes = [];

    /**
     * @var int
     */
    private $iterations;

    /**
     * @var ActivationFunction
     */
    protected $activationFunction;

    /**
     * @var int
     */
    private $theta;

    /**
     * @var Backpropagation
     */
    protected $backpropagation = null;

    /**
     * @param int                     $inputLayerFeatures
     * @param array                   $hiddenLayers
     * @param array                   $classes
     * @param int                     $iterations
     * @param ActivationFunction|null $activationFunction
     * @param int                     $theta
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $inputLayerFeatures, array $hiddenLayers, array $classes, int $iterations = 10000, ActivationFunction $activationFunction = null, int $theta = 1)
    {
        if (empty($hiddenLayers)) {
            throw InvalidArgumentException::invalidLayersNumber();
        }

        if (count($classes) < 2) {
            throw InvalidArgumentException::invalidClassesNumber();
        }

        $this->classes = array_values($classes);
        $this->iterations = $iterations;
        $this->inputLayerFeatures = $inputLayerFeatures;
        $this->hiddenLayers = $hiddenLayers;
        $this->activationFunction = $activationFunction;
        $this->theta = $theta;

        $this->initNetwork();
    }

    /**
     * @return void
     */
    private function initNetwork()
    {
        $this->addInputLayer($this->inputLayerFeatures);
        $this->addNeuronLayers($this->hiddenLayers, $this->activationFunction);
        $this->addNeuronLayers([count($this->classes)], $this->activationFunction);

        $this->addBiasNodes();
        $this->generateSynapses();

        $this->backpropagation = new Backpropagation($this->theta);
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets)
    {
        $this->reset();
        $this->initNetwork();
        $this->partialTrain($samples, $targets, $this->classes);
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    public function partialTrain(array $samples, array $targets, array $classes = [])
    {
        if (!empty($classes) && array_values($classes) !== $this->classes) {
            // We require the list of classes in the constructor.
            throw InvalidArgumentException::inconsistentClasses();
        }

        for ($i = 0; $i < $this->iterations; ++$i) {
            $this->trainSamples($samples, $targets);
        }
    }

    /**
     * @param array $sample
     * @param mixed $target
     */
    abstract protected function trainSample(array $sample, $target);

    /**
     * @param array $sample
     * @return mixed
     */
    abstract protected function predictSample(array $sample);

    /**
     * @return void
     */
    protected function reset()
    {
        $this->removeLayers();
    }

    /**
     * @param int $nodes
     */
    private function addInputLayer(int $nodes)
    {
        $this->addLayer(new Layer($nodes, Input::class));
    }

    /**
     * @param array                   $layers
     * @param ActivationFunction|null $activationFunction
     */
    private function addNeuronLayers(array $layers, ActivationFunction $activationFunction = null)
    {
        foreach ($layers as $neurons) {
            $this->addLayer(new Layer($neurons, Neuron::class, $activationFunction));
        }
    }

    private function generateSynapses()
    {
        $layersNumber = count($this->layers) - 1;
        for ($i = 0; $i < $layersNumber; ++$i) {
            $currentLayer = $this->layers[$i];
            $nextLayer = $this->layers[$i + 1];
            $this->generateLayerSynapses($nextLayer, $currentLayer);
        }
    }

    private function addBiasNodes()
    {
        $biasLayers = count($this->layers) - 1;
        for ($i = 0; $i < $biasLayers; ++$i) {
            $this->layers[$i]->addNode(new Bias());
        }
    }

    /**
     * @param Layer $nextLayer
     * @param Layer $currentLayer
     */
    private function generateLayerSynapses(Layer $nextLayer, Layer $currentLayer)
    {
        foreach ($nextLayer->getNodes() as $nextNeuron) {
            if ($nextNeuron instanceof Neuron) {
                $this->generateNeuronSynapses($currentLayer, $nextNeuron);
            }
        }
    }

    /**
     * @param Layer  $currentLayer
     * @param Neuron $nextNeuron
     */
    private function generateNeuronSynapses(Layer $currentLayer, Neuron $nextNeuron)
    {
        foreach ($currentLayer->getNodes() as $currentNeuron) {
            $nextNeuron->addSynapse(new Synapse($currentNeuron));
        }
    }

    /**
     * @param array $samples
     * @param array $targets
     */
    private function trainSamples(array $samples, array $targets)
    {
        foreach ($targets as $key => $target) {
            $this->trainSample($samples[$key], $target);
        }
    }
}
