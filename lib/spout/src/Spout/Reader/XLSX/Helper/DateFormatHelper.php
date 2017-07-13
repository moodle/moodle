<?php

namespace Box\Spout\Reader\XLSX\Helper;

/**
 * Class DateFormatHelper
 * This class provides helper functions to format Excel dates
 *
 * @package Box\Spout\Reader\XLSX\Helper
 */
class DateFormatHelper
{
    const KEY_GENERAL = 'general';
    const KEY_HOUR_12 = '12h';
    const KEY_HOUR_24 = '24h';

    /**
     * This map is used to replace Excel format characters by their PHP equivalent.
     * Keys should be ordered from longest to smallest.
     *
     * @var array Mapping between Excel format characters and PHP format characters
     */
    private static $excelDateFormatToPHPDateFormatMapping = [
        self::KEY_GENERAL => [
            // Time
            'am/pm' => 'A',  // Uppercase Ante meridiem and Post meridiem
            ':mm'   => ':i', // Minutes with leading zeros - if preceded by a ":" (otherwise month)
            'mm:'   => 'i:', // Minutes with leading zeros - if followed by a ":" (otherwise month)
            'ss'    => 's',  // Seconds, with leading zeros
            '.s'    => '',   // Ignore (fractional seconds format does not exist in PHP)

            // Date
            'e'     => 'Y',  // Full numeric representation of a year, 4 digits
            'yyyy'  => 'Y',  // Full numeric representation of a year, 4 digits
            'yy'    => 'y',  // Two digit representation of a year
            'mmmmm' => 'M',  // Short textual representation of a month, three letters ("mmmmm" should only contain the 1st letter...)
            'mmmm'  => 'F',  // Full textual representation of a month
            'mmm'   => 'M',  // Short textual representation of a month, three letters
            'mm'    => 'm',  // Numeric representation of a month, with leading zeros
            'm'     => 'n',  // Numeric representation of a month, without leading zeros
            'dddd'  => 'l',  // Full textual representation of the day of the week
            'ddd'   => 'D',  // Textual representation of a day, three letters
            'dd'    => 'd',  // Day of the month, 2 digits with leading zeros
            'd'     => 'j',  // Day of the month without leading zeros
        ],
        self::KEY_HOUR_12 => [
            'hh'    => 'h',  // 12-hour format of an hour without leading zeros
            'h'     => 'g',  // 12-hour format of an hour without leading zeros
        ],
        self::KEY_HOUR_24 => [
            'hh'    => 'H',  // 24-hour hours with leading zero
            'h'     => 'G',  // 24-hour format of an hour without leading zeros
        ],
    ];

    /**
     * Converts the given Excel date format to a format understandable by the PHP date function.
     *
     * @param string $excelDateFormat Excel date format
     * @return string PHP date format (as defined here: http://php.net/manual/en/function.date.php)
     */
    public static function toPHPDateFormat($excelDateFormat)
    {
        // Remove brackets potentially present at the beginning of the format string
        $dateFormat = preg_replace('/^(\[\$[^\]]+?\])/i', '', $excelDateFormat);

        // Double quotes are used to escape characters that must not be interpreted.
        // For instance, ["Day " dd] should result in "Day 13" and we should not try to interpret "D", "a", "y"
        // By exploding the format string using double quote as a delimiter, we can get all parts
        // that must be transformed (even indexes) and all parts that must not be (odd indexes).
        $dateFormatParts = explode('"', $dateFormat);

        foreach ($dateFormatParts as $partIndex => $dateFormatPart) {
            // do not look at odd indexes
            if ($partIndex % 2 === 1) {
                continue;
            }

            // Make sure all characters are lowercase, as the mapping table is using lowercase characters
            $transformedPart = strtolower($dateFormatPart);

            // Remove escapes related to non-format characters
            $transformedPart = str_replace('\\', '', $transformedPart);

            // Apply general transformation first...
            $transformedPart = strtr($transformedPart, self::$excelDateFormatToPHPDateFormatMapping[self::KEY_GENERAL]);

            // ... then apply hour transformation, for 12-hour or 24-hour format
            if (self::has12HourFormatMarker($dateFormatPart)) {
                $transformedPart = strtr($transformedPart, self::$excelDateFormatToPHPDateFormatMapping[self::KEY_HOUR_12]);
            } else {
                $transformedPart = strtr($transformedPart, self::$excelDateFormatToPHPDateFormatMapping[self::KEY_HOUR_24]);
            }

            // overwrite the parts array with the new transformed part
            $dateFormatParts[$partIndex] = $transformedPart;
        }

        // Merge all transformed parts back together
        $phpDateFormat = implode('"', $dateFormatParts);

        // Finally, to have the date format compatible with the DateTime::format() function, we need to escape
        // all characters that are inside double quotes (and double quotes must be removed).
        // For instance, ["Day " dd] should become [\D\a\y\ dd]
        $phpDateFormat = preg_replace_callback('/"(.+?)"/', function($matches) {
            $stringToEscape = $matches[1];
            $letters = preg_split('//u', $stringToEscape, -1, PREG_SPLIT_NO_EMPTY);
            return '\\' . implode('\\', $letters);
        }, $phpDateFormat);

        return $phpDateFormat;
    }

    /**
     * @param string $excelDateFormat Date format as defined by Excel
     * @return bool Whether the given date format has the 12-hour format marker
     */
    private static function has12HourFormatMarker($excelDateFormat)
    {
        return (stripos($excelDateFormat, 'am/pm') !== false);
    }
}
