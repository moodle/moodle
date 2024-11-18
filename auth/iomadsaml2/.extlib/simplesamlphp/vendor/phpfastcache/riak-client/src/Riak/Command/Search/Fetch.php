<?php

namespace Basho\Riak\Command\Search;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to fetch a search results from Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    /**
     * @var Command\Search\Response|null
     */
    protected $response = null;

    protected $index_name;

    public function __construct(Command\Builder\Search\FetchObjects $builder)
    {
        parent::__construct($builder);

        $this->index_name = $builder->getIndexName();
    }

    public function getData()
    {
        return '';
    }

    public function getEncodedData()
    {
        return '';
    }

    /**
     * @return Command\Search\Response
     */
    public function execute()
    {
        return parent::execute();
    }

    public function __toString()
    {
        return $this->index_name;
    }
}
