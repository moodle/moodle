<?php

declare(strict_types=1);

namespace DI\Proxy;

use LogicException;

/**
 * Uses PHP 8.4+'s native support for lazy proxies to generate proxy objects.
 *
 * @since  7.1
 * @author Buster Neece <buster@busterneece.com>
 */
class NativeProxyFactory implements ProxyFactoryInterface
{
    /**
     * Creates a new lazy proxy instance of the given class with
     * the given initializer.
     *
     * {@inheritDoc}
     */
    public function createProxy(string $className, \Closure $createFunction) : object
    {
        if (\PHP_VERSION_ID < 80400) {
            throw new LogicException('Lazy loading proxies require PHP 8.4 or higher.');
        }

        $reflector = new \ReflectionClass($className);

        return $reflector->newLazyProxy($createFunction);
    }

    public function generateProxyClass(string $className) : void
    {
        // Noop for this type.
    }
}
