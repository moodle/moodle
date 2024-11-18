<?php

namespace Basho\Riak\Command;

/**
 * BuilderInterface
 *
 * [summary]
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
interface BuilderInterface
{
    const COMMAND = '';

    public function build();
}
