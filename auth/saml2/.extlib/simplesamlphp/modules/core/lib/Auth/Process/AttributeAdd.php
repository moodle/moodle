<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

/**
 * Filter to add attributes.
 *
 * This filter allows you to add attributes to the attribute set being processed.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */
class AttributeAdd extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * Flag which indicates wheter this filter should append new values or replace old values.
     * @var bool
     */
    private $replace = false;

    /**
     * Attributes which should be added/appended.
     *
     * Associative array of arrays.
     * @var array
     */
    private $attributes = [];


    /**
     * Initialize this filter.
     *
     * @param array &$config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert(is_array($config));

        foreach ($config as $name => $values) {
            if (is_int($name)) {
                if ($values === '%replace') {
                    $this->replace = true;
                } else {
                    throw new \Exception('Unknown flag: ' . var_export($values, true));
                }
                continue;
            }

            if (!is_array($values)) {
                $values = [$values];
            }
            foreach ($values as $value) {
                if (!is_string($value)) {
                    throw new \Exception(
                        'Invalid value for attribute ' . $name . ': ' . var_export($values, true)
                    );
                }
            }

            $this->attributes[$name] = $values;
        }
    }


    /**
     * Apply filter to add or replace attributes.
     *
     * Add or replace existing attributes with the configured values.
     *
     * @param array &$request  The current request
     * @return void
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));

        $attributes = &$request['Attributes'];

        foreach ($this->attributes as $name => $values) {
            if ($this->replace === true || !array_key_exists($name, $attributes)) {
                $attributes[$name] = $values;
            } else {
                $attributes[$name] = array_merge($attributes[$name], $values);
            }
        }
    }
}
