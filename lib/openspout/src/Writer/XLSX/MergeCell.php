<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX;

/**
 * @internal
 */
final class MergeCell
{
    /**
     * @param 0|positive-int $sheetIndex
     * @param 0|positive-int $topLeftColumn
     * @param positive-int   $topLeftRow
     * @param 0|positive-int $bottomRightColumn
     * @param positive-int   $bottomRightRow
     */
    public function __construct(
        public int $sheetIndex,
        public int $topLeftColumn,
        public int $topLeftRow,
        public int $bottomRightColumn,
        public int $bottomRightRow,
    ) {
    }
}
