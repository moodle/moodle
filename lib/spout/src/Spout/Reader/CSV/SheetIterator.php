<?php

namespace Box\Spout\Reader\CSV;

use Box\Spout\Reader\IteratorInterface;

/**
 * Class SheetIterator
 * Iterate over CSV unique "sheet".
 */
class SheetIterator implements IteratorInterface
{
    /** @var Sheet The CSV unique "sheet" */
    protected $sheet;

    /** @var bool Whether the unique "sheet" has already been read */
    protected $hasReadUniqueSheet = false;

    /**
     * @param Sheet $sheet Corresponding unique sheet
     */
    public function __construct($sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     * Rewind the Iterator to the first element
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @return void
     */
    public function rewind() : void
    {
        $this->hasReadUniqueSheet = false;
    }

    /**
     * Checks if current position is valid
     * @see http://php.net/manual/en/iterator.valid.php
     *
     * @return bool
     */
    public function valid() : bool
    {
        return (!$this->hasReadUniqueSheet);
    }

    /**
     * Move forward to next element
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @return void
     */
    public function next() : void
    {
        $this->hasReadUniqueSheet = true;
    }

    /**
     * Return the current element
     * @see http://php.net/manual/en/iterator.current.php
     *
     * @return Sheet
     */
    public function current() : Sheet
    {
        return $this->sheet;
    }

    /**
     * Return the key of the current element
     * @see http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key() : int
    {
        return 1;
    }

    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    public function end() : void
    {
        // do nothing
    }
}
