<?php

namespace Basho\Riak\Command\Stats;

use Basho\Riak\Location;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    protected $stats = [];

    public function __construct($success = true, $code = 0, $message = '', $data = [])
    {
        parent::__construct($success, $code, $message);

        $this->stats = $data;
    }

    public function __get($name) {
        if (isset($this->stats[$name])) {
            return $this->stats[$name];
        }

        return null;
    }

    public function getAllStats() {
        return $this->stats;
    }
}
