<?php

namespace Basho\Riak;

/**
 * Abstraction for Conflict-free Replicated Data Types
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class DataType
{
    /**
     * DataType::TYPE
     *
     * Defines the key to be used to identify the data type. Used within a Maps composite key.
     *
     * @var string
     */
    const TYPE = '';

    /**
     * Storage member for DataType's current value
     *
     * @var mixed
     */
    protected $data;

    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }
}
