<?php

namespace PhpXmlRpc\Exception;

use PhpXmlRpc\Exception as BaseExtension;

/**
 * Exception thrown when an argument passed to a function or method has an unsupported value (but its type is ok)
 */
class ValueErrorException extends BaseExtension
{
}
