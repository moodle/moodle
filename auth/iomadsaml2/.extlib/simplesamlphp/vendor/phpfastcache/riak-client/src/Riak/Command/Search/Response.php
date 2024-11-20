<?php

namespace Basho\Riak\Command\Search;

use Basho\Riak\Search\Doc;

/**
 * Container for a response for receiving data back from a Search request on Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    /**
     * @var int
     */
    protected $numFound = 0;

    /**
     * @var Doc[]
     */
    protected $docs = [];

    /**
     * Response constructor.
     * @param bool|true $success
     * @param int $code
     * @param string $message
     * @param int $numFound
     * @param \Basho\Riak\Search\Doc[] $docs
     */
    public function __construct($success = true, $code = 0, $message = '', $numFound = 0, $docs = [])
    {
        parent::__construct($success, $code, $message);

        $this->numFound = $numFound;
        $this->docs = $docs;
    }

    /**
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * @return \Basho\Riak\Search\Doc[]
     */
    public function getDocs()
    {
        return $this->docs;
    }
}
