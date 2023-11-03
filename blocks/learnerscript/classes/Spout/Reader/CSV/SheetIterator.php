<?php

namespace block_learnerscript\Spout\Reader\CSV;

use block_learnerscript\Spout\Reader\IteratorInterface;

/**
 * Class SheetIterator
 * Iterate over CSV unique "sheet".
 *
 * @package block_learnerscript\Spout\Reader\CSV
 */
class SheetIterator implements IteratorInterface
{
    /** @var \block_learnerscript\Spout\Reader\CSV\Sheet The CSV unique "sheet" */
    protected $sheet;

    /** @var bool Whether the unique "sheet" has already been read */
    protected $hasReadUniqueSheet = false;

    /**
     * @param resource $filePointer
     * @param string $fieldDelimiter Character that delimits fields
     * @param string $fieldEnclosure Character that enclose fields
     * @param string $encoding Encoding of the CSV file to be read
     * @param \block_learnerscript\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     */
    public function __construct($filePointer, $fieldDelimiter, $fieldEnclosure, $encoding, $endOfLineCharacter, $globalFunctionsHelper)
    {
        $this->sheet = new Sheet($filePointer, $fieldDelimiter, $fieldEnclosure, $encoding, $endOfLineCharacter, $globalFunctionsHelper);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void
     */
    public function rewind()
    {
        $this->hasReadUniqueSheet = false;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return boolean
     */
    public function valid()
    {
        return (!$this->hasReadUniqueSheet);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void
     */
    public function next()
    {
        $this->hasReadUniqueSheet = true;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return \block_learnerscript\Spout\Reader\CSV\Sheet
     */
    public function current()
    {
        return $this->sheet;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return int
     */
    public function key()
    {
        return 1;
    }

    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    public function end()
    {
        // do nothing
    }
}
