<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SetBucketProperties extends Command\Builder implements Command\BuilderInterface
{
    use BucketTrait;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Bucket\Store
     */
    public function build()
    {
        $this->validate();

        return new Command\Bucket\Store($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('Bucket');

        if (count($this->properties) < 1) {
            throw new Exception('At least one element to add or remove needs to be defined.');
        }
    }
}
