<?php

declare(strict_types=1);

namespace SAML2\Compat;

use SAML2\Compat\Ssp\Container;

class ContainerSingleton
{
    /**
     * @var \SAML2\Compat\AbstractContainer
     */
    protected static $container;


    /**
     * @return \SAML2\Compat\AbstractContainer
     */
    public static function getInstance() : AbstractContainer
    {
        if (!isset(self::$container)) {
            self::$container = self::initSspContainer();
        }
        return self::$container;
    }


    /**
     * Set a container to use.
     *
     * @param \SAML2\Compat\AbstractContainer $container
     * @return void
     */
    public static function setContainer(AbstractContainer $container) : void
    {
        self::$container = $container;
    }


    /**
     * @return \SAML2\Compat\Ssp\Container
     */
    public static function initSspContainer() : Container
    {
        return new Container();
    }
}
