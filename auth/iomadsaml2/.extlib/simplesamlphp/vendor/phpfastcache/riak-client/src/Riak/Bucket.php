<?php

namespace Basho\Riak;

/**
 * Core data structure for a Riak Bucket.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Bucket
{
    /**
     * The default bucket type in Riak.
     */
    const DEFAULT_TYPE = "default";

    /**
     * Bucket properties
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Name of bucket
     */
    protected $name = '';

    /**
     * Buckets are grouped by type, inheriting the properties defined on the type
     */
    protected $type = '';

    /**
     * @param        $name
     * @param string $type
     * @param array $properties
     */
    public function __construct($name, $type = self::DEFAULT_TYPE, array $properties = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->properties = $properties;
    }

    public function __toString()
    {
        return $this->getNamespace();
    }

    /**
     * Bucket namespace
     *
     * This is a human readable namespace for the bucket.
     *
     * @return string
     */
    public function getNamespace()
    {
        return "/{$this->type}/{$this->name}/";
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function getProperty($key)
    {
        $properties = $this->getProperties();
        if (isset($properties[$key])) {
            return $properties[$key];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
