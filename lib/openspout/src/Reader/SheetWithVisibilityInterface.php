<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

/**
 * @template T of RowIteratorInterface
 *
 * @extends SheetInterface<T>
 */
interface SheetWithVisibilityInterface extends SheetInterface
{
    /**
     * @return bool Whether the sheet is visible
     */
    public function isVisible(): bool;
}
