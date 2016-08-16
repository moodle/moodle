<?php

namespace Box\Spout\Common\Escaper;

/**
 * Class CSV
 * Provides functions to escape and unescape data for CSV files
 *
 * @package Box\Spout\Common\Escaper
 */
class CSV implements EscaperInterface
{
    /**
     * Escapes the given string to make it compatible with CSV
     *
     * @codeCoverageIgnore
     *
     * @param string $string The string to escape
     * @return string The escaped string
     */
    public function escape($string)
    {
        return $string;
    }

    /**
     * Unescapes the given string to make it compatible with CSV
     *
     * @codeCoverageIgnore
     *
     * @param string $string The string to unescape
     * @return string The unescaped string
     */
    public function unescape($string)
    {
        return $string;
    }
}
