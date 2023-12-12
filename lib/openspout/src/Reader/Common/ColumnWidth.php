<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Common;

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
        public int $start,
        public int $end,
        public float $width,
    ) {
    }
}
