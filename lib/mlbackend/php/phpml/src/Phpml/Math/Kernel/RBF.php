<?php

declare(strict_types=1);

namespace Phpml\Math\Kernel;

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

    /**
     * @param array $a
     * @param array $b
     */
    public function compute($a, $b): float
    {
        $score = 2 * Product::scalar($a, $b);
        $squares = Product::scalar($a, $a) + Product::scalar($b, $b);

        return exp(-$this->gamma * ($squares - $score));
    }
}
