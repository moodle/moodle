<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork;

interface Network
{
    /**
     * @param mixed $input
     *
     * @return self
     */
    public function setInput($input);

    /**
     * @return array
     */
    public function getOutput(): array;

    /**
     * @param Layer $layer
     */
    public function addLayer(Layer $layer);

    /**
     * @return Layer[]
     */
    public function getLayers(): array;
}
