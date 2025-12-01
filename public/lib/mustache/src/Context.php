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

use Mustache\Exception\InvalidArgumentException;

/**
 * Mustache Template rendering Context.
 */
class Context
{
    private $stack      = [];
    private $blockStack = [];

    private $buggyPropertyShadowing = false;

    /**
     * Mustache rendering Context constructor.
     *
     * @param mixed $context                Default rendering context (default: null)
     * @param bool  $buggyPropertyShadowing See Engine::getBuggyPropertyShadowing (default: false)
     */
    public function __construct($context = null, $buggyPropertyShadowing = false)
    {
        if ($context !== null) {
            $this->stack = [$context];
        }

        $this->buggyPropertyShadowing = $buggyPropertyShadowing;
    }

    /**
     * Push a new Context frame onto the stack.
     *
     * @param mixed $value Object or array to use for context
     */
    public function push($value)
    {
        array_push($this->stack, $value);
    }

    /**
     * Push a new Context frame onto the block context stack.
     *
     * @param mixed $value Object or array to use for block context
     */
    public function pushBlockContext($value)
    {
        array_push($this->blockStack, $value);
    }

    /**
     * Pop the last Context frame from the stack.
     *
     * @return mixed Last Context frame (object or array)
     */
    public function pop()
    {
        return array_pop($this->stack);
    }

    /**
     * Pop the last block Context frame from the stack.
     *
     * @return mixed Last block Context frame (object or array)
     */
    public function popBlockContext()
    {
        return array_pop($this->blockStack);
    }

    /**
     * Get the last Context frame.
     *
     * @return mixed Last Context frame (object or array)
     */
    public function last()
    {
        return end($this->stack);
    }

    /**
     * Find a variable in the Context stack.
     *
     * Starting with the last Context frame (the context of the innermost section), and working back to the top-level
     * rendering context, look for a variable with the given name:
     *
     *  * If the Context frame is an associative array which contains the key $id, returns the value of that element.
     *  * If the Context frame is an object, this will check first for a public method, then a public property named
     *    $id. Failing both of these, it will try `__isset` and `__get` magic methods.
     *  * If a value named $id is not found in any Context frame, returns an empty string.
     *
     * @param string $id Variable name
     *
     * @return mixed Variable value, or '' if not found
     */
    public function find($id)
    {
        return $this->findVariableInStack($id, $this->stack);
    }

    /**
     * Find a 'dot notation' variable in the Context stack.
     *
     * Note that dot notation traversal bubbles through scope differently than the regular find method. After finding
     * the initial chunk of the dotted name, each subsequent chunk is searched for only within the value of the previous
     * result. For example, given the following context stack:
     *
     *     $data = [
     *         'name' => 'Fred',
     *         'child' => [
     *             'name' => 'Bob'
     *         ],
     *     ];
     *
     * ... and the Mustache following template:
     *
     *     {{ child.name }}
     *
     * ... the `name` value is only searched for within the `child` value of the global Context, not within parent
     * Context frames.
     *
     * @param string $id              Dotted variable selector
     * @param bool   $strictCallables (default: false)
     *
     * @return mixed Variable value, or '' if not found
     */
    public function findDot($id, $strictCallables = false)
    {
        $chunks = explode('.', $id);
        $first  = array_shift($chunks);
        $value  = $this->findVariableInStack($first, $this->stack);

        // This wasn't really a dotted name, so we can just return the value.
        if (empty($chunks)) {
            return $value;
        }

        foreach ($chunks as $chunk) {
            $isCallable = $strictCallables ? (is_object($value) && is_callable($value)) : (!is_string($value) && is_callable($value));

            if ($isCallable) {
                $value = $value();
            } elseif ($value === '') {
                return $value;
            }

            $value = $this->findVariableInStack($chunk, [$value]);
        }

        return $value;
    }

    /**
     * Find an 'anchored dot notation' variable in the Context stack.
     *
     * This is the same as findDot(), except it looks in the top of the context
     * stack for the first value, rather than searching the whole context stack
     * and starting from there.
     *
     * @see Mustache\Context::findDot
     *
     * @throws InvalidArgumentException if given an invalid anchored dot $id
     *
     * @param string $id Dotted variable selector
     *
     * @return mixed Variable value, or '' if not found
     */
    public function findAnchoredDot($id)
    {
        $chunks = explode('.', $id);
        $first  = array_shift($chunks);
        if ($first !== '') {
            throw new InvalidArgumentException(sprintf('Unexpected id for findAnchoredDot: %s', $id));
        }

        $value  = $this->last();

        foreach ($chunks as $chunk) {
            if ($value === '') {
                return $value;
            }

            $value = $this->findVariableInStack($chunk, [$value]);
        }

        return $value;
    }

    /**
     * Find an argument in the block context stack.
     *
     * @param string $id
     *
     * @return mixed Variable value, or '' if not found
     */
    public function findInBlock($id)
    {
        foreach ($this->blockStack as $context) {
            if (array_key_exists($id, $context)) {
                return $context[$id];
            }
        }

        return '';
    }

    /**
     * Helper function to find a variable in the Context stack.
     *
     * @see Mustache\Context::find
     *
     * @param string $id    Variable name
     * @param array  $stack Context stack
     *
     * @return mixed Variable value, or '' if not found
     */
    private function findVariableInStack($id, array $stack)
    {
        for ($i = count($stack) - 1; $i >= 0; $i--) {
            $frame = &$stack[$i];

            switch (gettype($frame)) {
                case 'object':
                    if (!($frame instanceof \Closure)) {
                        // Note that is_callable() *will not work here*
                        // See https://github.com/bobthecow/mustache.php/wiki/Magic-Methods
                        if (method_exists($frame, $id)) {
                            return $frame->$id();
                        }

                        if (isset($frame->$id)) {
                            return $frame->$id;
                        }

                        // Preserve backwards compatibility with a property shadowing bug in
                        // Mustache.php <= 2.14.2
                        // See https://github.com/bobthecow/mustache.php/pull/410
                        if ($this->buggyPropertyShadowing) {
                            if ($frame instanceof \ArrayAccess && isset($frame[$id])) {
                                return $frame[$id];
                            }
                        } else {
                            if (property_exists($frame, $id)) {
                                $rp = new \ReflectionProperty($frame, $id);
                                if ($rp->isPublic()) {
                                    return $frame->$id;
                                }
                            }

                            if ($frame instanceof \ArrayAccess && $frame->offsetExists($id)) {
                                return $frame[$id];
                            }
                        }
                    }
                    break;

                case 'array':
                    if (array_key_exists($id, $frame)) {
                        return $frame[$id];
                    }
                    break;
            }
        }

        return '';
    }
}
