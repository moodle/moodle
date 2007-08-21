<?php

require_once 'HTMLPurifier/ConfigDef.php';

/**
 * Structure object describing a directive alias
 */
class HTMLPurifier_ConfigDef_DirectiveAlias extends HTMLPurifier_ConfigDef
{
    var $class = 'alias';
    
    /**
     * Namespace being aliased to
     */
    var $namespace;
    /**
     * Directive being aliased to
     */
    var $name;
    
    function HTMLPurifier_ConfigDef_DirectiveAlias($namespace, $name) {
        $this->namespace = $namespace;
        $this->name = $name;
    }
}

