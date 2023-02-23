<?php

namespace PhpXmlRpc\Exception;

class HttpException extends PhpXmlrpcException
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
