<?php

namespace Basho\Riak\Command\Search\Schema;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Class Store
 *
 * Riak Yokozuna Search Schema Store
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'PUT';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $schema = '';

    /**
     * @var Command\Response|null
     */
    protected $response = null;

    public function __construct(Command\Builder\Search\StoreSchema $builder)
    {
        parent::__construct($builder);

        $this->name = $builder->getName();
        $this->schema = $builder->getSchema();
    }

    public function getEncodedData()
    {
        return $this->getData();
    }

    public function getData()
    {
        return $this->schema;
    }

    /**
     * @return Command\Response
     */
    public function execute()
    {
        return parent::execute();
    }

    public function __toString()
    {
        return $this->name;
    }
}
