<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

use Iterator;

/**
 * @template T of SheetInterface
 *
 * @extends Iterator<T>
 */
interface SheetIteratorInterface extends Iterator
{
    /**
     * @return T of SheetInterface
     */
    public function current(): SheetInterface;
}
