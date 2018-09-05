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
    protected $layers;

    /**
     * @param Layer $layer
     */
    public function addLayer(Layer $layer)
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

    /**
     * @return void
     */
    public function removeLayers()
    {
        unset($this->layers);
    }

    /**
     * @return Layer
     */
    public function getOutputLayer(): Layer
    {
        return $this->layers[count($this->layers) - 1];
    }

    /**
     * @return array
     */
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
     *
     * @return $this
     */
    public function setInput($input)
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
