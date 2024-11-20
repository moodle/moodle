<?php

namespace Gettext\Utils;

use Gettext\Translations;

/**
 * Trait to provide the functionality of extracting headers.
 */
trait HeadersExtractorTrait
{
    /**
     * Add the headers found to the translations instance.
     *
     * @param string       $headers
     * @param Translations $translations
     *
     * @return array
     */
    protected static function extractHeaders($headers, Translations $translations)
    {
        $headers = explode("\n", $headers);
        $currentHeader = null;

        foreach ($headers as $line) {
            $line = static::convertString($line);

            if ($line === '') {
                continue;
            }

            if (static::isHeaderDefinition($line)) {
                $header = array_map('trim', explode(':', $line, 2));
                $currentHeader = $header[0];
                $translations->setHeader($currentHeader, $header[1]);
            } else {
                $entry = $translations->getHeader($currentHeader);
                $translations->setHeader($currentHeader, $entry.$line);
            }
        }
    }

    /**
     * Checks if it is a header definition line. Useful for distguishing between header definitions
     * and possible continuations of a header entry.
     *
     * @param string $line Line to parse
     *
     * @return bool
     */
    protected static function isHeaderDefinition($line)
    {
        return (bool) preg_match('/^[\w-]+:/', $line);
    }

    /**
     * Normalize a string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function convertString($value)
    {
        return $value;
    }
}
