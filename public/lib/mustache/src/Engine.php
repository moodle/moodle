<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mustache;

use Mustache\Cache\FilesystemCache;
use Mustache\Cache\NoopCache;
use Mustache\Exception\InvalidArgumentException;
use Mustache\Exception\RuntimeException;
use Mustache\Exception\UnknownTemplateException;
use Mustache\Loader\ArrayLoader;
use Mustache\Loader\MutableLoader;
use Mustache\Loader\StringLoader;
use Psr\Log\LoggerInterface;

/**
 * A Mustache implementation in PHP.
 *
 * {@link https://mustache.github.io}
 *
 * Mustache is a framework-agnostic logic-less templating language. It enforces separation of view
 * logic from template files. In fact, it is not even possible to embed logic in the template.
 *
 * This is very, very rad.
 *
 * @author Justin Hileman {@link http://justinhileman.com}
 */
class Engine
{
    const VERSION      = '3.0.0';
    const SPEC_VERSION = '1.4.3';

    const PRAGMA_FILTERS       = 'FILTERS';
    const PRAGMA_ANCHORED_DOT  = 'ANCHORED-DOT';

    /**
     * @deprecated PRAGMA_BLOCKS is now part of the Mustache spec, and is enabled by default
     */
    const PRAGMA_BLOCKS = 'BLOCKS';

    // Known pragmas
    private static $knownPragmas = [
        self::PRAGMA_FILTERS       => true,
        self::PRAGMA_ANCHORED_DOT  => true,
        self::PRAGMA_BLOCKS        => true,
    ];

    // Template cache
    private $templates = [];

    // Environment
    private $templateClassPrefix = '__Mustache_';
    private $cache;
    private $lambdaCache;
    private $cacheLambdaTemplates = false;
    private $doubleRenderLambdas = false;
    private $loader;
    private $partialsLoader;
    private $helpers;
    private $escape;
    private $entityFlags = ENT_COMPAT;
    private $charset = 'UTF-8';
    private $logger;
    private $strictCallables = true;
    private $pragmas = [];
    private $delimiters;
    private $buggyPropertyShadowing = false;

    // Optional Mustache specs
    private $dynamicNames = true;
    private $inheritance = true;
    private $lambdas = true;

    // Services
    private $tokenizer;
    private $parser;
    private $compiler;

