<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

/**
 * @template T of RowIteratorInterface
 */
interface SheetInterface
{
    /**
     * @return T iterator to iterate over the sheet's rows
     */
    public function getRowIterator(): RowIteratorInterface;

    /**
     * @return int Index of the sheet
     */
    public function getIndex(): int;

    /**
     * @return string Name of the sheet
     */
    public function getName(): string;

    /**
     * @return bool Whether the sheet was defined as active
     */
    public function isActive(): bool;
}
