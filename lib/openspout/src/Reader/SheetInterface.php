<?php

namespace OpenSpout\Reader;

/**
 * Interface SheetInterface.
 */
interface SheetInterface
{
    /**
     * @return IteratorInterface iterator to iterate over the sheet's rows
     */
    public function getRowIterator();

    /**
     * @return int Index of the sheet
     */
    public function getIndex();

    /**
     * @return string Name of the sheet
     */
    public function getName();

    /**
     * @return bool Whether the sheet was defined as active
     */
    public function isActive();

    /**
     * @return bool Whether the sheet is visible
     */
    public function isVisible();
}
