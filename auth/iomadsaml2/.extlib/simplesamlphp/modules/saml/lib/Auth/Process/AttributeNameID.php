<?php

declare(strict_types=1);

namespace SimpleSAML\Module\saml\Auth\Process;

use SimpleSAML\Error;
use SimpleSAML\Logger;

/**
 * Authentication processing filter to create a NameID from an attribute.
 *
 * @package SimpleSAMLphp
 */

class AttributeNameID extends \SimpleSAML\Module\saml\BaseNameIDGenerator
{
    /**
     * The attribute we should use as the NameID.
     *
     * @var string
     */
    private $attribute;


    /**
     * Initialize this filter, parse configuration.
     *
     * @param array $config Configuration information about this filter.
     * @param mixed $reserved For future use.
     *
     * @throws \SimpleSAML\Error\Exception If the required options 'Format' or 'attribute' are missing.
     */
    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);
        assert(is_array($config));

        if (!isset($config['Format'])) {
            throw new Error\Exception("AttributeNameID: Missing required option 'Format'.");
        }
        $this->format = (string) $config['Format'];

        if (!isset($config['attribute'])) {
            throw new Error\Exception("AttributeNameID: Missing required option 'attribute'.");
        }
        $this->attribute = (string) $config['attribute'];
    }


    /**
     * Get the NameID value.
     *
     * @param array $state The state array.
     * @return string|null The NameID value.
     */
    protected function getValue(array &$state)
    {
        if (!isset($state['Attributes'][$this->attribute]) || count($state['Attributes'][$this->attribute]) === 0) {
            Logger::warning(
                'Missing attribute ' . var_export($this->attribute, true) .
                ' on user - not generating attribute NameID.'
            );
            return null;
        }
        if (count($state['Attributes'][$this->attribute]) > 1) {
            Logger::warning(
                'More than one value in attribute ' . var_export($this->attribute, true) .
                ' on user - not generating attribute NameID.'
            );
            return null;
        }
        $value = array_values($state['Attributes'][$this->attribute]); // just in case the first index is no longer 0
        $value = strval($value[0]);

        if (empty($value)) {
            Logger::warning(
                'Empty value in attribute ' . var_export($this->attribute, true) .
                ' on user - not generating attribute NameID.'
            );
            return null;
        }

        return $value;
    }
}
