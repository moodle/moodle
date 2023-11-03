<?php

namespace block_learnerscript\Spout\Reader;

/**
 * Interface IteratorInterface
 *
 * @package block_learnerscript\Spout\Reader
 */
interface IteratorInterface extends \Iterator
{
    /**
     * Cleans up what was created to iterate over the object.
     *
     * @return void
     */
    public function end();
}
