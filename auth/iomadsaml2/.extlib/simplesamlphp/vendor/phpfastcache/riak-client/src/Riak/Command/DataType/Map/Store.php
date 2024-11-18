<?php

namespace Basho\Riak\Command\DataType\Map;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;
use Basho\Riak\Location;

/**
 * Stores a write to a map
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'POST';

    /**
     * @var Command\DataType\Map\Response|null
     */
    protected $response = NULL;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    /**
     * Elements to remove from the map
     *
     * @var array
     */
    protected $remove = [];

    /**
     * @var array
     */
    protected $registers = [];

    /**
     * @var array
     */
    protected $flags = [];

    /**
     * @var Command\Builder\IncrementCounter[]
     */
    protected $counters = [];

    /**
     * @var Command\Builder\UpdateSet[]
     */
    protected $sets = [];

    /***
     * @var Command\Builder\UpdateMap[]
     */
    protected $maps = [];

    public function __construct(Command\Builder\UpdateMap $builder)
    {
        parent::__construct($builder);

        $this->remove = $builder->getRemove();
        $this->registers = $builder->getRegisters();
        $this->flags = $builder->getFlags();
        $this->counters = $builder->getCounters();
        $this->sets = $builder->getSets();
        $this->maps = $builder->getMaps();
        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function getData()
    {
        $data = [];

        if (count($this->remove)) {
            $data['remove'] = $this->remove;
        }

        if (count($this->registers) || count($this->flags) || count($this->counters) || count($this->sets) || count($this->maps)) {
            $data['update'] = [];
        }

        foreach ($this->registers as $key => $item) {
            $data['update'][$key] = $item;
        }

        foreach ($this->flags as $key => $item) {
            $data['update'][$key] = ($item === TRUE ? 'enable' : 'disable');
        }

        foreach ($this->counters as $key => $item) {
            $data['update'][$key] = $item->getIncrement();
        }

        foreach ($this->sets as $key => $item) {
            $data['update'][$key] = [];
            $data['update'][$key]['add_all'] = $item->getAddAll();

            $remove = $item->getRemoveAll();
            if (count($remove)) {
                $data['update'][$key]['remove_all'] = $remove;
            }
        }

        foreach ($this->maps as $key => $item) {
            $mapCommand = $item->atLocation($this->getLocation())->build();
            $data['update'][$key] = $mapCommand->getData();
        }

        return $data;
    }

    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return Command\DataType\Map\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
