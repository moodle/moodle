<?php

/**
 * Time-related utility methods.
 *
 * @package SimpleSAMLphp
 */

declare(strict_types=1);

namespace SimpleSAML\Utils;

use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Logger;

class Time
{
    /**
     * Whether the timezone has been initialized or not.
     *
     * @var bool
     */
    private static $tz_initialized = false;


    /**
     * This function generates a timestamp on the form used by the SAML protocols.
     *
     * @param int $instant The time the timestamp should represent. Defaults to current time.
     *
     * @return string The timestamp.
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     */
    public static function generateTimestamp($instant = null)
    {
        if ($instant === null) {
            $instant = time();
        }
        return gmdate('Y-m-d\TH:i:s\Z', $instant);
    }


    /**
     * Initialize the timezone.
     *
     * This function should be called before any calls to date().
     *
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     *
     * @throws \SimpleSAML\Error\Exception If the timezone set in the configuration is invalid.
     *
     * @return void
     */
    public static function initTimezone()
    {
        if (self::$tz_initialized) {
            return;
        }

        $globalConfig = Configuration::getInstance();

        $timezone = $globalConfig->getString('timezone', null);
        if ($timezone !== null) {
            if (!date_default_timezone_set($timezone)) {
                throw new Error\Exception('Invalid timezone set in the "timezone" option in config.php.');
            }
            self::$tz_initialized = true;
            return;
        }
        // we don't have a timezone configured

        Logger::maskErrors(E_ALL);
        $serverTimezone = date_default_timezone_get();
        Logger::popErrorMask();

        // set the timezone to the default
        date_default_timezone_set($serverTimezone);
        self::$tz_initialized = true;
    }


    /**
     * Interpret a ISO8601 duration value relative to a given timestamp. Please note no fractions are allowed, neither
     * durations specified in the formats PYYYYMMDDThhmmss nor P[YYYY]-[MM]-[DD]T[hh]:[mm]:[ss].
     *
     * @param string $duration The duration, as a string.
     * @param int    $timestamp The unix timestamp we should apply the duration to. Optional, default to the current
     *     time.
     *
     * @return int The new timestamp, after the duration is applied.
     * @throws \InvalidArgumentException If $duration is not a valid ISO 8601 duration or if the input parameters do
     *     not have the right data types.
     */
    public static function parseDuration($duration, $timestamp = null)
    {
        if (!(is_string($duration) && (is_int($timestamp) || is_null($timestamp)))) {
            throw new \InvalidArgumentException('Invalid input parameters');
        }

        // parse the duration. We use a very strict pattern
        $durationRegEx = '#^(-?)P(?:(?:(?:(\\d+)Y)?(?:(\\d+)M)?(?:(\\d+)D)?(?:T(?:(\\d+)H)?(?:(\\d+)M)?(?:(\\d+)' .
            '(?:[.,]\d+)?S)?)?)|(?:(\\d+)W))$#D';
        if (!preg_match($durationRegEx, $duration, $matches)) {
            throw new \InvalidArgumentException('Invalid ISO 8601 duration: ' . $duration);
        }

        $durYears = (empty($matches[2]) ? 0 : (int) $matches[2]);
        $durMonths = (empty($matches[3]) ? 0 : (int) $matches[3]);
        $durDays = (empty($matches[4]) ? 0 : (int) $matches[4]);
        $durHours = (empty($matches[5]) ? 0 : (int) $matches[5]);
        $durMinutes = (empty($matches[6]) ? 0 : (int) $matches[6]);
        $durSeconds = (empty($matches[7]) ? 0 : (int) $matches[7]);
        $durWeeks = (empty($matches[8]) ? 0 : (int) $matches[8]);

        if (!empty($matches[1])) {
            // negative
            $durYears = -$durYears;
            $durMonths = -$durMonths;
            $durDays = -$durDays;
            $durHours = -$durHours;
            $durMinutes = -$durMinutes;
            $durSeconds = -$durSeconds;
            $durWeeks = -$durWeeks;
        }

        if ($timestamp === null) {
            $timestamp = time();
        }

        if ($durYears !== 0 || $durMonths !== 0) {
            /* Special handling of months and years, since they aren't a specific interval, but
             * instead depend on the current time.
             */

            /* We need the year and month from the timestamp. Unfortunately, PHP doesn't have the
             * gmtime function. Instead we use the gmdate function, and split the result.
             */
            $yearmonth = explode(':', gmdate('Y:n', $timestamp));
            $year = (int) ($yearmonth[0]);
            $month = (int) ($yearmonth[1]);

            // remove the year and month from the timestamp
            $timestamp -= gmmktime(0, 0, 0, $month, 1, $year);

            // add years and months, and normalize the numbers afterwards
            $year += $durYears;
            $month += $durMonths;
            while ($month > 12) {
                $year += 1;
                $month -= 12;
            }
            while ($month < 1) {
                $year -= 1;
                $month += 12;
            }

            // add year and month back into timestamp
            $timestamp += gmmktime(0, 0, 0, $month, 1, $year);
        }

        // add the other elements
        $timestamp += $durWeeks * 7 * 24 * 60 * 60;
        $timestamp += $durDays * 24 * 60 * 60;
        $timestamp += $durHours * 60 * 60;
        $timestamp += $durMinutes * 60;
        $timestamp += $durSeconds;

        return $timestamp;
    }
}
