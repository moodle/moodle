<?php

namespace Basho\Riak\Command\TimeSeries\Query;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to store data within a TS table
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    protected $method = 'POST';

    /**
     * Stores the table name
     *
     * @var string|null
     */
    protected $query = NULL;

    /**
     * Interpolations in the form of key => value of the query string
     *
     * @var array
     */
    protected $interps = [];

    public function getData()
    {
        return ['query' => $this->query, 'interpolations' => $this->interps];
    }

    public function getEncodedData()
    {
        // plain text string
        return $this->query;
    }

    public function __construct(Command\Builder\TimeSeries\Query $builder)
    {
        parent::__construct($builder);

        $this->query = $builder->getQuery();
        $this->interps = $builder->getInterps();
    }
}
