<?php

declare(strict_types=1);

namespace OpenSpout\Writer;

/**
 * @readonly
 */
final class AutoFilter
{
    /**
     * @param 0|positive-int $fromColumnIndex
     * @param positive-int   $fromRow
     * @param 0|positive-int $toColumnIndex
     * @param positive-int   $toRow
     */
    public function __construct(
        public int $fromColumnIndex,
        public int $fromRow,
        public int $toColumnIndex,
        public int $toRow
    ) {
    }
}
