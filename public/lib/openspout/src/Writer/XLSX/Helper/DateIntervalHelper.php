<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Helper;

use DateInterval;

/**
 * @internal
 */
final class DateIntervalHelper
{
    /**
     * Excel stores time durations as fractions of days:
     *   A value of 1 equals 24 hours, a value of 0.5 equals 12 hours, etc.
     *
     * Note: Excel can only display durations up to hours and it will only auto-detect this value as an actual duration
     *     if the value is less than 1, even if you specify a custom format such als "hh:mm:ss".
     *   To force the display into a duration format, you have to use the brackets around the left most unit
     *     of the format, e.g. "[h]:mm" or "[mm]:ss", which tells Excel to use up all the remaining time exceeding
     *     this unit and put it in this last unit.
     */
    public static function toExcel(DateInterval $interval): float
    {
        // For years and months we can only use the respective average of days here - this won't be accurate, but the
        // DateInterval doesn't give us more details on those:
        $days = $interval->y * 365.25
            + $interval->m * 30.437
            + $interval->d
            + $interval->h / 24
            + $interval->i / 24 / 60
            + $interval->s / 24 / 60 / 60;

        if (1 === $interval->invert) {
            $days *= -1;
        }

        return $days;
    }
}
