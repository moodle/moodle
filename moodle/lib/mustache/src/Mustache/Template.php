<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2016 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract Mustache Template class.
 *
 * @abstract
 */
abstract class Mustache_Template
{
    /**
     * @var Mustache_Engine
     */
    protected $mustache;

    /**
     * @var bool
     */
    protected $strictCallables = false;

    /**
     * Mustache Template constructor.
     *
     * @param Mustache_Engine $mustache
     */
    public function __construct(Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    /**
     * Mustache Template instances can be treated as a function and rendered by simply calling them.
     *
     *     $m = new Mustache_Engine;
     *     $tpl = $m->loadTemplate('Hello, {{ name }}!');
     *     echo $tpl(array('name' => 'World')); // "Hello, World!"
     *
     * @see Mustache_Template::render
     *
     * @param mixed $context Array or object rendering context (default: array())
     *
     * @return string Rendered template
     */
    public function __invoke($context = array())
    {
        return $this->render($context);
    }

    /**
     * Render this template given the rendering context.
     *
     * @param mixed $context Array or object rendering context (default: array())
     *
     * @return string Rendered template
     */
    public function render($context = array())
    {
        return $this->renderInternal(
            $this->prepareContextStack($context)
        );
    }

    /**
     * Internal rendering method implemented by Mustache Template concrete subclasses.
     *
     * This is where the magic happens :)
     *
     * NOTE: This method is not part of the Mustache.php public API.
     *
     * @param Mustache_Context $context
     * @param string           $indent  (default: '')
     *
     * @return string Rendered template
     */
    abstract public function renderInternal(Mustache_Context $context, $indent = '');

    /**
     * Tests whether a value should be iterated over (e.g. in a section context).
     *
     * In most languages there are two distinct array types: list and hash (or whatever you want to call them). Lists
     * should be iterated, hashes should be treated as objects. Mustache follows this paradigm for Ruby, Javascript,
     * Java, Python, etc.
     *
     * PHP, however, treats lists and hashes as one primitive type: array. So Mustache.php needs a way to distinguish
     * between between a list of things (numeric, normalized array) and a set of variables to be used as section context
     * (associative array). In other words, this will be iterated over:
     *
     *     $items = array(
     *         array('name' => 'foo'),
     *         array('name' => 'bar'),
     *         array('name' => 'baz'),
     *     );
     *
     * ... but this will be used as a section context block:
     *
     *     $items = array(
     *         1        => array('name' => 'foo'),
     *         'banana' => array('name' => 'bar'),
     *         42       => array('name' => 'baz'),
     *     );
     *
     * @param mixed $value
     *
     * @return bool True if the value is 'iterable'
     */
    protected function isIterable($value)
    {
        switch (gettype($value)) {
            case 'object':
                return $value instanceof Traversable;

            case 'array':
                $i = 0;
                foreach ($value as $k => $v) {
                    if ($k !== $i++) {
                        return false;
                    }
                }

                return true;

            default:
                return false;
        }
    }

    /**
     * Helper method to prepare the Context stack.
     *
     * Adds the Mustache HelperCollection to the stack's top context frame if helpers are present.
     *
     * @param mixed $context Optional first context frame (default: null)
     *
     * @return Mustache_Context
     */
    protected function prepareContextStack($context = null)
    {
        $stack = new Mustache_Context();

        $helpers = $this->mustache->getHelpers();
        if (!$helpers->isEmpty()) {
            $stack->push($helpers);
        }

        if (!empty($context)) {
            $stack->push($context);
        }

        return $stack;
    }

    /**
     * Resolve a context value.
     *
     * Invoke the value if it is callable, otherwise return the value.
     *
     * @param mixed            $value
     * @param Mustache_Context $context
     *
     * @return string
     */
    protected function resolveValue($value, Mustache_Context $context)
    {
        if (($this->strictCallables ? is_object($value) : !is_string($value)) && is_callable($value)) {
            return $this->mustache
                ->loadLambda((string) call_user_func($value))
                ->renderInternal($context);
        }

        return $value;
    }
}
