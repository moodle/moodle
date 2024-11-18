<?php

declare(strict_types=1);

namespace SimpleSAML\Assert;

use UnexpectedValueException;

/**
 * Generic exception for failing assertions.
 * Applications may extend from it to create more specific exceptions.
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/assert
 */
class AssertionFailedException extends UnexpectedValueException
{
}
