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
     * @param int                     $nodesNumber
     * @param string                  $nodeClass
     * @param ActivationFunction|null $activationFunction
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $nodesNumber = 0, string $nodeClass = Neuron::class, ActivationFunction $activationFunction = null)
    {
        if (!in_array(Node::class, class_implements($nodeClass))) {
            throw InvalidArgumentException::invalidLayerNodeClass();
        }

        for ($i = 0; $i < $nodesNumber; ++$i) {
            $this->nodes[] = $this->createNode($nodeClass, $activationFunction);
        }
    }

    /**
     * @param string                  $nodeClass
     * @param ActivationFunction|null $activationFunction
     *
     * @return Neuron
     */
    private function createNode(string $nodeClass, ActivationFunction $activationFunction = null)
    {
        if (Neuron::class == $nodeClass) {
            return new Neuron($activationFunction);
        }

        return new $nodeClass();
    }

    /**
     * @param Node $node
     */
    public function addNode(Node $node)
    {
        $this->nodes[] = $node;
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}
