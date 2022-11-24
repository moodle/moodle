<?php

namespace OpenSpout\Reader\ODS\Creator;

use OpenSpout\Reader\ODS\Helper\CellValueFormatter;
use OpenSpout\Reader\ODS\Helper\SettingsHelper;

/**
 * Factory to create helpers.
 */
class HelperFactory extends \OpenSpout\Common\Creator\HelperFactory
{
    /**
     * @param bool $shouldFormatDates Whether date/time values should be returned as PHP objects or be formatted as strings
     *
     * @return CellValueFormatter
     */
    public function createCellValueFormatter($shouldFormatDates)
    {
        $escaper = $this->createStringsEscaper();

        return new CellValueFormatter($shouldFormatDates, $escaper);
    }

    /**
     * @param InternalEntityFactory $entityFactory
     *
     * @return SettingsHelper
     */
    public function createSettingsHelper($entityFactory)
    {
        return new SettingsHelper($entityFactory);
    }

    /**
     * @return \OpenSpout\Common\Helper\Escaper\ODS
     */
    public function createStringsEscaper()
    {
        // @noinspection PhpUnnecessaryFullyQualifiedNameInspection
        return new \OpenSpout\Common\Helper\Escaper\ODS();
    }
}
