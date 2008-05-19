<?php

require_once 'HTMLPurifier/DefinitionCache.php';
require_once 'HTMLPurifier/DefinitionCache/Serializer.php';

HTMLPurifier_ConfigSchema::define(
    'Cache', 'DefinitionImpl', 'Serializer', 'string/null', '
This directive defines which method to use when caching definitions,
the complex data-type that makes HTML Purifier tick. Set to null
to disable caching (not recommended, as you will see a definite
performance degradation). This directive has been available since 2.0.0.
');

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
    var $implementations = array();
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
     * Registers a new definition cache object
     * @param $short Short name of cache object, for reference
     * @param $long Full class name of cache object, for construction 
     */
    function register($short, $long) {
        $this->implementations[$short] = $long;
    }
    
    /**
     * Factory method that creates a cache object based on configuration
     * @param $name Name of definitions handled by cache
     * @param $config Instance of HTMLPurifier_Config
     */
    function &create($type, $config) {
        $method = $config->get('Cache', 'DefinitionImpl');
        if ($method === null) {
            $null = new HTMLPurifier_DefinitionCache_Null($type);
            return $null;
        }
        if (!empty($this->caches[$method][$type])) {
            return $this->caches[$method][$type];
        }
        if (
          isset($this->implementations[$method]) &&
          class_exists($class = $this->implementations[$method])
        ) {
            $cache = new $class($type);
        } else {
            if ($method != 'Serializer') {
                trigger_error("Unrecognized DefinitionCache $method, using Serializer instead", E_USER_WARNING);
            }
            $cache = new HTMLPurifier_DefinitionCache_Serializer($type);
        }
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

