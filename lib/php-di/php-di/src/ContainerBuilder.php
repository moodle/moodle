<?php

declare(strict_types=1);

namespace DI;

use DI\Compiler\Compiler;
use DI\Definition\Source\AttributeBasedAutowiring;
use DI\Definition\Source\DefinitionArray;
use DI\Definition\Source\DefinitionFile;
use DI\Definition\Source\DefinitionSource;
use DI\Definition\Source\NoAutowiring;
use DI\Definition\Source\ReflectionBasedAutowiring;
use DI\Definition\Source\SourceCache;
use DI\Definition\Source\SourceChain;
use DI\Proxy\ProxyFactory;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

/**
 * Helper to create and configure a Container.
 *
 * With the default options, the container created is appropriate for the development environment.
 *
 * Example:
 *
 *     $builder = new ContainerBuilder();
 *     $container = $builder->build();
 *
 * @api
 *
 * @since  3.2
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 *
 * @psalm-template ContainerClass of Container
 */
class ContainerBuilder
{
    /**
     * Name of the container class, used to create the container.
     * @var class-string<Container>
     * @psalm-var class-string<ContainerClass>
     */
    private string $containerClass;

    /**
     * Name of the container parent class, used on compiled container.
     * @var class-string<Container>
     * @psalm-var class-string<ContainerClass>
     */
    private string $containerParentClass;

    private bool $useAutowiring = true;

    private bool $useAttributes = false;

    /**
     * If set, write the proxies to disk in this directory to improve performances.
     */
    private ?string $proxyDirectory = null;

    /**
     * If PHP-DI is wrapped in another container, this references the wrapper.
     */
    private ?ContainerInterface $wrapperContainer = null;

    /**
     * @var DefinitionSource[]|string[]|array[]
     */
    private array $definitionSources = [];

    /**
     * Whether the container has already been built.
     */
    private bool $locked = false;

    private ?string $compileToDirectory = null;

    private bool $sourceCache = false;

    protected string $sourceCacheNamespace = '';

    /**
     * @param class-string<Container> $containerClass Name of the container class, used to create the container.
     * @psalm-param class-string<ContainerClass> $containerClass
     */
    public function __construct(string $containerClass = Container::class)
    {
        $this->containerClass = $containerClass;
    }

    /**
     * Build and return a container.
     *
     * @return Container
     * @psalm-return ContainerClass
     */
    public function build()
    {
        $sources = array_reverse($this->definitionSources);

        if ($this->useAttributes) {
            $autowiring = new AttributeBasedAutowiring;
            $sources[] = $autowiring;
        } elseif ($this->useAutowiring) {
            $autowiring = new ReflectionBasedAutowiring;
            $sources[] = $autowiring;
        } else {
            $autowiring = new NoAutowiring;
        }

        $sources = array_map(function ($definitions) use ($autowiring) {
            if (is_string($definitions)) {
                // File
                return new DefinitionFile($definitions, $autowiring);
            }
            if (is_array($definitions)) {
                return new DefinitionArray($definitions, $autowiring);
            }

            return $definitions;
        }, $sources);
        $source = new SourceChain($sources);

        // Mutable definition source
        $source->setMutableDefinitionSource(new DefinitionArray([], $autowiring));

        if ($this->sourceCache) {
            if (!SourceCache::isSupported()) {
                throw new \Exception('APCu is not enabled, PHP-DI cannot use it as a cache');
            }
            // Wrap the source with the cache decorator
            $source = new SourceCache($source, $this->sourceCacheNamespace);
        }

        $proxyFactory = new ProxyFactory($this->proxyDirectory);

        $this->locked = true;

        $containerClass = $this->containerClass;

        if ($this->compileToDirectory) {
            $compiler = new Compiler($proxyFactory);
            $compiledContainerFile = $compiler->compile(
                $source,
                $this->compileToDirectory,
                $containerClass,
                $this->containerParentClass,
                $this->useAutowiring
            );
            // Only load the file if it hasn't been already loaded
            // (the container can be created multiple times in the same process)
            if (!class_exists($containerClass, false)) {
                require $compiledContainerFile;
            }
        }

        return new $containerClass($source, $proxyFactory, $this->wrapperContainer);
    }

