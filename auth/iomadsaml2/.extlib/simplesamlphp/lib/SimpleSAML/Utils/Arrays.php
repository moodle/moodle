<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

/**
 * Array-related utility methods.
 *
 * @package SimpleSAMLphp
 */
class Arrays
{
    /**
     * Put a non-array variable into an array.
     *
     * @param mixed $data The data to place into an array.
     * @param mixed $index The index or key of the array where to place the data. Defaults to 0.
     *
     * @return array An array with one element containing $data, with key $index, or $data itself if it's already an
     *     array.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function arrayize($data, $index = 0)
    {
        return (is_array($data)) ? $data : [$index => $data];
    }


    /**
     * This function transposes a two-dimensional array, so that $a['k1']['k2'] becomes $a['k2']['k1'].
     *
     * @param array $array The two-dimensional array to transpose.
     *
     * @return mixed The transposed array, or false if $array is not a valid two-dimensional array.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     */
    public static function transpose($array)
    {
        if (!is_array($array)) {
            return false;
        }

        $ret = [];
        foreach ($array as $k1 => $a2) {
            if (!is_array($a2)) {
                return false;
            }

            foreach ($a2 as $k2 => $v) {
                if (!array_key_exists($k2, $ret)) {
                    $ret[$k2] = [];
                }
                $ret[$k2][$k1] = $v;
            }
        }
        return $ret;
    }
}
