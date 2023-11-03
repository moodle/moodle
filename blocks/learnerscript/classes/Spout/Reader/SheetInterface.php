<?php

namespace block_learnerscript\Spout\Reader;

/**
 * Interface SheetInterface
 *
 * @package block_learnerscript\Spout\Reader
 */
interface SheetInterface
{
    /**
     * Returns an iterator to iterate over the sheet's rows.
     *
     * @return \Iterator
     */
    public function getRowIterator();
}
