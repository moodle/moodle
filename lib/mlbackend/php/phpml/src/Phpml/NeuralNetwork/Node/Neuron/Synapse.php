<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Node\Neuron;

use Phpml\NeuralNetwork\Node;

class Synapse
{
    /**
     * @var float
     */
    protected $weight;

    /**
     * @var Node
     */
    protected $node;

    /**
     * @param Node       $node
     * @param float|null $weight
     */
    public function __construct(Node $node, float $weight = null)
    {
        $this->node = $node;
        $this->weight = $weight ?: $this->generateRandomWeight();
    }

    /**
     * @return float
     */
    protected function generateRandomWeight(): float
    {
        return 1 / random_int(5, 25) * (random_int(0, 1) ? -1 : 1);
    }

    /**
     * @return float
     */
    public function getOutput(): float
    {
        return $this->weight * $this->node->getOutput();
    }

    /**
     * @param float $delta
     */
    public function changeWeight($delta)
    {
        $this->weight += $delta;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }
}
