<?php

require_once 'HTMLPurifier/AttrTransform.php';

/**
 * Generic pre-transform that converts an attribute with a fixed number of
 * values (enumerated) to CSS.
 */
class HTMLPurifier_AttrTransform_EnumToCSS extends HTMLPurifier_AttrTransform {
    
    /**
     * Name of attribute to transform from
     */
    var $attr;
    
    /**
     * Lookup array of attribute values to CSS
     */
    var $enumToCSS = array();
    
    /**
     * Case sensitivity of the matching
     * @warning Currently can only be guaranteed to work with ASCII
     *          values.
     */
    var $caseSensitive = false;
    
    /**
     * @param $attr String attribute name to transform from
     * @param $enumToCSS Lookup array of attribute values to CSS
     * @param $case_sensitive Boolean case sensitivity indicator, default false
     */
    function HTMLPurifier_AttrTransform_EnumToCSS($attr, $enum_to_css, $case_sensitive = false) {
        $this->attr = $attr;
        $this->enumToCSS = $enum_to_css;
        $this->caseSensitive = (bool) $case_sensitive;
    }
    
    function transform($attr, $config, &$context) {
        
        if (!isset($attr[$this->attr])) return $attr;
        
        $value = trim($attr[$this->attr]);
        unset($attr[$this->attr]);
        
        if (!$this->caseSensitive) $value = strtolower($value);
        
        if (!isset($this->enumToCSS[$value])) {
            return $attr;
        }
        
        $this->prependCSS($attr, $this->enumToCSS[$value]);
        
        return $attr;
        
    }
    
}