    /**
     * Mustache class constructor.
     *
     * Passing an $options array allows overriding certain Mustache options during instantiation:
     *
     *     $options = [
     *         // The class prefix for compiled templates. Defaults to '__Mustache_'.
     *         'template_class_prefix' => '__MyTemplates_',
     *
     *         // A Mustache cache instance or a cache directory string for compiled templates.
     *         // Mustache will not cache templates unless this is set.
     *         'cache' => __DIR__.'/tmp/cache/mustache',
     *
     *         // Override default permissions for cache files. Defaults to using the system-defined umask. It is
     *         // *strongly* recommended that you configure your umask properly rather than overriding permissions here.
     *         'cache_file_mode' => 0666,
     *
     *         // Optionally, enable caching for lambda section templates. This is generally not recommended, as lambda
     *         // sections are often too dynamic to benefit from caching.
     *         'cache_lambda_templates' => true,
     *
     *         // Customize the tag delimiters used by this engine instance. Note that overriding here changes the
     *         // delimiters used to parse all templates and partials loaded by this instance. To override just for a
     *         // single template, use an inline "change delimiters" tag at the start of the template file:
     *         //
     *         //     {{=<% %>=}}
     *         //
     *         'delimiters' => '<% %>',
     *
     *         // A Mustache template loader instance. Uses a StringLoader if not specified.
     *         'loader' => new \Mustache\Loader\FilesystemLoader(__DIR__.'/views'),
     *
     *         // A Mustache loader instance for partials.
     *         'partials_loader' => new \Mustache\Loader\FilesystemLoader(__DIR__.'/views/partials'),
     *
     *         // An array of Mustache partials. Useful for quick-and-dirty string template loading, but not as
     *         // efficient or lazy as a Filesystem (or database) loader.
     *         'partials' => ['foo' => file_get_contents(__DIR__.'/views/partials/foo.mustache')],
     *
     *         // An array of 'helpers'. Helpers can be global variables or objects, closures (e.g. for higher order
     *         // sections), or any other valid Mustache context value. They will be prepended to the context stack,
     *         // so they will be available in any template loaded by this Mustache instance.
     *         'helpers' => ['i18n' => function ($text) {
     *             // do something translatey here...
     *         }],
     *
     *         // An 'escape' callback, responsible for escaping double-mustache variables.
     *         'escape' => function ($value) {
     *             return htmlspecialchars($buffer, ENT_COMPAT, 'UTF-8');
     *         },
     *
     *         // Type argument for `htmlspecialchars`.  Defaults to ENT_COMPAT.  You may prefer ENT_QUOTES.
     *         'entity_flags' => ENT_QUOTES,
     *
     *         // Character set for `htmlspecialchars`. Defaults to 'UTF-8'. Use 'UTF-8'.
     *         'charset' => 'ISO-8859-1',
     *
     *         // A Mustache Logger instance. No logging will occur unless this is set. Using a PSR-3 compatible
     *         // logging library -- such as Monolog -- is highly recommended. A simple stream logger implementation is
     *         // available as well:
     *         'logger' => new \Mustache\Logger\StreamLogger('php://stderr'),
     *
     *
     *         // OPTIONAL MUSTACHE FEATURES:
     *
     *         // Enable dynamic names. By default, variables and sections like `{{*name}}` will be resolved dynamically.
     *         //
     *         // To disable dynamic name resolution, set this to false.
     *         'dynamic_names' => true,
     *
     *         // Enable template inheritance. By default, templates can extend other templates using the `{{< name}}` and
     *         // `{{$ block}}` tags.
     *         //
     *         // To disable inheritance, set this to false.
     *         'inheritance' => true,
     *
     *         // Enable lambda sections and values. By default, "lambdas" are enabled; if a variable resolves to a
     *         // callable value, that callable is called before interpolation. If a section name resolves to a callable
     *         // value, it is treated as a "higher order section", and the section content is passed to the callable
     *         // for processing prior to rendering.
     *         //
     *         // Note that the FILTERS pragma requires lambdas to function, so using FILTERS without lambdas enabled
     *         // will throw an invalid argument exception.
     *         //
     *         // To disable lambdas and higher order sections entirely, set this to false.
     *         'lambdas' => true,
     *
     *         // Enable pragmas across all templates, regardless of the presence of pragma tags in the individual
     *         // templates.
     *         'pragmas' => [\Mustache\Engine::PRAGMA_FILTERS],
     *
     *
     *         // BACKWARDS COMPATIBILITY:
     *
     *         // Only treat \Closure instances and invokable classes as callable. If true, values like
     *         // `['ClassName', 'methodName']` and `[$classInstance, 'methodName']`, which are traditionally
     *         // "callable" in PHP, are not called to resolve variables for interpolation or section contexts. This
     *         // helps protect against arbitrary code execution when user input is passed directly into the template.
     *         //
     *         // Defaults to true, but can be set to false to preserve Mustache.php v2.x behavior.
     *         //
     *         // THIS IS NOT RECOMMENDED.
     *         'strict_callables' => true,
     *
     *         // Enable buggy property shadowing. Per the Mustache spec, keys of a value higher in the context stack
     *         // shadow similarly named keys lower in the stack. For example, in the template
     *         // `{{# foo }}{{ bar }}{{/ foo }}` if the value for `foo` has a method, property, or key named `bar`, it
     *         // will prevent looking lower in the context stack for a another value named `bar`.
     *         //
     *         // Setting the value of an array key to null prevents lookups higher in the context stack. The behavior
     *         // should have been identical for object properties (and ArrayAccess) as well, but a bug in the context
     *         // lookup logic meant that a property which exists but is set to null would not prevent further context
     *         // lookup.
     *         //
     *         // This bug was fixed in Mustache.php v3.x, but the previous buggy behavior can be preserved by setting this
     *         // option to true.
     *         //
     *         // THIS IS NOT RECOMMENDED.
     *         'buggy_property_shadowing' => false,
     *
     *         // Double-render lambda return values. By default, the return value of higher order sections that are
     *         // rendered via the lambda helper will *not* be re-rendered.
     *         //
     *         // To preserve the behavior of Mustache.php v2.x, set this to true.
     *         //
     *         // THIS IS NOT RECOMMENDED.
     *        'double_render_lambdas' => false,
     *     ];
     *
     * @throws InvalidArgumentException If `escape` option is not callable
     * @throws InvalidArgumentException If `lambdas` is disabled but the `FILTERS` pragma is enabled
     */
    public function __construct(array $options = [])
    {
        if (isset($options['template_class_prefix'])) {
            if ((string) $options['template_class_prefix'] === '') {
                throw new InvalidArgumentException('Mustache Constructor "template_class_prefix" must not be empty');
            }

            $this->templateClassPrefix = $options['template_class_prefix'];
        }

        if (isset($options['cache'])) {
            $cache = $options['cache'];

            if (is_string($cache)) {
                $mode  = isset($options['cache_file_mode']) ? $options['cache_file_mode'] : null;
                $cache = new FilesystemCache($cache, $mode);
            }

            $this->setCache($cache);
        }

        if (isset($options['cache_lambda_templates'])) {
            $this->cacheLambdaTemplates = (bool) $options['cache_lambda_templates'];
        }

        if (isset($options['loader'])) {
            $this->setLoader($options['loader']);
        }

        if (isset($options['partials_loader'])) {
            $this->setPartialsLoader($options['partials_loader']);
        }

        if (isset($options['partials'])) {
            $this->setPartials($options['partials']);
        }

        if (isset($options['helpers'])) {
            $this->setHelpers($options['helpers']);
        }

        if (isset($options['escape'])) {
            if (!is_callable($options['escape'])) {
                throw new InvalidArgumentException('Mustache Constructor "escape" option must be callable');
            }

            $this->escape = $options['escape'];
        }

        if (isset($options['entity_flags'])) {
            $this->entityFlags = $options['entity_flags'];
        }

        if (isset($options['charset'])) {
            $this->charset = $options['charset'];
        }

        if (isset($options['logger'])) {
            $this->setLogger($options['logger']);
        }

        if (isset($options['delimiters'])) {
            $this->delimiters = $options['delimiters'];
        }

        // Optional Mustache features

        if (isset($options['dynamic_names'])) {
            $this->dynamicNames = $options['dynamic_names'] !== false;
        }

        if (isset($options['inheritance'])) {
            $this->inheritance = $options['inheritance'] !== false;
        }

        if (isset($options['lambdas'])) {
            $this->lambdas = $options['lambdas'] !== false;
        }

        if (isset($options['pragmas'])) {
            foreach ($options['pragmas'] as $pragma) {
                if (!isset(self::$knownPragmas[$pragma])) {
                    throw new InvalidArgumentException(sprintf('Unknown pragma: "%s"', $pragma));
                }
                $this->pragmas[$pragma] = true;
            }
        }

        if (!$this->lambdas && isset($this->pragmas[self::PRAGMA_FILTERS])) {
            throw new InvalidArgumentException('The FILTERS pragma requires lambda support');
        }

        // Backwards compatibility

        if (isset($options['strict_callables'])) {
            $this->strictCallables = (bool) $options['strict_callables'];
        }

        if (isset($options['buggy_property_shadowing'])) {
            $this->buggyPropertyShadowing = (bool) $options['buggy_property_shadowing'];
        }

        if (isset($options['double_render_lambdas'])) {
            $this->doubleRenderLambdas = (bool) $options['double_render_lambdas'];
        }
    }

