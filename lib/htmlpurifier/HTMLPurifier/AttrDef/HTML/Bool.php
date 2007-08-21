<?php

require_once 'HTMLPurifier/AttrDef.php';

/**
 * Validates a boolean attribute
 */
class HTMLPurifier_AttrDef_HTML_Bool extends HTMLPurifier_AttrDef
{
    
    var $name;
    var $minimized = true;
    
    function HTMLPurifier_AttrDef_HTML_Bool($name = false) {$this->name = $name;}
    
    function validate($string, $config, &$context) {
        if (empty($string)) return false;
        return $this->name;
    }
    
    /**
     * @param $string Name of attribute
     */
    function make($string) {
        return new HTMLPurifier_AttrDef_HTML_Bool($string);
    }
    
}

