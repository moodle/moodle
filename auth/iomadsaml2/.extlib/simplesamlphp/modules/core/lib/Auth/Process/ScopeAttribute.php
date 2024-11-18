<?php

declare(strict_types=1);

namespace SimpleSAML\Module\core\Auth\Process;

use SimpleSAML\Configuration;

/**
 * Add a scoped variant of an attribute.
 *
 * @package SimpleSAMLphp
 */

class ScopeAttribute extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * The attribute we extract the scope from.
     *
     * @var string
     */
    private $scopeAttribute;

    /**
     * The attribute we want to add scope to.
     *
     * @var string
     */
    private $sourceAttribute;

    /**
     * The attribute we want to add the scoped attributes to.
     *
     * @var string
     */
    private $targetAttribute;

    /**
     * Only modify targetAttribute if it doesn't already exist.
     *
     * @var bool
     */
    private $onlyIfEmpty = false;


    /**
     * Initialize this filter, parse configuration
     *
     * @param array &$config  Configuration information about this filter.
     * @param mixed $reserved  For future use.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);
        assert(is_array($config));

        $cfg = Configuration::loadFromArray($config, 'ScopeAttribute');

        $this->scopeAttribute = $cfg->getString('scopeAttribute');
        $this->sourceAttribute = $cfg->getString('sourceAttribute');
        $this->targetAttribute = $cfg->getString('targetAttribute');
        $this->onlyIfEmpty = $cfg->getBoolean('onlyIfEmpty', false);
    }


    /**
     * Apply this filter to the request.
     *
     * @param array &$request  The current request
     * @return void
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));

        $attributes = &$request['Attributes'];

        if (!isset($attributes[$this->scopeAttribute])) {
            return;
        }

        if (!isset($attributes[$this->sourceAttribute])) {
            return;
        }

        if (!isset($attributes[$this->targetAttribute])) {
            $attributes[$this->targetAttribute] = [];
        }

        if ($this->onlyIfEmpty && count($attributes[$this->targetAttribute]) > 0) {
            return;
        }

        foreach ($attributes[$this->scopeAttribute] as $scope) {
            if (strpos($scope, '@') !== false) {
                $scope = explode('@', $scope, 2);
                $scope = $scope[1];
            }

            foreach ($attributes[$this->sourceAttribute] as $value) {
                $value = $value . '@' . $scope;

                if (in_array($value, $attributes[$this->targetAttribute], true)) {
                    // Already present
                    continue;
                }

                $attributes[$this->targetAttribute][] = $value;
            }
        }
    }
}
