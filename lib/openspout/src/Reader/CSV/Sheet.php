<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Reader\SheetInterface;

/**
 * @implements SheetInterface<RowIterator>
 */
final class Sheet implements SheetInterface
{
    /** @var RowIterator To iterate over the CSV's rows */
    private RowIterator $rowIterator;

    /**
     * @param RowIterator $rowIterator Corresponding row iterator
     */
    public function __construct(RowIterator $rowIterator)
    {
        $this->rowIterator = $rowIterator;
    }

    public function getRowIterator(): RowIterator
    {
        return $this->rowIterator;
    }

    /**
     * @return int Index of the sheet
     */
    public function getIndex(): int
    {
        return 0;
    }

    /**
     * @return string Name of the sheet - empty string since CSV does not support that
     */
    public function getName(): string
    {
        return '';
    }

    /**
     * @return bool Always TRUE as there is only one sheet
     */
    public function isActive(): bool
    {
        return true;
    }
}
