<?php

require_once 'HTMLPurifier/DefinitionCache/Serializer.php';
require_once 'HTMLPurifier/DefinitionCache/Null.php';

require_once 'HTMLPurifier/DefinitionCache/Decorator.php';
require_once 'HTMLPurifier/DefinitionCache/Decorator/Memory.php';
require_once 'HTMLPurifier/DefinitionCache/Decorator/Cleanup.php';

/**
 * Abstract class representing Definition cache managers that implements
 * useful common methods and is a factory.
 * @todo Get some sort of versioning variable so the library can easily
 *       invalidate the cache with a new version
 * @todo Make the test runner cache aware and allow the user to easily
 *       flush the cache
 * @todo Create a separate maintenance file advanced users can use to
 *       cache their custom HTMLDefinition, which can be loaded
 *       via a configuration directive
 * @todo Implement memcached
 */
class HTMLPurifier_DefinitionCache
{
    
    var $type;
    
    /**
     * @param $name Type of definition objects this instance of the
     *      cache will handle.
     */
    function HTMLPurifier_DefinitionCache($type) {
        $this->type = $type;
    }
    
    /**
     * Generates a unique identifier for a particular configuration
     * @param Instance of HTMLPurifier_Config
     */
    function generateKey($config) {
        return $config->version . '-' . // possibly replace with function calls
               $config->getBatchSerial($this->type) . '-' .
               $config->get($this->type, 'DefinitionRev');
    }
    
    /**
     * Tests whether or not a key is old with respect to the configuration's
     * version and revision number.
     * @param $key Key to test
     * @param $config Instance of HTMLPurifier_Config to test against
     */
    function isOld($key, $config) {
        if (substr_count($key, '-') < 2) return true;
        list($version, $hash, $revision) = explode('-', $key, 3);
        $compare = version_compare($version, $config->version);
        // version mismatch, is always old
        if ($compare != 0) return true;
        // versions match, ids match, check revision number
        if (
            $hash == $config->getBatchSerial($this->type) &&
            $revision < $config->get($this->type, 'DefinitionRev')
        ) return true;
        return false;
    }
    
    /**
     * Checks if a definition's type jives with the cache's type
     * @note Throws an error on failure
     * @param $def Definition object to check
     * @return Boolean true if good, false if not
     */
    function checkDefType($def) {
        if ($def->type !== $this->type) {
            trigger_error("Cannot use definition of type {$def->type} in cache for {$this->type}");
            return false;
        }
        return true;
    }
    
    /**
     * Adds a definition object to the cache
     */
    function add($def, $config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
    
    /**
     * Unconditionally saves a definition object to the cache
     */
    function set($def, $config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
    
    /**
     * Replace an object in the cache
     */
    function replace($def, $config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
    
    /**
     * Retrieves a definition object from the cache
     */
    function get($config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
    
    /**
     * Removes a definition object to the cache
     */
    function remove($config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
    
    /**
     * Clears all objects from cache
     */
    function flush($config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
    
    /**
     * Clears all expired (older version or revision) objects from cache
     * @note Be carefuly implementing this method as flush. Flush must
     *       not interfere with other Definition types, and cleanup()
     *       should not be repeatedly called by userland code.
     */
    function cleanup($config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
}

