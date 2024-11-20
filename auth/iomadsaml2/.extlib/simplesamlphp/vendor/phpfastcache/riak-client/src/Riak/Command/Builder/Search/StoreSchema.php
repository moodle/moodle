<?php

namespace Basho\Riak\Command\Builder\Search;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class StoreSchema extends Command\Builder implements Command\BuilderInterface
{
    /**
     * Name of index to create
     *
     * @var string
     */
    protected $name = '';

    protected $schema = '';

    public function withSchemaFile($schema_file)
    {
        $this->schema = file_get_contents($schema_file);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    public function withSchemaString($schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Search\Schema\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\Search\Schema\Store($this);
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
