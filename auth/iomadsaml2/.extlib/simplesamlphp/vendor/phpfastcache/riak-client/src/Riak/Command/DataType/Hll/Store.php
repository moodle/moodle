<?php

namespace Basho\Riak\Command\DataType\Hll;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;
use Basho\Riak\Location;

/**
 * Stores an update to an hll
 *
 * @author Luke Bakken <lbakken@basho.com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'POST';

    /**
     * @var array
     */
    protected $add_all = [];

    /**
     * @var Command\DataType\Hll\Response|null
     */
    protected $response = NULL;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    public function __construct(Command\Builder\UpdateHll $builder)
    {
        parent::__construct($builder);

        $this->add_all = $builder->getAddAll();
        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function getData()
    {
        return ['add_all' => $this->add_all];
    }

    /**
     * @return Command\DataType\Hll\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
