<?php

namespace Box\Spout\Reader\XLSX\Creator;

use Box\Spout\Common\Helper\Escaper;
use Box\Spout\Reader\XLSX\Helper\CellValueFormatter;
use Box\Spout\Reader\XLSX\Manager\SharedStringsManager;
use Box\Spout\Reader\XLSX\Manager\StyleManager;

/**
 * Class HelperFactory
 * Factory to create helpers
 */
class HelperFactory extends \Box\Spout\Common\Creator\HelperFactory
{
    /**
     * @param SharedStringsManager $sharedStringsManager Manages shared strings
     * @param StyleManager $styleManager Manages styles
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     * @param bool $shouldUse1904Dates Whether date/time values should use a calendar starting in 1904 instead of 1900
     * @return CellValueFormatter
     */
    public function createCellValueFormatter($sharedStringsManager, $styleManager, $shouldFormatDates, $shouldUse1904Dates)
    {
        $escaper = $this->createStringsEscaper();

        return new CellValueFormatter($sharedStringsManager, $styleManager, $shouldFormatDates, $shouldUse1904Dates, $escaper);
    }

    /**
     * @return Escaper\XLSX
     */
    public function createStringsEscaper()
    {
        /* @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        return new Escaper\XLSX();
    }
}
