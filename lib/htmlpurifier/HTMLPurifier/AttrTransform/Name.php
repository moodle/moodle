<?php

require_once 'HTMLPurifier/AttrTransform.php';

/**
 * Pre-transform that changes deprecated name attribute to ID if necessary
 */
class HTMLPurifier_AttrTransform_Name extends HTMLPurifier_AttrTransform
{
    
    function transform($attr, $config, &$context) {
        
        if (!isset($attr['name'])) return $attr;
        
        $name = $attr['name'];
        unset($attr['name']);
        
        if (isset($attr['id'])) {
            // ID already set, discard name
            return $attr;
        }
        
        $attr['id'] = $name;
        
        return $attr;
        
    }
    
}

?>