<?php

namespace OpenSpout\Reader\XLSX\Creator;

use OpenSpout\Common\Helper\Escaper;
use OpenSpout\Reader\XLSX\Helper\CellValueFormatter;
use OpenSpout\Reader\XLSX\Manager\SharedStringsManager;
use OpenSpout\Reader\XLSX\Manager\StyleManager;

/**
 * Factory to create helpers.
 */
class HelperFactory extends \OpenSpout\Common\Creator\HelperFactory
{
    /**
     * @param SharedStringsManager $sharedStringsManager Manages shared strings
     * @param StyleManager         $styleManager         Manages styles
     * @param bool                 $shouldFormatDates    Whether date/time values should be returned as PHP objects or be formatted as strings
     * @param bool                 $shouldUse1904Dates   Whether date/time values should use a calendar starting in 1904 instead of 1900
     *
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
        // @noinspection PhpUnnecessaryFullyQualifiedNameInspection
        return new Escaper\XLSX();
    }
}
