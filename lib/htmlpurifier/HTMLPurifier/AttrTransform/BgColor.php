<?php

require_once 'HTMLPurifier/AttrTransform.php';

/**
 * Pre-transform that changes deprecated bgcolor attribute to CSS.
 */
class HTMLPurifier_AttrTransform_BgColor
extends HTMLPurifier_AttrTransform {

    function transform($attr, $config, &$context) {
        
        if (!isset($attr['bgcolor'])) return $attr;
        
        $bgcolor = $attr['bgcolor'];
        unset($attr['bgcolor']);
        // some validation should happen here
        
        $attr['style'] = isset($attr['style']) ? $attr['style'] : '';
        $attr['style'] = "background-color:$bgcolor;" . $attr['style'];
        
        return $attr;
        
    }
    
}

?>