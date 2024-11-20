<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common;

/**
 * @internal
 */
final class ColumnWidth
{
    /**
     * @param positive-int $start
     * @param positive-int $end
     */
    public function __construct(
        public readonly int $start,
        public readonly int $end,
        public readonly float $width,
    ) {}
}
