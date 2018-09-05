<?php

declare(strict_types=1);

namespace Phpml\Math\Statistic;

use Phpml\Exception\InvalidArgumentException;

class Covariance
{
    /**
     * Calculates covariance from two given arrays, x and y, respectively
     *
     * @param array $x
     * @param array $y
     * @param bool  $sample
     * @param float $meanX
     * @param float $meanY
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public static function fromXYArrays(array $x, array $y, $sample = true, float $meanX = null, float $meanY = null)
    {
        if (empty($x) || empty($y)) {
            throw InvalidArgumentException::arrayCantBeEmpty();
        }

        $n = count($x);
        if ($sample && $n === 1) {
            throw InvalidArgumentException::arraySizeToSmall(2);
        }

        if ($meanX === null) {
            $meanX = Mean::arithmetic($x);
        }

        if ($meanY === null) {
            $meanY = Mean::arithmetic($y);
        }

        $sum = 0.0;
        foreach ($x as $index => $xi) {
            $yi = $y[$index];
            $sum += ($xi - $meanX) * ($yi - $meanY);
        }

        if ($sample) {
            --$n;
        }

        return $sum / $n;
    }

    /**
     * Calculates covariance of two dimensions, i and k in the given data.
     *
     * @param array $data
     * @param int   $i
     * @param int   $k
     * @param bool  $sample
     * @param float $meanX
     * @param float $meanY
     *
     * @return float
     *
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public static function fromDataset(array $data, int $i, int $k, bool $sample = true, float $meanX = null, float $meanY = null)
    {
        if (empty($data)) {
            throw InvalidArgumentException::arrayCantBeEmpty();
        }

        $n = count($data);
        if ($sample && $n === 1) {
            throw InvalidArgumentException::arraySizeToSmall(2);
        }

        if ($i < 0 || $k < 0 || $i >= $n || $k >= $n) {
            throw new \Exception("Given indices i and k do not match with the dimensionality of data");
        }

        if ($meanX === null || $meanY === null) {
            $x = array_column($data, $i);
            $y = array_column($data, $k);

            $meanX = Mean::arithmetic($x);
            $meanY = Mean::arithmetic($y);
            $sum = 0.0;
            foreach ($x as $index => $xi) {
                $yi = $y[$index];
                $sum += ($xi - $meanX) * ($yi - $meanY);
            }
        } else {
            // In the case, whole dataset given along with dimension indices, i and k,
            // we would like to avoid getting column data with array_column and operate
            // over this extra copy of column data for memory efficiency purposes.
            //
            // Instead we traverse through the whole data and get what we actually need
            // without copying the data. This way, memory use will be reduced
            // with a slight cost of CPU utilization.
            $sum = 0.0;
            foreach ($data as $row) {
                $val = [];
                foreach ($row as $index => $col) {
                    if ($index == $i) {
                        $val[0] = $col - $meanX;
                    }
                    if ($index == $k) {
                        $val[1] = $col - $meanY;
                    }
                }
                $sum += $val[0] * $val[1];
            }
        }

        if ($sample) {
            --$n;
        }

        return $sum / $n;
    }

    /**
     * Returns the covariance matrix of n-dimensional data
     *
     * @param array      $data
     * @param array|null $means
     *
     * @return array
     */
    public static function covarianceMatrix(array $data, array $means = null)
    {
        $n = count($data[0]);

        if ($means === null) {
            $means = [];
            for ($i = 0; $i < $n; ++$i) {
                $means[] = Mean::arithmetic(array_column($data, $i));
            }
        }

        $cov = [];
        for ($i = 0; $i < $n; ++$i) {
            for ($k = 0; $k < $n; ++$k) {
                if ($i > $k) {
                    $cov[$i][$k] = $cov[$k][$i];
                } else {
                    $cov[$i][$k] = self::fromDataset(
                        $data, $i, $k, true, $means[$i], $means[$k]
                    );
                }
            }
        }

        return $cov;
    }
}
