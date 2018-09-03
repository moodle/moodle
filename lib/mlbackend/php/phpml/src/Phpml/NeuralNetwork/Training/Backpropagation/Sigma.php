<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Training\Backpropagation;

use Phpml\NeuralNetwork\Node\Neuron;

class Sigma
{
    /**
     * @var Neuron
     */
    private $neuron;

    /**
     * @var float
     */
    private $sigma;

    /**
     * @param Neuron $neuron
     * @param float  $sigma
     */
    public function __construct(Neuron $neuron, $sigma)
    {
        $this->neuron = $neuron;
        $this->sigma = $sigma;
    }

    /**
     * @return Neuron
     */
    public function getNeuron()
    {
        return $this->neuron;
    }

    /**
     * @return float
     */
    public function getSigma()
    {
        return $this->sigma;
    }

    /**
     * @param Neuron $neuron
     *
     * @return float
     */
    public function getSigmaForNeuron(Neuron $neuron): float
    {
        $sigma = 0.0;

        foreach ($this->neuron->getSynapses() as $synapse) {
            if ($synapse->getNode() == $neuron) {
                $sigma += $synapse->getWeight() * $this->getSigma();
            }
        }

        return $sigma;
    }
}
