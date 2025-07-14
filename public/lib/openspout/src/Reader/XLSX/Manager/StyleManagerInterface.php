<?php

declare(strict_types=1);

namespace OpenSpout\Reader\XLSX\Manager;

/**
 * @internal
 */
interface StyleManagerInterface
{
    /**
     * Returns whether the style with the given ID should consider
     * numeric values as timestamps and format the cell as a date.
     *
     * @param int $styleId Zero-based style ID
     *
     * @return bool Whether the cell with the given cell should display a date instead of a numeric value
     */
    public function shouldFormatNumericValueAsDate(int $styleId): bool;

    /**
     * Returns the format as defined in "styles.xml" of the given style.
     * NOTE: It is assumed that the style DOES have a number format associated to it.
     *
     * @param int $styleId Zero-based style ID
     *
     * @return string The number format code associated with the given style
     */
    public function getNumberFormatCode(int $styleId): string;
}
