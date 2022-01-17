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
     * @param float|null $weight
     */
    public function __construct(Node $node, ?float $weight = null)
    {
        $this->node = $node;
        $this->weight = $weight ?? $this->generateRandomWeight();
    }

    public function getOutput(): float
    {
        return $this->weight * $this->node->getOutput();
    }

    public function changeWeight(float $delta): void
    {
        $this->weight += $delta;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    protected function generateRandomWeight(): float
    {
        return (1 / random_int(5, 25) * random_int(0, 1)) > 0 ? -1 : 1;
    }
}
