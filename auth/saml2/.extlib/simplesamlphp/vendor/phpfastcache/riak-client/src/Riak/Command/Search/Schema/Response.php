<?php

namespace Basho\Riak\Command\Search\Schema;

/**
 * Container for a response related to an operation on an object
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends \Basho\Riak\Command\Response
{
    protected $schema = '';
    protected $contentType = '';

    public function __construct($success = true, $code = 0, $message = '', $schema = null, $contentType = '')
    {
        parent::__construct($success, $code, $message);

        $this->schema = $schema;
        $this->contentType = $contentType;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
