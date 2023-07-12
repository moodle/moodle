<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SimpleSAML\Error;

/**
 * Attribute filter for running arbitrary PHP code.
 *
 * @package SimpleSAMLphp
 */

class PHP extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * The PHP code that should be run.
     *
     * @var string
     */
    private $code;


    /**
     * Initialize this filter, parse configuration
     *
     * @param array &$config Configuration information about this filter.
     * @param mixed $reserved For future use.
     *
     * @throws \SimpleSAML\Error\Exception if the 'code' option is not defined.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert(is_array($config));

        if (!isset($config['code'])) {
            throw new Error\Exception("core:PHP: missing mandatory configuration option 'code'.");
        }
        $this->code = (string) $config['code'];
    }


    /**
     * Apply the PHP code to the attributes.
     *
     * @param array &$request The current request
     * @return void
     *
     * @scrutinizer ignore-unused
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));

        /**
         * @param array &$attributes
         * @param array &$state
         */
        $function = /** @return void */ function (
            array &$attributes,
            array &$state
        ) {
            eval($this->code);
        };
        $function($request['Attributes'], $request);
    }
}
