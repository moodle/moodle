<?php

declare(strict_types=1);

namespace Phpml\Math;

class Product
{
    /**
     * @param array $a
     * @param array $b
     *
     * @return mixed
     */
    public static function scalar(array $a, array $b)
    {
        $product = 0;
        foreach ($a as $index => $value) {
            if (is_numeric($value) && is_numeric($b[$index])) {
                $product += $value * $b[$index];
            }
        }

        return $product;
    }
}
