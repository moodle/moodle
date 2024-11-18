<?php

declare(strict_types=1);

namespace SimpleSAML\Assert;

use BadMethodCallException;
use InvalidArgumentException;
use Throwable;
use Webmozart\Assert\Assert as Webmozart;

/**
 * Webmozart\Assert wrapper class
 *
 * @author Tim van Dijen, <tvdijen@gmail.com>
 * @package simplesamlphp/assert
 */
final class Assert
{
    /**
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public static function __callStatic($name, $arguments): void
    {
        // Handle Exception-parameter
        $exception = AssertionFailedException::class;
        $last = end($arguments);
        if (is_string($last) && class_exists($last) && is_subclass_of($last, Throwable::class)) {
            $exception = $last;

            array_pop($arguments);
        }

        try {
            call_user_func_array([Webmozart::class, $name], $arguments);
            return;
        } catch (InvalidArgumentException $e) {
            throw new $exception($e->getMessage());
        }
    }
}
