<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SimpleSAML\Error;

/**
 * Filter to modify attributes using regular expressions
 *
 * This filter can modify or replace attributes given a regular expression.
 *
 * @author Jacob Christiansen, WAYF
 * @package SimpleSAMLphp
 */
class AttributeAlter extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * Should the pattern found be replaced?
     * @var bool
     */
    private $replace = false;

    /**
     * Should the value found be removed?
     * @var bool
     */
    private $remove = false;

    /**
     * Pattern to search for.
     * @var string
     */
    private $pattern = '';

    /**
     * String to replace the pattern found with.
     * @var string|false
     */
    private $replacement = false;

    /**
     * Attribute to search in
     * @var string
     */
    private $subject = '';

    /**
     * Attribute to place the result in.
     * @var string
     */
    private $target = '';


    /**
     * Initialize this filter.
     *
     * @param array &$config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     * @throws \SimpleSAML\Error\Exception In case of invalid configuration.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert(is_array($config));

        // parse filter configuration
        foreach ($config as $name => $value) {
            if (is_int($name)) {
                // check if this is an option
                if ($value === '%replace') {
                    $this->replace = true;
                } elseif ($value === '%remove') {
                    $this->remove = true;
                } else {
                    throw new Error\Exception('Unknown flag : ' . var_export($value, true));
                }
                continue;
            } elseif ($name === 'pattern') {
                // Set pattern
                $this->pattern = $value;
            } elseif ($name === 'replacement') {
                // Set replacement
                $this->replacement = $value;
            } elseif ($name === 'subject') {
                // Set subject
                $this->subject = $value;
            } elseif ($name === 'target') {
                // Set target
                $this->target = $value;
            }
        }
    }


    /**
     * Apply the filter to modify attributes.
     *
     * Modify existing attributes with the configured values.
     *
     * @param array &$request The current request.
     * @throws \SimpleSAML\Error\Exception In case of invalid configuration.
     * @return void
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));

        // get attributes from request
        $attributes = &$request['Attributes'];

        // check that all required params are set in config
        if (empty($this->pattern) || empty($this->subject)) {
            throw new Error\Exception("Not all params set in config.");
        }

        if (!$this->replace && !$this->remove && $this->replacement === false) {
            throw new Error\Exception(
                "'replacement' must be set if neither '%replace' nor " . "'%remove' are set."
            );
        }

        if (!$this->replace && $this->replacement === null) {
            throw new Error\Exception("'%replace' must be set if 'replacement' is null.");
        }

        if ($this->replace && $this->remove) {
            throw new Error\Exception("'%replace' and '%remove' cannot be used together.");
        }

        if (empty($this->target)) {
            // use subject as target if target is not set
            $this->target = $this->subject;
        }

        if ($this->subject !== $this->target && $this->remove) {
            throw new Error\Exception("Cannot use '%remove' when 'target' is different than 'subject'.");
        }

        if (!array_key_exists($this->subject, $attributes)) {
            // if no such subject, stop gracefully
            return;
        }

        if ($this->replace) {
            // replace the whole value
            foreach ($attributes[$this->subject] as &$value) {
                $matches = [];
                if (preg_match($this->pattern, $value, $matches) > 0) {
                    $new_value = $matches[0];

                    if ($this->replacement !== false) {
                        $new_value = $this->replacement;
                    }

                    if ($this->subject === $this->target) {
                        $value = $new_value;
                    } else {
                        $attributes[$this->target] = [$new_value];
                    }
                }
            }
        } elseif ($this->remove) {
            // remove the whole value
            $removedAttrs = [];
            foreach ($attributes[$this->subject] as $value) {
                $matches = [];
                if (preg_match($this->pattern, $value, $matches) > 0) {
                    $removedAttrs[] = $value;
                }
            }
            $attributes[$this->target] = array_diff($attributes[$this->subject], $removedAttrs);

            if (empty($attributes[$this->target])) {
                unset($attributes[$this->target]);
            }
        } else {
            // replace only the part that matches
            if ($this->subject === $this->target) {
                $attributes[$this->target] = preg_replace(
                    $this->pattern,
                    $this->replacement,
                    $attributes[$this->subject]
                );
            } else {
                /** @psalm-suppress InvalidArgument */
                $attributes[$this->target] = array_diff(
                    preg_replace(
                        $this->pattern,
                        $this->replacement,
                        $attributes[$this->subject]
                    ),
                    $attributes[$this->subject]
                );
            }
        }
    }
}