    /**
     * Shortcut 'render' invocation.
     *
     * Equivalent to calling `$mustache->loadTemplate($template)->render($context);`
     *
     * @see Mustache\Engine::loadTemplate
     * @see Mustache\Template::render
     *
     * @param string $template
     *
     * @return string Rendered template
     */
    public function render($template, $context = [])
    {
        return $this->loadTemplate($template)->render($context);
    }

    /**
     * Get the current Mustache escape callback.
     *
     * @return callable|null
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * Get the current Mustache entity type to escape.
     *
     * @return int
     */
    public function getEntityFlags()
    {
        return $this->entityFlags;
    }

    /**
     * Get the current Mustache character set.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Check whether to double-render higher-order sections.
     *
     * By default, the return value of higher order sections that are rendered
     * via the lambda helper will *not* be re-rendered. To preserve the
     * behavior of Mustache.php v2.x, set this to true.
     *
     * THIS IS NOT RECOMMENDED.
     */
    public function getDoubleRenderLambdas()
    {
        return $this->doubleRenderLambdas;
    }

    /**
     * Check whether to use buggy property shadowing.
     *
     * THIS IS NOT RECOMMENDED.
     *
     * See https://github.com/bobthecow/mustache.php/pull/410
     */
    public function getBuggyPropertyShadowing()
    {
        return $this->buggyPropertyShadowing;
    }

