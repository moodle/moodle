<?php

namespace Gettext\Utils;

/*
 * Trait to provide the functionality of read/write csv.
 */
trait CsvTrait
{
    protected static $csvEscapeChar;

    /**
     * Check whether support the escape_char argument to fgetcsv/fputcsv or not
     *
     * @return bool
     */
    protected static function supportsCsvEscapeChar()
    {
        if (static::$csvEscapeChar === null) {
            static::$csvEscapeChar = version_compare(PHP_VERSION, '5.5.4') >= 0;
        }

        return static::$csvEscapeChar;
    }

    /**
     * @param resource $handle
     * @param array $options
     *
     * @return array
     */
    protected static function fgetcsv($handle, $options)
    {
        if (static::supportsCsvEscapeChar()) {
            return fgetcsv($handle, 0, $options['delimiter'], $options['enclosure'], $options['escape_char']);
        }

        return fgetcsv($handle, 0, $options['delimiter'], $options['enclosure']);
    }

    /**
     * @param resource $handle
     * @param array $fields
     * @param array $options
     *
     * @return bool|int
     */
    protected static function fputcsv($handle, $fields, $options)
    {
        if (static::supportsCsvEscapeChar()) {
            return fputcsv($handle, $fields, $options['delimiter'], $options['enclosure'], $options['escape_char']);
        }

        return fputcsv($handle, $fields, $options['delimiter'], $options['enclosure']);
    }
}
