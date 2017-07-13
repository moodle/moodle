<?php

namespace Box\Spout\Common\Helper;

/**
 * Class StringHelper
 * This class provides helper functions to work with strings and multibyte strings.
 *
 * @codeCoverageIgnore
 *
 * @package Box\Spout\Common\Helper
 */
class StringHelper
{
    /** @var bool Whether the mbstring extension is loaded */
    protected $hasMbstringSupport;

    /**
     *
     */
    public function __construct()
    {
        $this->hasMbstringSupport = extension_loaded('mbstring');
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
        return $this->hasMbstringSupport ? mb_strlen($string) : strlen($string);
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
        $position = $this->hasMbstringSupport ? mb_strpos($string, $char) : strpos($string, $char);
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
        $position = $this->hasMbstringSupport ? mb_strrpos($string, $char) : strrpos($string, $char);
        return ($position !== false) ? $position : -1;
    }
}
