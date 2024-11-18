<?php

namespace Basho\Riak;

use Basho\Riak\Command\Builder;

/**
 * The command class is used to build a read or write command to be executed against a Riak node.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Command
{
    /**
     * Request method
     *
     * This can be GET, POST, PUT, or DELETE
     *
     * @see http://docs.basho.com/riak/latest/dev/references/http/
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Command parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * @var Bucket|null
     */
    protected $bucket = null;

    /**
     * @var Command\Response|null
     */
    protected $response = null;

    /**
     * @var \Basho\Riak|null
     */
    protected $riak = null;

    protected $verbose = false;

    protected $connectionTimeout = 0;

    public function __construct(Builder $builder)
    {
        $this->riak = $builder->getConnection();
        $this->parameters = $builder->getParameters();
        $this->verbose = $builder->getVerbose();
        $this->connectionTimeout = $builder->getConnectionTimeout();
    }

    public function isVerbose()
    {
        return $this->verbose;
    }

    /**
     * Executes the command against the API
     *
     * @return Command\Response
     */
    public function execute()
    {
        return $this->riak->execute($this);
    }

    /**
     * Gets the request that was issued to the API by this Command.
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->riak->getLastRequest();
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param $key string
     *
     * @return null|string
     */
    public function getParameter($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Command has parameters?
     *
     * @return bool
     */
    public function hasParameters()
    {
        return (bool)count($this->parameters);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Location|null
     */
    public function getLocation()
    {
        return null;
    }

    /**
     * @return Object|null
     */
    public function getObject()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    abstract public function getData();

    abstract public function getEncodedData();
}
