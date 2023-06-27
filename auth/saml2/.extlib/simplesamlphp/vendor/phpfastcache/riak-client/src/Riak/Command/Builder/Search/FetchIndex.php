<?php

namespace Basho\Riak\Command\Builder\Search;

use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchIndex extends Command\Builder implements Command\BuilderInterface
{
    protected $index_name = '';

    /**
     * {@inheritdoc}
     *
     * @return Command\Search\Index\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\Search\Index\Fetch($this);
    }

    public function validate()
    {
        return;
    }

    public function withName($name)
    {
        $this->index_name = $name;

        return $this;
    }

    public function getIndexName()
    {
        return $this->index_name;
    }
}
