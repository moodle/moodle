<?php

declare(strict_types=1);

namespace SimpleSAML\Module\exampleauth\Auth\Source;

use SimpleSAML\Utils;

/**
 * Example authentication source.
 *
 * This class is an example authentication source which will always return a user with
 * a static set of attributes.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */
class StaticSource extends \SimpleSAML\Auth\Source
{
    /**
     * The attributes we return.
     * @var array
     */
    private $attributes;


    /**
     * Constructor for this authentication source.
     *
     * @param array $info  Information about this authentication source.
     * @param array $config  Configuration.
     */
    public function __construct($info, $config)
    {
        assert(is_array($info));
        assert(is_array($config));

        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        // Parse attributes
        try {
            $this->attributes = Utils\Attributes::normalizeAttributesArray($config);
        } catch (\Exception $e) {
            throw new \Exception('Invalid attributes for authentication source ' .
                $this->authId . ': ' . $e->getMessage());
        }
    }


    /**
     * Log in using static attributes.
     *
     * @param array &$state  Information about the current authentication.
     * @return void
     */
    public function authenticate(&$state)
    {
        assert(is_array($state));
        $state['Attributes'] = $this->attributes;
    }
}
