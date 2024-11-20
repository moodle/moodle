<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork;

use Phpml\Exception\InvalidArgumentException;
use Phpml\NeuralNetwork\Node\Neuron;

class Layer
{
    /**
     * @var Node[]
     */
    private $nodes = [];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(int $nodesNumber = 0, string $nodeClass = Neuron::class, ?ActivationFunction $activationFunction = null)
    {
        if (!in_array(Node::class, class_implements($nodeClass), true)) {
            throw new InvalidArgumentException('Layer node class must implement Node interface');
        }

        for ($i = 0; $i < $nodesNumber; ++$i) {
            $this->nodes[] = $this->createNode($nodeClass, $activationFunction);
        }
    }

    public function addNode(Node $node): void
    {
        $this->nodes[] = $node;
    }

    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    private function createNode(string $nodeClass, ?ActivationFunction $activationFunction = null): Node
    {
        if ($nodeClass === Neuron::class) {
            return new Neuron($activationFunction);
        }

        return new $nodeClass();
    }
}
