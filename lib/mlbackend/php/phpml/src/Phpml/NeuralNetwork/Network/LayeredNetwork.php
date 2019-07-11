<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork\Network;

use Phpml\NeuralNetwork\Layer;
use Phpml\NeuralNetwork\Network;
use Phpml\NeuralNetwork\Node\Input;
use Phpml\NeuralNetwork\Node\Neuron;

abstract class LayeredNetwork implements Network
{
    /**
     * @var Layer[]
     */
    protected $layers = [];

    public function addLayer(Layer $layer): void
    {
        $this->layers[] = $layer;
    }

    /**
     * @return Layer[]
     */
    public function getLayers(): array
    {
        return $this->layers;
    }

    public function removeLayers(): void
    {
        unset($this->layers);
    }

    public function getOutputLayer(): Layer
    {
        return $this->layers[count($this->layers) - 1];
    }

    public function getOutput(): array
    {
        $result = [];
        foreach ($this->getOutputLayer()->getNodes() as $neuron) {
            $result[] = $neuron->getOutput();
        }

        return $result;
    }

    /**
     * @param mixed $input
     */
    public function setInput($input): Network
    {
        $firstLayer = $this->layers[0];

        foreach ($firstLayer->getNodes() as $key => $neuron) {
            if ($neuron instanceof Input) {
                $neuron->setInput($input[$key]);
            }
        }

        foreach ($this->getLayers() as $layer) {
            foreach ($layer->getNodes() as $node) {
                if ($node instanceof Neuron) {
                    $node->reset();
                }
            }
        }

        return $this;
    }
}
