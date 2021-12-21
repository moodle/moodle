<?php

namespace Box\Spout\Common\Helper;

/**
 * Class StringHelper
 * This class provides helper functions to work with strings and multibyte strings.
 *
 * @codeCoverageIgnore
 */
class StringHelper
{
    /** @var bool Whether the mbstring extension is loaded */
    protected $hasMbstringSupport;

    /** @var bool Whether the code is running with PHP7 or older versions */
    private $isRunningPhp7OrOlder;

    /** @var array Locale info, used for number formatting */
    private $localeInfo;

    /**
     *
     */
    public function __construct()
    {
        $this->hasMbstringSupport = \extension_loaded('mbstring');
        $this->isRunningPhp7OrOlder = \version_compare(PHP_VERSION, '8.0.0') < 0;
        $this->localeInfo = \localeconv();
    }

    /**
     * Returns the length of the given string.
     * It uses the multi-bytes function is available.
     * @see strlen
     * @see mb_strlen
     *
     * @param string $string
     * @return int
     */
    public function getStringLength($string)
    {
        return $this->hasMbstringSupport ? \mb_strlen($string) : \strlen($string);
    }

    /**
     * Returns the position of the first occurrence of the given character/substring within the given string.
     * It uses the multi-bytes function is available.
     * @see strpos
     * @see mb_strpos
     *
     * @param string $char Needle
     * @param string $string Haystack
     * @return int Char/substring's first occurrence position within the string if found (starts at 0) or -1 if not found
     */
    public function getCharFirstOccurrencePosition($char, $string)
    {
        $position = $this->hasMbstringSupport ? \mb_strpos($string, $char) : \strpos($string, $char);

        return ($position !== false) ? $position : -1;
    }

    /**
     * Returns the position of the last occurrence of the given character/substring within the given string.
     * It uses the multi-bytes function is available.
     * @see strrpos
     * @see mb_strrpos
     *
     * @param string $char Needle
     * @param string $string Haystack
     * @return int Char/substring's last occurrence position within the string if found (starts at 0) or -1 if not found
     */
    public function getCharLastOccurrencePosition($char, $string)
    {
        $position = $this->hasMbstringSupport ? \mb_strrpos($string, $char) : \strrpos($string, $char);

        return ($position !== false) ? $position : -1;
    }

    /**
     * Formats a numeric value (int or float) in a way that's compatible with the expected spreadsheet format.
     *
     * Formatting of float values is locale dependent in PHP < 8.
     * Thousands separators and decimal points vary from locale to locale (en_US: 12.34 vs pl_PL: 12,34).
     * However, float values must be formatted with no thousands separator and a "." as decimal point
     * to work properly. This method can be used to convert the value to the correct format before storing it.
     *
     * @see https://wiki.php.net/rfc/locale_independent_float_to_string for the changed behavior in PHP8.
     *
     * @param int|float $numericValue
     * @return string
     */
    public function formatNumericValue($numericValue)
    {
        if ($this->isRunningPhp7OrOlder && is_float($numericValue)) {
            return str_replace(
                [$this->localeInfo['thousands_sep'], $this->localeInfo['decimal_point']],
                ['', '.'],
                $numericValue
            );
        }

        return $numericValue;
    }
}
