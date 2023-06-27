<?php

namespace Basho\Riak\Command\DataType\Counter;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;
use Basho\Riak\Location;

/**
 * Used to fetch a counter
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    /**
     * @var Command\DataType\Counter\Response|null
     */
    protected $response = NULL;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    public function __construct(Command\Builder\FetchCounter $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
    }

    public function getLocation()
    {
        return $this->location;
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
     * @return Command\DataType\Counter\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
