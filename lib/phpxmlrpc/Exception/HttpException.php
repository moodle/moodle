<?php

namespace PhpXmlRpc\Exception;

/**
 * To be used for all errors related to parsing HTTP requests and responses
 */
class HttpException extends TransportException
{
    protected $statusCode;

    public function __construct($message = "", $code = 0, $previous = null, $statusCode = null)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
    }

    public function statusCode()
    {
        return $this->statusCode;
    }
}
