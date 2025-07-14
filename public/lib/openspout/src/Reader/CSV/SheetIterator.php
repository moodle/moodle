<?php

declare(strict_types=1);

namespace OpenSpout\Reader\CSV;

use OpenSpout\Reader\SheetIteratorInterface;

/**
 * @implements SheetIteratorInterface<Sheet>
 */
final class SheetIterator implements SheetIteratorInterface
{
    /** @var Sheet The CSV unique "sheet" */
    private readonly Sheet $sheet;

    /** @var bool Whether the unique "sheet" has already been read */
    private bool $hasReadUniqueSheet = false;

    /**
     * @param Sheet $sheet Corresponding unique sheet
     */
    public function __construct(Sheet $sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind(): void
    {
        $this->hasReadUniqueSheet = false;
    }

    /**
     * Checks if current position is valid.
     *
     * @see http://php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        return !$this->hasReadUniqueSheet;
    }

    /**
     * Move forward to next element.
     *
     * @see http://php.net/manual/en/iterator.next.php
     */
    public function next(): void
    {
        $this->hasReadUniqueSheet = true;
    }

    /**
     * Return the current element.
     *
     * @see http://php.net/manual/en/iterator.current.php
     */
    public function current(): Sheet
    {
        return $this->sheet;
    }

    /**
     * Return the key of the current element.
     *
     * @see http://php.net/manual/en/iterator.key.php
     */
    public function key(): int
    {
        return 1;
    }
}
