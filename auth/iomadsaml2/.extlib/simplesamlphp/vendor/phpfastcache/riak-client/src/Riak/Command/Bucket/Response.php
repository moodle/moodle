<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\Bucket;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * Bucket from the command re-instantiated with its fetched properties
     *
     * @var Bucket|null
     */
    protected $bucket = null;

    protected $modified = '';

    public function __construct($success = true, $code = 0, $message = '', Bucket $bucket = null, $modified = '')
    {
        parent::__construct($success, $code, $message);

        $this->bucket = $bucket;
        $this->modified = $modified;
    }

    /**
     * getBucket
     *
     * @return Bucket
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Retrieves the last modified time of the object
     *
     * @return string
     */
    public function getLastModified()
    {
        return $this->modified;
    }
}
