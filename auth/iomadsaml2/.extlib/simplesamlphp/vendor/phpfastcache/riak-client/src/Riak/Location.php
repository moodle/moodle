<?php

namespace Basho\Riak;

/**
 * Immutable data structure storing the location of an Object or DataType
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Location
{
    /**
     * Kv Object / CRDT key
     *
     * @var string
     */
    protected $key = '';

    /**
     * @var Bucket|null
     */
    protected $bucket = NULL;

    /**
     * @param $key
     * @param Bucket $bucket
     */
    public function __construct($key, Bucket $bucket)
    {
        $this->key = $key;
        $this->bucket = $bucket;
    }

    /**
     * Generate an instance of the Location object using the Location header string value returned from Riak
     *
     * @param $location_string
     *
     * @return Location
     */
    public static function fromString($location_string)
    {
        preg_match('/^\/types\/([^\/]+)\/buckets\/([^\/]+)\/keys\/([^\/]+)$/', $location_string, $matches);

        return new self($matches[3], new Bucket($matches[2], $matches[1]));
    }

    public function __toString()
    {
        return $this->bucket . $this->key;
    }

    /**
     * @return Bucket|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
