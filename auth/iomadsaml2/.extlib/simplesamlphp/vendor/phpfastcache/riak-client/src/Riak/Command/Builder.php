<?php

namespace Basho\Riak\Command;

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\DataType;

/**
 * This class follows the Builder design pattern and is the preferred method for creating Basho\Riak\Command
 * objects for interacting with your Riak data cluster.
 *
 * <code>
 * use Basho\Riak\Command;
 * use Basho\Riak\Bucket;
 * use Basho\Riak\Location;
 *
 * $bucket = new Bucket('users');
 *
 * $command = (new Command\Builder(Command::STORE_OBJECT))
 *      ->withObject(new Object('test_data'))
 *      ->atLocation(new Location('test_key', $bucket))
 *      ->build();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Builder
{
    /**
     * @var Riak|null
     */
    protected $riak = null;

    /**
     * Command parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Command request headers
     *
     * @var array
     */
    protected $headers = [];

    protected $verbose = false;

    protected $connectionTimeout = 0;

    public function __construct(Riak $riak)
    {
        $this->riak = $riak;
    }

    /**
     * Command build
     *
     * Validates then returns the built command object.
     */
    abstract public function build();

    public function withParameter($key, $value = true)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function withParameters($parameters = [])
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function withVerboseMode($verbose = true)
    {
        $this->verbose = $verbose;

        return $this;
    }

    public function withConnectionTimeout($seconds = 60)
    {
        $this->connectionTimeout = $seconds;

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getConnection()
    {
        return $this->riak;
    }

    public function getVerbose()
    {
        return $this->verbose;
    }

    /**
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    /**
     * Validate command
     *
     * Method validates if the builder has the parameters / objects required to successfully execute the command
     *
     * @return bool
     * @throws Builder\Exception
     */
    protected function validate()
    {
        throw new Command\Builder\Exception('Invalid builder.');
    }

    /**
     * Used to verify a property within the builder is not null and is instantiated
     *
     * @param $objectName
     *
     * @throws Builder\Exception
     */
    protected function required($objectName)
    {
        $method = "get{$objectName}";
        $class = "Basho\\Riak\\{$objectName}";
        $value = $this->$method();
        if (is_null($value)) {
            throw new Builder\Exception("Expected non-empty value for {$objectName}");
        }
        if (is_object($value) && $value instanceof $class === false) {
            throw new Builder\Exception("Expected instance of {$class}, received instance of " . get_class($value));
        }
        if (is_array($value) && count($value) == 0) {
            throw new Builder\Exception("Expected non-empty array value for {$objectName}");
        }
    }
}
