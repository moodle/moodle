<?php

namespace Basho\Riak\Command\TimeSeries;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to fetch data within a TS table
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    /**
     * Stores the table name
     *
     * @var string|null
     */
    protected $table = NULL;

    /**
     * Stores the key
     *
     * @var \Basho\Riak\TimeSeries\Cell[]
     */
    protected $key = [];

    public function getTable()
    {
        return $this->table;
    }

    public function getData()
    {
        return $this->key;
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function __construct(Command\Builder\TimeSeries\FetchRow $builder)
    {
        parent::__construct($builder);

        $this->table = $builder->getTable();
        $this->key = $builder->getKey();
    }
}