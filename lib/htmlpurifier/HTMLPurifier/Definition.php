<?php

/**
 * Super-class for definition datatype objects, implements serialization
 * functions for the class.
 */
class HTMLPurifier_Definition
{
    
    /**
     * Has setup() been called yet?
     */
    var $setup = false;
    
    /**
     * What type of definition is it?
     */
    var $type;
    
    /**
     * Sets up the definition object into the final form, something
     * not done by the constructor
     * @param $config HTMLPurifier_Config instance
     */
    function doSetup($config) {
        trigger_error('Cannot call abstract method', E_USER_ERROR);
    }
    
    /**
     * Setup function that aborts if already setup
     * @param $config HTMLPurifier_Config instance
     */
    function setup($config) {
        if ($this->setup) return;
        $this->setup = true;
        $this->doSetup($config);
    }
    
}

