<?php

namespace Basho\Riak\Command\DataType\Map;

use Basho\Riak\DataType\Map;
use Basho\Riak\Location;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\DataType\Map|null
     */
    protected $map = null;

    /**
     * @var Location
     */
    protected $location = null;

    /**
     * @var string
     */
    protected $date = '';

    public function __construct($success = true, $code = 0, $message = '', $location = null, $map = null, $date = '')
    {
        parent::__construct($success, $code, $message);

        $this->map = $map;
        $this->location = $location;
        $this->date = $date;
    }

    /**
     * Retrieves the Location value from the response headers
     *
     * @return Location
     * @throws \Basho\Riak\Command\Exception
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return Map|null
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Retrieves the date of the counter's retrieval
     *
     * @return string
     * @throws \Basho\Riak\Command\Exception
     */
    public function getDate()
    {
        return $this->date;
    }
}