    /**
     * Compile the container for optimum performances.
     *
     * Be aware that the container is compiled once and never updated!
     *
     * Therefore:
     *
     * - in production you should clear that directory every time you deploy
     * - in development you should not compile the container
     *
     * @see https://php-di.org/doc/performances.html
     *
     * @psalm-template T of CompiledContainer
     *
     * @param string $directory Directory in which to put the compiled container.
     * @param string $containerClass Name of the compiled class. Customize only if necessary.
     * @param class-string<Container> $containerParentClass Name of the compiled container parent class. Customize only if necessary.
     * @psalm-param class-string<T> $containerParentClass
     *
     * @psalm-return self<T>
     */
    public function enableCompilation(
        string $directory,
        string $containerClass = 'CompiledContainer',
        string $containerParentClass = CompiledContainer::class
    ) : self {
        $this->ensureNotLocked();

        $this->compileToDirectory = $directory;
        $this->containerClass = $containerClass;
        $this->containerParentClass = $containerParentClass;

        return $this;
    }

    /**
     * Enable or disable the use of autowiring to guess injections.
     *
     * Enabled by default.
     *
     * @return $this
     */
    public function useAutowiring(bool $bool) : self
    {
        $this->ensureNotLocked();

        $this->useAutowiring = $bool;

        return $this;
    }

    /**
     * Enable or disable the use of PHP 8 attributes to configure injections.
     *
     * Disabled by default.
     *
     * @return $this
     */
    public function useAttributes(bool $bool) : self
    {
        $this->ensureNotLocked();

        $this->useAttributes = $bool;

        return $this;
    }

    /**
     * Configure the proxy generation.
     *
     * For dev environment, use `writeProxiesToFile(false)` (default configuration)
     * For production environment, use `writeProxiesToFile(true, 'tmp/proxies')`
     *
     * @see https://php-di.org/doc/lazy-injection.html
     *
     * @param bool $writeToFile If true, write the proxies to disk to improve performances
     * @param string|null $proxyDirectory Directory where to write the proxies
     * @return $this
     * @throws InvalidArgumentException when writeToFile is set to true and the proxy directory is null
     */
    public function writeProxiesToFile(bool $writeToFile, string $proxyDirectory = null) : self
    {
        $this->ensureNotLocked();

        if ($writeToFile && $proxyDirectory === null) {
            throw new InvalidArgumentException(
                'The proxy directory must be specified if you want to write proxies on disk'
            );
        }
        $this->proxyDirectory = $writeToFile ? $proxyDirectory : null;

        return $this;
    }

    /**
     * If PHP-DI's container is wrapped by another container, we can
     * set this so that PHP-DI will use the wrapper rather than itself for building objects.
     *
     * @return $this
     */
    public function wrapContainer(ContainerInterface $otherContainer) : self
    {
        $this->ensureNotLocked();

        $this->wrapperContainer = $otherContainer;

        return $this;
    }

    /**
     * Add definitions to the container.
     *
     * @param string|array|DefinitionSource ...$definitions Can be an array of definitions, the
     *                                                      name of a file containing definitions
     *                                                      or a DefinitionSource object.
     * @return $this
     */
    public function addDefinitions(string|array|DefinitionSource ...$definitions) : self
    {
        $this->ensureNotLocked();

        foreach ($definitions as $definition) {
            $this->definitionSources[] = $definition;
        }

        return $this;
    }

    /**
     * Enables the use of APCu to cache definitions.
     *
     * You must have APCu enabled to use it.
     *
     * Before using this feature, you should try these steps first:
     * - enable compilation if not already done (see `enableCompilation()`)
     * - if you use autowiring or attributes, add all the classes you are using into your configuration so that
     *   PHP-DI knows about them and compiles them
     * Once this is done, you can try to optimize performances further with APCu. It can also be useful if you use
     * `Container::make()` instead of `get()` (`make()` calls cannot be compiled so they are not optimized).
     *
     * Remember to clear APCu on each deploy else your application will have a stale cache. Do not enable the cache
     * in development environment: any change you will make to the code will be ignored because of the cache.
     *
     * @see https://php-di.org/doc/performances.html
     *
     * @param string $cacheNamespace use unique namespace per container when sharing a single APC memory pool to prevent cache collisions
     * @return $this
     */
    public function enableDefinitionCache(string $cacheNamespace = '') : self
    {
        $this->ensureNotLocked();

        $this->sourceCache = true;
        $this->sourceCacheNamespace = $cacheNamespace;

        return $this;
    }

    /**
     * Are we building a compiled container?
     */
    public function isCompilationEnabled() : bool
    {
        return (bool) $this->compileToDirectory;
    }

    private function ensureNotLocked() : void
    {
        if ($this->locked) {
            throw new \LogicException('The ContainerBuilder cannot be modified after the container has been built');
        }
    }
}
