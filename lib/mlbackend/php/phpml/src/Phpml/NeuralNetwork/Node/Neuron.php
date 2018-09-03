<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Node;

use Phpml\NeuralNetwork\ActivationFunction;
use Phpml\NeuralNetwork\Node\Neuron\Synapse;
use Phpml\NeuralNetwork\Node;

class Neuron implements Node
{
    /**
     * @var Synapse[]
     */
    protected $synapses;

    /**
     * @var ActivationFunction
     */
    protected $activationFunction;

    /**
     * @var float
     */
    protected $output;

    /**
     * @param ActivationFunction|null $activationFunction
     */
    public function __construct(ActivationFunction $activationFunction = null)
    {
        $this->activationFunction = $activationFunction ?: new ActivationFunction\Sigmoid();
        $this->synapses = [];
        $this->output = 0;
    }

    /**
     * @param Synapse $synapse
     */
    public function addSynapse(Synapse $synapse)
    {
        $this->synapses[] = $synapse;
    }

    /**
     * @return Synapse[]
     */
    public function getSynapses()
    {
        return $this->synapses;
    }

    /**
     * @return float
     */
    public function getOutput(): float
    {
        if (0 === $this->output) {
            $sum = 0;
            foreach ($this->synapses as $synapse) {
                $sum += $synapse->getOutput();
            }

            $this->output = $this->activationFunction->compute($sum);
        }

        return $this->output;
    }

    public function reset()
    {
        $this->output = 0;
    }
}
