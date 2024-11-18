<?php

namespace Basho\Riak\Command\KVObject\Keys;

use Basho\Riak\Location;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var \Basho\Riak\Location[]
     */
    protected $keys = [];

    public function __construct($success = true, $code = 0, $message = '', $keys = [])
    {
        parent::__construct($success, $code, $message);

        $this->keys = $keys;
    }

    /**
     * Fetches the keys from the response
     *
     * @return \Basho\Riak\Location[]
     */
    public function getKeys()
    {
        return $this->keys;
    }
}
