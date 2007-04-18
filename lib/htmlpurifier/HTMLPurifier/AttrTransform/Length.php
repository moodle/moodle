<?php

require_once 'HTMLPurifier/AttrTransform.php';

/**
 * Class for handling width/height length attribute transformations to CSS
 */
class HTMLPurifier_AttrTransform_Length extends HTMLPurifier_AttrTransform
{
    
    var $name;
    var $cssName;
    
    function HTMLPurifier_AttrTransform_Length($name, $css_name = null) {
        $this->name = $name;
        $this->cssName = $css_name ? $css_name : $name;
    }
    
    function transform($attr, $config, &$context) {
        if (!isset($attr[$this->name])) return $attr;
        $length = $attr[$this->name];
        unset($attr[$this->name]);
        if(ctype_digit($length)) $length .= 'px';
        
        $attr['style'] = isset($attr['style']) ? $attr['style'] : '';
        $attr['style'] = $this->cssName . ":$length;" . $attr['style'];
        
        return $attr;
    }
    
}

?>