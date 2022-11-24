<?php

namespace OpenSpout\Reader\Exception;

use Throwable;

class InvalidValueException extends ReaderException
{
    /** @var mixed */
    private $invalidValue;

    /**
     * @param mixed  $invalidValue
     * @param string $message
     * @param int    $code
     */
    public function __construct($invalidValue, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->invalidValue = $invalidValue;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getInvalidValue()
    {
        return $this->invalidValue;
    }
}
