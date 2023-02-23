<?php

namespace OpenSpout\Reader\Common\Entity;

/**
 * Readers' options holder.
 */
abstract class Options
{
    // Common options
    public const SHOULD_FORMAT_DATES = 'shouldFormatDates';
    public const SHOULD_PRESERVE_EMPTY_ROWS = 'shouldPreserveEmptyRows';

    // CSV specific options
    public const FIELD_DELIMITER = 'fieldDelimiter';
    public const FIELD_ENCLOSURE = 'fieldEnclosure';
    public const ENCODING = 'encoding';

    // XLSX specific options
    public const TEMP_FOLDER = 'tempFolder';
    public const SHOULD_USE_1904_DATES = 'shouldUse1904Dates';
}
