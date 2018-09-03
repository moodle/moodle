<?php

namespace Box\Spout\Reader\Common;

/**
 * Class ReaderOptions
 * Readers' common options
 *
 * @package Box\Spout\Reader\Common
 */
class ReaderOptions
{
    /** @var bool Whether date/time values should be returned as PHP objects or be formatted as strings */
    protected $shouldFormatDates = false;

    /** @var bool Whether empty rows should be returned or skipped */
    protected $shouldPreserveEmptyRows = false;

    /**
     * @return bool Whether date/time values should be returned as PHP objects or be formatted as strings.
     */
    public function shouldFormatDates()
    {
        return $this->shouldFormatDates;
    }

    /**
     * Sets whether date/time values should be returned as PHP objects or be formatted as strings.
     *
     * @param bool $shouldFormatDates
     * @return ReaderOptions
     */
    public function setShouldFormatDates($shouldFormatDates)
    {
        $this->shouldFormatDates = $shouldFormatDates;
        return $this;
    }

    /**
     * @return bool Whether empty rows should be returned or skipped.
     */
    public function shouldPreserveEmptyRows()
    {
        return $this->shouldPreserveEmptyRows;
    }

    /**
     * Sets whether empty rows should be returned or skipped.
     *
     * @param bool $shouldPreserveEmptyRows
     * @return ReaderOptions
     */
    public function setShouldPreserveEmptyRows($shouldPreserveEmptyRows)
    {
        $this->shouldPreserveEmptyRows = $shouldPreserveEmptyRows;
        return $this;
    }
}
