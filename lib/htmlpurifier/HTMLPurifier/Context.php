<?php

/**
 * Registry object that contains information about the current context.
 */
class HTMLPurifier_Context
{
    
    /**
     * Private array that stores the references.
     * @private
     */
    var $_storage = array();
    
    /**
     * Registers a variable into the context.
     * @param $name String name
     * @param $ref Variable to be registered
     */
    function register($name, &$ref) {
        if (isset($this->_storage[$name])) {
            trigger_error('Name collision, cannot re-register',
                          E_USER_ERROR);
            return;
        }
        $this->_storage[$name] =& $ref;
    }
    
    /**
     * Retrieves a variable reference from the context.
     * @param $name String name
     */
    function &get($name) {
        if (!isset($this->_storage[$name])) {
            trigger_error('Attempted to retrieve non-existent variable',
                          E_USER_ERROR);
            $var = null; // so we can return by reference
            return $var;
        }
        return $this->_storage[$name];
    }
    
    /**
     * Destorys a variable in the context.
     * @param $name String name
     */
    function destroy($name) {
        if (!isset($this->_storage[$name])) {
            trigger_error('Attempted to destroy non-existent variable',
                          E_USER_ERROR);
            return;
        }
        unset($this->_storage[$name]);
    }
    
    /**
     * Checks whether or not the variable exists.
     * @param $name String name
     */
    function exists($name) {
        return isset($this->_storage[$name]);
    }
    
    /**
     * Loads a series of variables from an associative array
     * @param $context_array Assoc array of variables to load
     */
    function loadArray(&$context_array) {
        foreach ($context_array as $key => $discard) {
            $this->register($key, $context_array[$key]);
        }
    }
    
}

?>