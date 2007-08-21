<?php

require_once 'HTMLPurifier/AttrTransform.php';

/**
 * Pre-transform that changes deprecated bgcolor attribute to CSS.
 */
class HTMLPurifier_AttrTransform_BgColor
extends HTMLPurifier_AttrTransform {

    function transform($attr, $config, &$context) {
        
        if (!isset($attr['bgcolor'])) return $attr;
        
        $bgcolor = $this->confiscateAttr($attr, 'bgcolor');
        // some validation should happen here
        
        $this->prependCSS($attr, "background-color:$bgcolor;");
        
        return $attr;
        
    }
    
}

