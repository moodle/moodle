<?php

require_once 'HTMLPurifier/DefinitionCache.php';

class HTMLPurifier_DefinitionCache_Decorator extends HTMLPurifier_DefinitionCache
{
    
    /**
     * Cache object we are decorating
     */
    var $cache;
    
    function HTMLPurifier_DefinitionCache_Decorator() {}
    
    /**
     * Lazy decorator function
     * @param $cache Reference to cache object to decorate
     */
    function decorate(&$cache) {
        $decorator = $this->copy();
        // reference is necessary for mocks in PHP 4
        $decorator->cache =& $cache;
        $decorator->type  = $cache->type;
        return $decorator;
    }
    
    /**
     * Cross-compatible clone substitute
     */
    function copy() {
        return new HTMLPurifier_DefinitionCache_Decorator();
    }
    
    function add($def, $config) {
        return $this->cache->add($def, $config);
    }
    
    function set($def, $config) {
        return $this->cache->set($def, $config);
    }
    
    function replace($def, $config) {
        return $this->cache->replace($def, $config);
    }
    
    function get($config) {
        return $this->cache->get($config);
    }
    
    function flush($config) {
        return $this->cache->flush($config);
    }
    
    function cleanup($config) {
        return $this->cache->cleanup($config);
    }
    
}

