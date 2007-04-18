<?php

require_once 'HTMLPurifier/ChildDef.php';

/**
 * Custom validation class, accepts DTD child definitions
 * 
 * @warning Currently this class is an all or nothing proposition, that is,
 *          it will only give a bool return value.
 * @note This class is currently not used by any code, although it is unit
 *       tested.
 */
class HTMLPurifier_ChildDef_Custom extends HTMLPurifier_ChildDef
{
    var $type = 'custom';
    var $allow_empty = false;
    /**
     * Allowed child pattern as defined by the DTD
     */
    var $dtd_regex;
    /**
     * PCRE regex derived from $dtd_regex
     * @private
     */
    var $_pcre_regex;
    /**
     * @param $dtd_regex Allowed child pattern from the DTD
     */
    function HTMLPurifier_ChildDef_Custom($dtd_regex) {
        $this->dtd_regex = $dtd_regex;
        $this->_compileRegex();
    }
    /**
     * Compiles the PCRE regex from a DTD regex ($dtd_regex to $_pcre_regex)
     */
    function _compileRegex() {
        $raw = str_replace(' ', '', $this->dtd_regex);
        if ($raw{0} != '(') {
            $raw = "($raw)";
        }
        $reg = str_replace(',', ',?', $raw);
        $reg = preg_replace('/([#a-zA-Z0-9_.-]+)/', '(,?\\0)', $reg);
        $this->_pcre_regex = $reg;
    }
    function validateChildren($tokens_of_children, $config, &$context) {
        $list_of_children = '';
        $nesting = 0; // depth into the nest
        foreach ($tokens_of_children as $token) {
            if (!empty($token->is_whitespace)) continue;
            
            $is_child = ($nesting == 0); // direct
            
            if ($token->type == 'start') {
                $nesting++;
            } elseif ($token->type == 'end') {
                $nesting--;
            }
            
            if ($is_child) {
                $list_of_children .= $token->name . ',';
            }
        }
        $list_of_children = rtrim($list_of_children, ',');
        
        $okay =
            preg_match(
                '/^'.$this->_pcre_regex.'$/',
                $list_of_children
            );
        
        return (bool) $okay;
    }
}

?>