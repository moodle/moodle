<?php

declare(strict_types=1);

namespace DI\Proxy;

/**
 * Generic interface for proxy factories.
 *
 * @since  7.1
 * @author Buster Neece <buster@busterneece.com>
 */
interface ProxyFactoryInterface
{
    /**
     * Creates a new lazy proxy instance of the given class with
     * the given initializer.
     *
     * @param class-string $className name of the class to be proxied
     * @param \Closure $createFunction initializer to be passed to the proxy initializer to be passed to the proxy
     */
    public function createProxy(string $className, \Closure $createFunction) : object;

    /**
     * If the proxy generator depends on a filesystem component,
     * this step writes the proxy for that class to file. Otherwise,
     * it is a no-op.
     *
     * @param class-string $className name of the class to be proxied
     */
    public function generateProxyClass(string $className) : void;
}
