<?php

require_once 'HTMLPurifier/AttrTransform.php';

/**
 * Pre-transform that changes converts a boolean attribute to fixed CSS
 */
class HTMLPurifier_AttrTransform_BoolToCSS
extends HTMLPurifier_AttrTransform {
    
    /**
     * Name of boolean attribute that is trigger
     */
    var $attr;
    
    /**
     * CSS declarations to add to style, needs trailing semicolon
     */
    var $css;
    
    /**
     * @param $attr string attribute name to convert from
     * @param $css string CSS declarations to add to style (needs semicolon)
     */
    function HTMLPurifier_AttrTransform_BoolToCSS($attr, $css) {
        $this->attr = $attr;
        $this->css  = $css;
    }
    
    function transform($attr, $config, &$context) {
        if (!isset($attr[$this->attr])) return $attr;
        unset($attr[$this->attr]);
        $this->prependCSS($attr, $this->css);
        return $attr;
    }
    
}

