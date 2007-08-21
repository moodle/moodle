<?php

require_once 'HTMLPurifier/DefinitionCache.php';

HTMLPurifier_ConfigSchema::define(
    'Cache', 'DefinitionImpl', 'Serializer', 'string/null', '
This directive defines which method to use when caching definitions,
the complex data-type that makes HTML Purifier tick. Set to null
to disable caching (not recommended, as you will see a definite
performance degradation). This directive has been available since 2.0.0.
');

HTMLPurifier_ConfigSchema::defineAllowedValues(
    'Cache', 'DefinitionImpl', array('Serializer')
);

HTMLPurifier_ConfigSchema::defineAlias(
    'Core', 'DefinitionCache',
    'Cache', 'DefinitionImpl'
);


/**
 * Responsible for creating definition caches.
 */
class HTMLPurifier_DefinitionCacheFactory
{
    
    var $caches = array('Serializer' => array());
    var $decorators = array();
    
    /**
     * Initialize default decorators
     */
    function setup() {
        $this->addDecorator('Cleanup');
    }
    
    /**
     * Retrieves an instance of global definition cache factory.
     * @static
     */
    function &instance($prototype = null) {
        static $instance;
        if ($prototype !== null) {
            $instance = $prototype;
        } elseif ($instance === null || $prototype === true) {
            $instance = new HTMLPurifier_DefinitionCacheFactory();
            $instance->setup();
        }
        return $instance;
    }
    
    /**
     * Factory method that creates a cache object based on configuration
     * @param $name Name of definitions handled by cache
     * @param $config Instance of HTMLPurifier_Config
     */
    function &create($type, $config) {
        // only one implementation as for right now, $config will
        // be used to determine implementation
        $method = $config->get('Cache', 'DefinitionImpl');
        if ($method === null) {
            $null = new HTMLPurifier_DefinitionCache_Null($type);
            return $null;
        }
        if (!empty($this->caches[$method][$type])) {
            return $this->caches[$method][$type];
        }
        $cache = new HTMLPurifier_DefinitionCache_Serializer($type);
        foreach ($this->decorators as $decorator) {
            $new_cache = $decorator->decorate($cache);
            // prevent infinite recursion in PHP 4
            unset($cache);
            $cache = $new_cache;
        }
        $this->caches[$method][$type] = $cache;
        return $this->caches[$method][$type];
    }
    
    /**
     * Registers a decorator to add to all new cache objects
     * @param 
     */
    function addDecorator($decorator) {
        if (is_string($decorator)) {
            $class = "HTMLPurifier_DefinitionCache_Decorator_$decorator";
            $decorator = new $class;
        }
        $this->decorators[$decorator->name] = $decorator;
    }
    
}