    /**
     * Get currently enabled optional features.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            'dynamic_names' => $this->dynamicNames,
            'inheritance' => $this->inheritance,
            'lambdas' => $this->lambdas,
        ];
    }

    /**
     * Get the current globally enabled pragmas.
     *
     * @return array
     */
    public function getPragmas()
    {
        return array_keys($this->pragmas);
    }

    /**
     * Set the Mustache template Loader instance.
     */
    public function setLoader(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get the current Mustache template Loader instance.
     *
     * If no Loader instance has been explicitly specified, this method will instantiate and return
     * a StringLoader instance.
     *
     * @return Loader
     */
    public function getLoader()
    {
        if (!isset($this->loader)) {
            $this->loader = new StringLoader();
        }

        return $this->loader;
    }

    /**
     * Set the Mustache partials Loader instance.
     */
    public function setPartialsLoader(Loader $partialsLoader)
    {
        $this->partialsLoader = $partialsLoader;
    }

    /**
     * Get the current Mustache partials Loader instance.
     *
     * If no Loader instance has been explicitly specified, this method will instantiate and return
     * an ArrayLoader instance.
     *
     * @return Loader
     */
    public function getPartialsLoader()
    {
        if (!isset($this->partialsLoader)) {
            $this->partialsLoader = new ArrayLoader();
        }

        return $this->partialsLoader;
    }

    /**
     * Set partials for the current partials Loader instance.
     *
     * @throws RuntimeException If the current Loader instance is immutable
     */
    public function setPartials(array $partials = [])
    {
        if (!isset($this->partialsLoader)) {
            $this->partialsLoader = new ArrayLoader();
        }

        if (!$this->partialsLoader instanceof MutableLoader) {
            throw new RuntimeException('Unable to set partials on an immutable Mustache Loader instance');
        }

        $this->partialsLoader->setTemplates($partials);
    }

    /**
     * Set an array of Mustache helpers.
     *
     * An array of 'helpers'. Helpers can be global variables or objects, closures (e.g. for higher order sections), or
     * any other valid Mustache context value. They will be prepended to the context stack, so they will be available in
     * any template loaded by this Mustache instance.
     *
     * @throws InvalidArgumentException if $helpers is not an array or \Traversable
     *
     * @param array|\Traversable $helpers
     */
    public function setHelpers($helpers)
    {
        if (!is_array($helpers) && !$helpers instanceof \Traversable) {
            throw new InvalidArgumentException('setHelpers expects an array of helpers');
        }

        $this->getHelpers()->clear();

        foreach ($helpers as $name => $helper) {
            $this->addHelper($name, $helper);
        }
    }

    /**
     * Get the current set of Mustache helpers.
     *
     * @see Mustache\Engine::setHelpers
     *
     * @return HelperCollection
     */
    public function getHelpers()
    {
        if (!isset($this->helpers)) {
            $this->helpers = new HelperCollection();
        }

        return $this->helpers;
    }

    /**
     * Add a new Mustache helper.
     *
     * @see Mustache\Engine::setHelpers
     *
     * @param string $name
     * @param mixed  $helper
     */
    public function addHelper($name, $helper)
    {
        $this->getHelpers()->add($name, $helper);
    }

    /**
     * Get a Mustache helper by name.
     *
     * @see Mustache\Engine::setHelpers
     *
     * @param string $name
     *
     * @return mixed Helper
     */
    public function getHelper($name)
    {
        return $this->getHelpers()->get($name);
    }

    /**
     * Check whether this Mustache instance has a helper.
     *
     * @see Mustache\Engine::setHelpers
     *
     * @param string $name
     *
     * @return bool True if the helper is present
     */
    public function hasHelper($name)
    {
        return $this->getHelpers()->has($name);
    }

    /**
     * Remove a helper by name.
     *
     * @see Mustache\Engine::setHelpers
     *
     * @param string $name
     */
    public function removeHelper($name)
    {
        $this->getHelpers()->remove($name);
    }

    /**
     * Set the Mustache Logger instance.
     *
     * @throws InvalidArgumentException If logger is not an instance of Mustache\Logger or Psr\Log\LoggerInterface
     *
     * @param Logger|LoggerInterface $logger
     */
    public function setLogger($logger = null)
    {
        // n.b. this uses `is_a` to prevent a dependency on Psr\Log
        if ($logger !== null && !$logger instanceof Logger && !is_a($logger, 'Psr\\Log\\LoggerInterface')) {
            throw new InvalidArgumentException('Expected an instance of Mustache\\Logger or Psr\\Log\\LoggerInterface.');
        }

        if ($this->getCache()->getLogger() === null) {
            $this->getCache()->setLogger($logger);
        }

        $this->logger = $logger;
    }

    /**
     * Get the current Mustache Logger instance.
     *
     * @return Logger|LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set the Mustache Tokenizer instance.
     */
    public function setTokenizer(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * Get the current Mustache Tokenizer instance.
     *
     * If no Tokenizer instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Tokenizer
     */
    public function getTokenizer()
    {
        if (!isset($this->tokenizer)) {
            $this->tokenizer = new Tokenizer();
        }

        return $this->tokenizer;
    }

    /**
     * Set the Mustache Parser instance.
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get the current Mustache Parser instance.
     *
     * If no Parser instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Parser
     */
    public function getParser()
    {
        if (!isset($this->parser)) {
            $this->parser = new Parser();
        }

        return $this->parser;
    }

    /**
     * Set the Mustache Compiler instance.
     */
    public function setCompiler(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Get the current Mustache Compiler instance.
     *
     * If no Compiler instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Compiler
     */
    public function getCompiler()
    {
        if (!isset($this->compiler)) {
            $this->compiler = new Compiler();
        }

        return $this->compiler;
    }

    /**
     * Set the Mustache Cache instance.
     */
    public function setCache(Cache $cache)
    {
        if (isset($this->logger) && $cache->getLogger() === null) {
            $cache->setLogger($this->getLogger());
        }

        $this->cache = $cache;
    }

    /**
     * Get the current Mustache Cache instance.
     *
     * If no Cache instance has been explicitly specified, this method will instantiate and return a new one.
     *
     * @return Cache
     */
    public function getCache()
    {
        if (!isset($this->cache)) {
            $this->setCache(new NoopCache());
        }

        return $this->cache;
    }

    /**
     * Get the current Lambda Cache instance.
     *
     * If 'cache_lambda_templates' is enabled, this is the default cache instance. Otherwise, it is a NoopCache.
     *
     * @see Mustache\Engine::getCache
     *
     * @return Cache
     */
    protected function getLambdaCache()
    {
        if ($this->cacheLambdaTemplates) {
            return $this->getCache();
        }

        if (!isset($this->lambdaCache)) {
            $this->lambdaCache = new NoopCache();
        }

        return $this->lambdaCache;
    }

    /**
     * Helper method to generate a Mustache template class.
     *
     * This method must be updated any time options are added which make it so
     * the same template could be parsed and compiled multiple different ways.
     *
     * @param string|Source $source
     *
     * @return string Mustache Template class name
     */
    public function getTemplateClassName($source)
    {
        // For the most part, adding a new option here should do the trick.
        //
        // Pick a value here which is unique for each possible way the template
        // could be compiled... but not necessarily unique per option value. See
        // escape below, which only needs to differentiate between 'custom' and
        // 'default' escapes.
        //
        // Keep this list in alphabetical order :)
        $chunks = [
            'charset'         => $this->charset,
            'delimiters'      => $this->delimiters ?: '{{ }}',
            'entityFlags'     => $this->entityFlags,
            'escape'          => isset($this->escape) ? 'custom' : 'default',
            'key'             => ($source instanceof Source) ? $source->getKey() : 'source',
            'options'         => $this->getOptions(),
            'pragmas'         => $this->getPragmas(),
            'strictCallables' => $this->strictCallables,
            'version'         => self::VERSION,
        ];

        $key = json_encode($chunks);

        // Template Source instances have already provided their own source key. For strings, just include the whole
        // source string in the md5 hash.
        if (!$source instanceof Source) {
            $key .= "\n" . $source;
        }

        return $this->templateClassPrefix . md5($key);
    }

    /**
     * Load a Mustache Template by name.
     *
     * @param string $name
     *
     * @return Template
     */
    public function loadTemplate($name)
    {
        return $this->loadSource($this->getLoader()->load($name));
    }

    /**
     * Load a Mustache partial Template by name.
     *
     * This is a helper method used internally by Template instances for loading partial templates. You can most likely
     * ignore it completely.
     *
     * @param string $name
     *
     * @return Template
     */
    public function loadPartial($name)
    {
        try {
            if (isset($this->partialsLoader)) {
                $loader = $this->partialsLoader;
            } elseif (isset($this->loader) && !$this->loader instanceof StringLoader) {
                $loader = $this->loader;
            } else {
                throw new UnknownTemplateException($name);
            }

            return $this->loadSource($loader->load($name));
        } catch (UnknownTemplateException $e) {
            // If the named partial cannot be found, log then return null.
            $this->log(
                Logger::WARNING,
                'Partial not found: "{name}"',
                ['name' => $e->getTemplateName()]
            );
        }
    }

    /**
     * Load a Mustache lambda Template by source.
     *
     * This is a helper method used by Template instances to generate subtemplates for Lambda sections. You can most
     * likely ignore it completely.
     *
     * @param string $source
     * @param string $delims (default: null)
     *
     * @return Template
     */
    public function loadLambda($source, $delims = null)
    {
        if ($delims !== null) {
            $source = $delims . "\n" . $source;
        }

        return $this->loadSource($source, $this->getLambdaCache());
    }

    /**
     * Instantiate and return a Mustache Template instance by source.
     *
     * Optionally provide a Mustache\Cache instance. This is used internally by Mustache\Engine::loadLambda to respect
     * the 'cache_lambda_templates' configuration option.
     *
     * @see Mustache\Engine::loadTemplate
     * @see Mustache\Engine::loadPartial
     * @see Mustache\Engine::loadLambda
     *
     * @param string|Source $source
     * @param Cache         $cache  (default: null)
     *
     * @return Template
     */
    private function loadSource($source, $cache = null)
    {
        $className = $this->getTemplateClassName($source);

        if (!isset($this->templates[$className])) {
            if ($cache === null || !$cache instanceof Cache) {
                $cache = $this->getCache();
            }

            if (!class_exists($className, false)) {
                if (!$cache->load($className)) {
                    $compiled = $this->compile($source);
                    $cache->cache($className, $compiled);
                }
            }

            $this->log(
                Logger::DEBUG,
                'Instantiating template: "{className}"',
                ['className' => $className]
            );

            $this->templates[$className] = new $className($this);
        }

        return $this->templates[$className];
    }

    /**
     * Helper method to tokenize a Mustache template.
     *
     * @see Mustache\Tokenizer::scan
     *
     * @param string $source
     *
     * @return array Tokens
     */
    private function tokenize($source)
    {
        return $this->getTokenizer()->scan($source, $this->delimiters);
    }

    /**
     * Helper method to parse a Mustache template.
     *
     * @see Mustache\Parser::parse
     *
     * @param string $source
     *
     * @return array Token tree
     */
    private function parse($source)
    {
        $parser = $this->getParser();
        $parser->setOptions($this->getOptions());
        $parser->setPragmas($this->getPragmas());

        return $parser->parse($this->tokenize($source));
    }

    /**
     * Helper method to compile a Mustache template.
     *
     * @see Mustache\Compiler::compile
     *
     * @param string|Source $source
     *
     * @return string generated Mustache template class code
     */
    private function compile($source)
    {
        $name = $this->getTemplateClassName($source);

        $this->log(
            Logger::INFO,
            'Compiling template to "{className}" class',
            ['className' => $name]
        );

        if ($source instanceof Source) {
            $source = $source->getSource();
        }
        $tree = $this->parse($source);

        $compiler = $this->getCompiler();
        $compiler->setOptions($this->getOptions());
        $compiler->setPragmas($this->getPragmas());

        return $compiler->compile($source, $tree, $name, isset($this->escape), $this->charset, $this->strictCallables, $this->entityFlags);
    }

    /**
     * Add a log record if logging is enabled.
     *
     * @param int    $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     */
    private function log($level, $message, array $context = [])
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
}
