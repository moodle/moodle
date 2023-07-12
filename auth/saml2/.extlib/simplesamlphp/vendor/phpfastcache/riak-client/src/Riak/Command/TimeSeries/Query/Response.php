<?php

namespace Basho\Riak\Command\TimeSeries\Query;

use Basho\Riak\Command;

/**
 * Response object for TS Fetch, Store, Delete
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends Command\Response
{
    protected $results = [];

    public function __construct($success = true, $code = 0, $message = '', $results = [])
    {
        parent::__construct($success, $code, $message);

        $this->results = $results;
    }

    /**
     * @return \Basho\Riak\TimeSeries\Cell[]|null
     */
    public function getResult()
    {
        return !empty($this->results[0]) ? $this->results[0] : null;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }
}
