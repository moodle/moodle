<?php

namespace Basho\Riak\Command\DataType\Set;

use Basho\Riak\DataType\Set;
use Basho\Riak\Location;

/**
 * Container for a response related to an operation on a set data type
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\DataType\Set|null
     */
    protected $set = null;

    public function __construct($success = true, $code = 0, $message = '', $location = null, $set = null, $date = '')
    {
        parent::__construct($success, $code, $message);

        $this->set = $set;
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
     * @return Set|null
     */
    public function getSet()
    {
        return $this->set;
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
