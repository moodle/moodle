<?php

namespace Basho\Riak;

/**
 * Trait HeadersTrait
 *
 * Offers code reuse between kv objects & crdts since they share several common needs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait HeadersTrait
{
    /**
     * Request / response headers for the object
     *
     * Content type, last modified, etc
     *
     * @var array
     */
    protected $headers = [];

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Retrieve the value for a header, null if not set
     *
     * @param $key
     *
     * @return string|null
     */
    protected function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : NULL;
    }
}
