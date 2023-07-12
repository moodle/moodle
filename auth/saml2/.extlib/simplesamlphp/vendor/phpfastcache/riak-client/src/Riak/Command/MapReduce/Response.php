<?php

namespace Basho\Riak\Command\MapReduce;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    protected $results = '';

    public function __construct($success = true, $code = 0, $message = '', $results = null)
    {
        parent::__construct($success, $code, $message);

        $this->results = $results;
    }

    public function getResults()
    {
        return $this->results;
    }
}
