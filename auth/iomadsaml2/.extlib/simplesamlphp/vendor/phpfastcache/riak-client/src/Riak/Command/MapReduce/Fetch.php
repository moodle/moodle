<?php

namespace Basho\Riak\Command\MapReduce;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to fetch a result set from Riak using MapReduce
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Fetch extends Command implements CommandInterface
{
    protected $method = 'POST';

    /**
     * @var Command\MapReduce\Response|null
     */
    protected $response = null;

    protected $inputs;

    protected $query;

    public function __construct(Command\Builder\MapReduce\FetchObjects $builder)
    {
        parent::__construct($builder);

        $this->inputs = $builder->getInputs();
        // query needs to be a list
        $this->query = $builder->getQuery();
    }

    public function getEncodedData()
    {
        return json_encode($this->getData());
    }

    public function getData()
    {
        return ['inputs' => $this->inputs, 'query' => $this->query];
    }

    /**
     * @return Command\MapReduce\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
