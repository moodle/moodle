<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SimpleSAML\Error;
use SimpleSAML\Logger;

/**
 * Filter to create target attribute based on value(s) in source attribute
 *
 * @author Martin van Es, m7
 * @package SimpleSAMLphp
 */
class AttributeValueMap extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * The name of the attribute we should assign values to (ie: the target attribute).
     * @var string
     */
    private $targetattribute = '';

    /**
     * The name of the attribute we should create values from.
     * @var string
     */
    private $sourceattribute = '';

    /**
     * The required $sourceattribute values and target affiliations.
     * @var array
     */
    private $values = [];

    /**
     * Whether $sourceattribute should be kept or not.
     * @var bool
     */
    private $keep = false;

    /**
     * Whether $target attribute values should be replaced by new values or not.
     * @var bool
     */
    private $replace = false;


    /**
     * Initialize the filter.
     *
     * @param array &$config Configuration information about this filter.
     * @param mixed $reserved For future use.
     * @throws \SimpleSAML\Error\Exception If the configuration is not valid.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert(is_array($config));

        // parse configuration
        foreach ($config as $name => $value) {
            if (is_int($name)) {
                // check if this is an option
                if ($value === '%replace') {
                    $this->replace = true;
                } elseif ($value === '%keep') {
                    $this->keep = true;
                } else {
                    // unknown configuration option, log it and ignore the error
                    Logger::warning(
                        "AttributeValueMap: unknown configuration flag '" . var_export($value, true) . "'"
                    );
                }
                continue;
            }

            // set the target attribute
            if ($name === 'targetattribute') {
                $this->targetattribute = $value;
            }

            // set the source attribute
            if ($name === 'sourceattribute') {
                $this->sourceattribute = $value;
            }

            // set the values
            if ($name === 'values') {
                $this->values = $value;
            }
        }

        // now validate it
        if (empty($this->sourceattribute)) {
            throw new Error\Exception("AttributeValueMap: 'sourceattribute' configuration option not set.");
        }
        if (empty($this->targetattribute)) {
            throw new Error\Exception("AttributeValueMap: 'targetattribute' configuration option not set.");
        }
        if (!is_array($this->values)) {
            throw new Error\Exception("AttributeValueMap: 'values' configuration option is not an array.");
        }
    }


    /**
     * Apply filter.
     *
     * @param array &$request The current request
     * @return void
     */
    public function process(&$request)
    {
        Logger::debug('Processing the AttributeValueMap filter.');

        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));
        $attributes = &$request['Attributes'];

        if (!array_key_exists($this->sourceattribute, $attributes)) {
            // the source attribute does not exist, nothing to do here
            return;
        }

        $sourceattribute = $attributes[$this->sourceattribute];
        $targetvalues = [];

        if (is_array($sourceattribute)) {
            foreach ($this->values as $value => $values) {
                if (!is_array($values)) {
                    $values = [$values];
                }
                if (count(array_intersect($values, $sourceattribute)) > 0) {
                    Logger::debug("AttributeValueMap: intersect match for '$value'");
                    $targetvalues[] = $value;
                }
            }
        }

        if (count($targetvalues) > 0) {
            if ($this->replace || !array_key_exists($this->targetattribute, $attributes)) {
                $attributes[$this->targetattribute] = $targetvalues;
            } else {
                $attributes[$this->targetattribute] = array_unique(array_merge(
                    $attributes[$this->targetattribute],
                    $targetvalues
                ));
            }
        }

        if (!$this->keep) {
            // no need to keep the source attribute
            unset($attributes[$this->sourceattribute]);
        }
    }
}
