<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Network;

use Phpml\Exception\InvalidArgumentException;
use Phpml\NeuralNetwork\ActivationFunction;
use Phpml\NeuralNetwork\Layer;
use Phpml\NeuralNetwork\Node\Bias;
use Phpml\NeuralNetwork\Node\Input;
use Phpml\NeuralNetwork\Node\Neuron;
use Phpml\NeuralNetwork\Node\Neuron\Synapse;

class MultilayerPerceptron extends LayeredNetwork
{
    /**
     * @param array                   $layers
     * @param ActivationFunction|null $activationFunction
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $layers, ActivationFunction $activationFunction = null)
    {
        if (count($layers) < 2) {
            throw InvalidArgumentException::invalidLayersNumber();
        }

        $this->addInputLayer(array_shift($layers));
        $this->addNeuronLayers($layers, $activationFunction);
        $this->addBiasNodes();
        $this->generateSynapses();
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
}
