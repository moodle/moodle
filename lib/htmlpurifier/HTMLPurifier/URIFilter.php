<?php

/**
 * Chainable filters for custom URI processing 
 */
class HTMLPurifier_URIFilter
{
    var $name;
    
    /**
     * Performs initialization for the filter
     */
    function prepare($config) {}
    
    /**
     * Filter a URI object
     * @param &$uri Reference to URI object
     * @param $config Instance of HTMLPurifier_Config
     * @param &$context Instance of HTMLPurifier_Context
     */
    function filter(&$uri, $config, &$context) {
        trigger_error('Cannot call abstract function', E_USER_ERROR);
    }
}
