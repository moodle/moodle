<?php
/**
 * SCSSPHP
 *
 * @copyright 2012-2015 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.github.io/scssphp
 */

namespace Leafo\ScssPhp;

use Leafo\ScssPhp\Base\Range;

/**
 * Utilties
 *
 * @author Anthon Pang <anthon.pang@gmail.com>
 */
class Util
{
    /**
     * Asserts that `value` falls within `range` (inclusive), leaving
     * room for slight floating-point errors.
     *
     * @param string $name  The name of the value. Used in the error message.
     * @param Range  $range Range of values.
     * @param array  $value The value to check.
     * @param string $unit  The unit of the value. Used in error reporting.
     *
     * @return mixed `value` adjusted to fall within range, if it was outside by a floating-point margin.
     *
     * @throws \Exception
     */
    public static function checkRange($name, Range $range, $value, $unit = '')
    {
        $val = $value[1];
        $grace = new Range(-0.00001, 0.00001);

        if ($range->includes($val)) {
            return $val;
        }

        if ($grace->includes($val - $range->first)) {
            return $range->first;
        }

        if ($grace->includes($val - $range->last)) {
            return $range->last;
        }

        throw new \Exception("$name {$val} must be between {$range->first} and {$range->last}$unit");
    }
}
