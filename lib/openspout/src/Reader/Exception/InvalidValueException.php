<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Exception;

use Throwable;

final class InvalidValueException extends ReaderException
{
    private readonly string $invalidValue;

    public function __construct(string $invalidValue, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->invalidValue = $invalidValue;
        parent::__construct($message, $code, $previous);
    }

    public function getInvalidValue(): string
    {
        return $this->invalidValue;
    }
}
