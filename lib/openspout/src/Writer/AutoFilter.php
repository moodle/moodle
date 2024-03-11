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
        public readonly int $fromColumnIndex,
        public readonly int $fromRow,
        public readonly int $toColumnIndex,
        public readonly int $toRow
    ) {}
}
