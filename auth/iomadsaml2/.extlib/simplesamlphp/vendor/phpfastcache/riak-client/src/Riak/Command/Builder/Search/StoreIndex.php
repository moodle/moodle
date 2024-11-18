<?php

namespace Basho\Riak\Command\Builder\Search;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class StoreIndex extends Command\Builder implements Command\BuilderInterface
{
    /**
     * Name of index to create
     *
     * @var string
     */
    protected $name = '';

    /**
     * Solr schema to use for Searching your Riak data
     *
     * @var string
     */
    protected $schema = '_yz_default';

    /**
     * @param $name
     *
     * @return $this
     */
    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param $schema
     *
     * @return $this
     */
    public function usingSchema($schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Search\Index\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\Search\Index\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Name');
        $this->required('Schema');
    }
}
