<?php

require_once 'HTMLPurifier/ConfigDef.php';

/**
 * Structure object describing of a namespace
 */
class HTMLPurifier_ConfigDef_Namespace extends HTMLPurifier_ConfigDef {
    
    function HTMLPurifier_ConfigDef_Namespace($description = null) {
        $this->description = $description;
    }
    
    var $class = 'namespace';
    
    /**
     * String description of what kinds of directives go in this namespace.
     */
    var $description;
    
}

