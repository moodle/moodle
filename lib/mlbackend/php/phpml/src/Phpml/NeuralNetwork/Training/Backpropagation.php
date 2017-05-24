<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Training;

use Phpml\NeuralNetwork\Node\Neuron;
use Phpml\NeuralNetwork\Training\Backpropagation\Sigma;

class Backpropagation
{
    /**
     * @var int
     */
    private $theta;

    /**
     * @var array
     */
    private $sigmas = null;

    /**
     * @var array
     */
    private $prevSigmas = null;

    /**
     * @param int $theta
     */
    public function __construct(int $theta)
    {
        $this->theta = $theta;
    }

    /**
     * @param array $layers
     * @param mixed $targetClass
     */
    public function backpropagate(array $layers, $targetClass)
    {
        $layersNumber = count($layers);

        // Backpropagation.
        for ($i = $layersNumber; $i > 1; --$i) {
            $this->sigmas = [];
            foreach ($layers[$i - 1]->getNodes() as $key => $neuron) {
                if ($neuron instanceof Neuron) {
                    $sigma = $this->getSigma($neuron, $targetClass, $key, $i == $layersNumber);
                    foreach ($neuron->getSynapses() as $synapse) {
                        $synapse->changeWeight($this->theta * $sigma * $synapse->getNode()->getOutput());
                    }
                }
            }
            $this->prevSigmas = $this->sigmas;
        }

        // Clean some memory (also it helps make MLP persistency & children more maintainable).
        $this->sigmas = null;
        $this->prevSigmas = null;
    }

    /**
     * @param Neuron $neuron
     * @param int    $targetClass
     * @param int    $key
     * @param bool   $lastLayer
     *
     * @return float
     */
    private function getSigma(Neuron $neuron, int $targetClass, int $key, bool $lastLayer): float
    {
        $neuronOutput = $neuron->getOutput();
        $sigma = $neuronOutput * (1 - $neuronOutput);

        if ($lastLayer) {
            $value = 0;
            if ($targetClass === $key) {
                $value = 1;
            }
            $sigma *= ($value - $neuronOutput);
        } else {
            $sigma *= $this->getPrevSigma($neuron);
        }

        $this->sigmas[] = new Sigma($neuron, $sigma);

        return $sigma;
    }

    /**
     * @param Neuron $neuron
     *
     * @return float
     */
    private function getPrevSigma(Neuron $neuron): float
    {
        $sigma = 0.0;

        foreach ($this->prevSigmas as $neuronSigma) {
            $sigma += $neuronSigma->getSigmaForNeuron($neuron);
        }

        return $sigma;
    }
}
