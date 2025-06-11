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
        public readonly int $sheetIndex,
        public readonly int $topLeftColumn,
        public readonly int $topLeftRow,
        public readonly int $bottomRightColumn,
        public readonly int $bottomRightRow,
    ) {}
}
