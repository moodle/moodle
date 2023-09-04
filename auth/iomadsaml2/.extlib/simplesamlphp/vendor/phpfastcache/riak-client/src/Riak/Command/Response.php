<?php

namespace Basho\Riak\Command;

/**
 * Data structure for handling Command responses from Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response
{
    protected $success = false;

    protected $code = '';

    protected $message = '';

    public function __construct($success = true, $code = 0, $message = '')
    {
        $this->success = $success;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->code == '404' ? true : false;
    }

    public function isUnauthorized()
    {
        return $this->code == '401' ? true : false;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
