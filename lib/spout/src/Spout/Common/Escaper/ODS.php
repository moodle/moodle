<?php

namespace Box\Spout\Common\Escaper;

/**
 * Class ODS
 * Provides functions to escape and unescape data for ODS files
 *
 * @package Box\Spout\Common\Escaper
 */
class ODS implements EscaperInterface
{
    /**
     * Escapes the given string to make it compatible with XLSX
     *
     * @param string $string The string to escape
     * @return string The escaped string
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * Unescapes the given string to make it compatible with XLSX
     *
     * @param string $string The string to unescape
     * @return string The unescaped string
     */
    public function unescape($string)
    {
        return htmlspecialchars_decode($string, ENT_QUOTES);
    }
}
