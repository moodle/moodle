<?php

namespace Basho\Riak\Command\DataType\Map;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;
use Basho\Riak\Location;

/**
 * Fetches a map data type from Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    /**
     * @var Command\DataType\Map\Response|null
     */
    protected $response = NULL;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    public function __construct(Command\Builder\FetchMap $builder)
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
     * @return Command\DataType\Map\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
