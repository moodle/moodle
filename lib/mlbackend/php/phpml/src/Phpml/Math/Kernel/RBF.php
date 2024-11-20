<?php

declare(strict_types=1);

namespace Phpml\Math\Kernel;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Kernel;
use Phpml\Math\Product;

class RBF implements Kernel
{
    /**
     * @var float
     */
    private $gamma;

    public function __construct(float $gamma)
    {
        $this->gamma = $gamma;
    }

    public function compute($a, $b): float
    {
        if (!is_array($a) || !is_array($b)) {
            throw new InvalidArgumentException(sprintf('Arguments of %s must be arrays', __METHOD__));
        }

        $score = 2 * Product::scalar($a, $b);
        $squares = Product::scalar($a, $a) + Product::scalar($b, $b);

        return exp(-$this->gamma * ($squares - $score));
    }
}
