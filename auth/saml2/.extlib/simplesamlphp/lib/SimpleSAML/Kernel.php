<?php

declare(strict_types=1);

namespace SimpleSAML;

use SimpleSAML\Utils\System;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * A class to create the container and handle a given request.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @var string
     */
    private $module;


    /**
     * @param string $module
     */
    public function __construct($module)
    {
        $this->module = $module;

        $env = getenv('APP_ENV') ?: (getenv('SYMFONY_ENV') ?: 'prod');

        parent::__construct($env, false);
    }


    /**
     * @return string
     */
    public function getCacheDir()
    {
        $configuration = Configuration::getInstance();
        $cachePath = $configuration->getString('tempdir') . '/cache/' . $this->module;

        if (System::isAbsolutePath($cachePath)) {
            return $cachePath;
        }

        return $configuration->getBaseDir() . '/' . $cachePath;
    }


    /**
     * @return string
     */
    public function getLogDir()
    {
        $configuration = Configuration::getInstance();
        $loggingPath = $configuration->getString('loggingdir');

        if (System::isAbsolutePath($loggingPath)) {
            return $loggingPath;
        }

        return $configuration->getBaseDir() . '/' . $loggingPath;
    }


    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }


    /**
     * Get the module loaded in this kernel.
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }


    /**
     * Configures the container.
     *
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     * @return void
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $configuration = Configuration::getInstance();
        $baseDir = $configuration->getBaseDir();
        $loader->load($baseDir . '/routing/services/*' . self::CONFIG_EXTS, 'glob');
        $confDir = Module::getModuleDir($this->module) . '/routing/services';
        if (is_dir($confDir)) {
            $loader->load($confDir . '/**/*' . self::CONFIG_EXTS, 'glob');
        }

        $c->loadFromExtension('framework', [
            'secret' => Configuration::getInstance()->getString('secretsalt'),
        ]);

        $this->registerModuleControllers($c);
    }


    /**
     * Import routes.
     *
     * @param RouteCollectionBuilder $routes
     * @return void
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $configuration = Configuration::getInstance();
        $baseDir = $configuration->getBaseDir();
        $routes->import($baseDir . '/routing/routes/*' . self::CONFIG_EXTS, '/', 'glob');
        $confDir = Module::getModuleDir($this->module) . '/routing/routes';
        if (is_dir($confDir)) {
            $routes->import($confDir . '/**/*' . self::CONFIG_EXTS, $this->module, 'glob');
        } else {
            // Remain backwards compatible by checking for routers in the old location (1.18 style)
            $confDir = Module::getModuleDir($this->module);
            $routes->import($confDir . '/routes' . self::CONFIG_EXTS, $this->module, 'glob');
        }
    }


    /**
     * @param ContainerBuilder $container
     * @return void
     */
    private function registerModuleControllers(ContainerBuilder $container): void
    {
        try {
            $definition = new Definition();
            $definition->setAutowired(true);
            $definition->setPublic(true);

            $controllerDir = Module::getModuleDir($this->module) . '/lib/Controller';

            if (!is_dir($controllerDir)) {
                return;
            }

            $loader = new DirectoryLoader(
                $container,
                new FileLocator($controllerDir . '/')
            );
            $loader->registerClasses(
                $definition,
                'SimpleSAML\\Module\\' . $this->module . '\\Controller\\',
                $controllerDir . '/*'
            );
        } catch (FileLocatorFileNotFoundException $e) {
        }
    }
}
