<?php

declare(strict_types=1);

namespace SimpleSAML\Stats;

use SimpleSAML\Configuration;

/**
 * Interface for statistics outputs.
 *
 * @package SimpleSAMLphp
 */

abstract class Output
{
    /**
     * Initialize the output.
     *
     * @param \SimpleSAML\Configuration $config The configuration for this output.
     */
    public function __construct(Configuration $config)
    {
        // do nothing by default
    }


    /**
     * Write a stats event.
     *
     * @param array $data The event.
     */
    abstract public function emit(array $data);
}
