<?php

namespace Box\Spout\Reader;

/**
 * Interface SheetInterface
 *
 * @package Box\Spout\Reader
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
