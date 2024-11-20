<?php

namespace Basho\Riak\Command\Builder\TimeSeries;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait KeyTrait
{
    /**
     * Stores the key
     *
     * @var \Basho\Riak\TimeSeries\Cell[]
     */
    protected $key = [];

    /**
     * Gets the key
     *
     * @return \Basho\Riak\TimeSeries\Cell[]
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Attach the provided key to the Command Builder
     *
     * @param \Basho\Riak\TimeSeries\Cell[] $key
     *
     * @return $this
     */
    public function atKey(array $key)
    {
        $this->key = $key;

        return $this;
    }
}
