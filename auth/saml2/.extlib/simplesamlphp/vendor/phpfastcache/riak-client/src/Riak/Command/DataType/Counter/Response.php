<?php

namespace Basho\Riak\Command\DataType\Counter;

use Basho\Riak\DataType\Counter;
use Basho\Riak\Location;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\DataType\Counter|null
     */
    protected $counter = null;

    /**
     * @var Location
     */
    protected $location = null;

    /**
     * @var string
     */
    protected $date = '';

    public function __construct($success = true, $code = 0, $message = '', $location = null, $counter = null, $date = '')
    {
        parent::__construct($success, $code, $message);

        $this->counter = $counter;
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
     * @return Counter|null
     */
    public function getCounter()
    {
        return $this->counter;
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
