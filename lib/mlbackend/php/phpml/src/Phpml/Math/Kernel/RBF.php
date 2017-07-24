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

    /**
     * @param float $gamma
     */
    public function __construct(float $gamma)
    {
        $this->gamma = $gamma;
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return float
     */
    public function compute($a, $b)
    {
        $score = 2 * Product::scalar($a, $b);
        $squares = Product::scalar($a, $a) + Product::scalar($b, $b);
        $result = exp(-$this->gamma * ($squares - $score));

        return $result;
    }
}
