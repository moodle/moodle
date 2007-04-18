<?php

require_once 'HTMLPurifier/AttrTransform.php';

/**
 * Pre-transform that changes deprecated border attribute to CSS.
 */
class HTMLPurifier_AttrTransform_Border
extends HTMLPurifier_AttrTransform {

    function transform($attr, $config, &$context) {
        
        if (!isset($attr['border'])) return $attr;
        
        $border_width = $attr['border'];
        unset($attr['border']);
        // some validation should happen here
        
        $attr['style'] = isset($attr['style']) ? $attr['style'] : '';
        $attr['style'] = "border:{$border_width}px solid;" . $attr['style'];
        
        return $attr;
        
    }
    
}

?>