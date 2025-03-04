<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

/**
 * @template T of RowIteratorInterface
 *
 * @extends SheetInterface<T>
 */
interface SheetWithMergeCellsInterface extends SheetInterface
{
    /**
     * @return list<string> Merge cells list ["C7:E7", "A9:D10"]
     */
    public function getMergeCells(): array;
}
