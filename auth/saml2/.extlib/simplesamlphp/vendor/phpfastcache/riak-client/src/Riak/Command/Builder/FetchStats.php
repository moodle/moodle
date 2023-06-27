<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Riak real time stats
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchStats extends Command\Builder implements Command\BuilderInterface
{
    public function __construct(Riak $riak)
    {
        parent::__construct($riak);
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Stats;
     */
    public function build()
    {
        $this->validate();

        return new Command\Stats($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
    }
}
