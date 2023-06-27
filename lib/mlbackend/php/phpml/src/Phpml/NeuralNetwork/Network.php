<?php

declare(strict_types=1);

namespace Phpml\NeuralNetwork;

interface Network
{
    /**
     * @param mixed $input
     */
    public function setInput($input): self;

    public function getOutput(): array;

    public function addLayer(Layer $layer): void;

    /**
     * @return Layer[]
     */
    public function getLayers(): array;
}
