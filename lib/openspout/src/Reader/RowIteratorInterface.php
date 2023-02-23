<?php

declare(strict_types=1);

namespace OpenSpout\Reader;

use OpenSpout\Common\Entity\Row;

interface RowIteratorInterface extends IteratorInterface
{
    /**
     * Cleans up what was created to iterate over the object.
     */
    #[\ReturnTypeWillChange]
    public function end();

    /**
     * @return null|Row
     */
    #[\ReturnTypeWillChange]
    public function current();
}
