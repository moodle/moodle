<?php

namespace Basho\Riak\Command\Search\Index;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to delete a Search Index from Riak Yokozuna
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Delete extends Command implements CommandInterface
{
    protected $method = 'DELETE';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Command\Response|null
     */
    protected $response = null;

    public function __construct(Command\Builder\Search\DeleteIndex $builder)
    {
        parent::__construct($builder);

        $this->name = $builder->getName();
    }

    public function getEncodedData()
    {
        return $this->getData();
    }

    public function getData()
    {
        return '';
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
