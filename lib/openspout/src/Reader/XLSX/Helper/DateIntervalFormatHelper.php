<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Helper;

use DateInterval;

final class DateIntervalFormatHelper
{
    /**
     * @see https://www.php.net/manual/en/dateinterval.format.php.
     */
    private const dateIntervalFormats = [
        'hh' => '%H',
        'h' => '%h',
        'mm' => '%I',
        'm' => '%i',
        'ss' => '%S',
        's' => '%s',
    ];

    /**
     * Excel stores durations as fractions of days (24h = 1).
     *
     * Only fills hours/minutes/seconds because those are the only values that we can format back out again.
     * Excel can also only handle those units as duration.
     * PHP's DateInterval is also quite limited - it will not automatically convert unit overflow
     *  (60 seconds are not converted to 1 minute).
     */
    public static function createDateIntervalFromHours(float $dayFractions): DateInterval
    {
        $time = abs($dayFractions) * 24; // convert to hours
        $hours = floor($time);
        $time = ($time - $hours) * 60;
        $minutes = (int) floor($time); // must cast to int for type strict compare below
        $time = ($time - $minutes) * 60;
        $seconds = (int) round($time); // must cast to int for type strict compare below

        // Bubble up rounding gain if we ended up with 60 seconds - disadvantage of using fraction of days for small durations:
        if (60 === $seconds) {
            $seconds = 0;
            ++$minutes;
        }
        if (60 === $minutes) {
            $minutes = 0;
            ++$hours;
        }

        $interval = new DateInterval("P0DT{$hours}H{$minutes}M{$seconds}S");
        if ($dayFractions < 0) {
            $interval->invert = 1;
        }

        return $interval;
    }

    public static function isDurationFormat(string $excelFormat): bool
    {
        // Only consider formats with leading brackets as valid duration formats (e.g. "[hh]:mm", "[mm]:ss", etc.):
        return 1 === preg_match('/^(\[hh?](:mm(:ss)?)?|\[mm?](:ss)?|\[ss?])$/', $excelFormat);
    }

    public static function toPHPDateIntervalFormat(string $excelDateFormat, ?string &$startUnit = null): string
    {
        $startUnit = null;
        $phpFormatParts = [];
        $formatParts = explode(':', str_replace(['[', ']'], '', $excelDateFormat));
        foreach ($formatParts as $formatPart) {
            $startUnit ??= $formatPart;
            $phpFormatParts[] = self::dateIntervalFormats[$formatPart];
        }

        // Add the minus sign for potential negative durations:
        return '%r'.implode(':', $phpFormatParts);
    }

    public static function formatDateInterval(DateInterval $dateInterval, string $excelDateFormat): string
    {
        $phpFormat = self::toPHPDateIntervalFormat($excelDateFormat, $startUnit);

        // We have to move the hours to minutes or hours+minutes to seconds if the format in Excel did the same:
        $startUnit = $startUnit[0]; // only take the first char
        $dateIntervalClone = clone $dateInterval;
        if ('m' === $startUnit) {
            $dateIntervalClone->i = $dateIntervalClone->i + $dateIntervalClone->h * 60;
            $dateIntervalClone->h = 0;
        } elseif ('s' === $startUnit) {
            $dateIntervalClone->s = $dateIntervalClone->s + $dateIntervalClone->i * 60 + $dateIntervalClone->h * 3600;
            $dateIntervalClone->i = 0;
            $dateIntervalClone->h = 0;
        }

        return $dateIntervalClone->format($phpFormat);
    }
}
