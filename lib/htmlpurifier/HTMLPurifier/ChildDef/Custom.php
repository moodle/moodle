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
        $el = '[#a-zA-Z0-9_.-]+';
        $reg = $raw;
        
        // COMPLICATED! AND MIGHT BE BUGGY! I HAVE NO CLUE WHAT I'M
        // DOING! Seriously: if there's problems, please report them.
        
        // collect all elements into the $elements array
        preg_match_all("/$el/", $reg, $matches);
        foreach ($matches[0] as $match) {
            $this->elements[$match] = true;
        }
        
        // setup all elements as parentheticals with leading commas
        $reg = preg_replace("/$el/", '(,\\0)', $reg);
        
        // remove commas when they were not solicited
        $reg = preg_replace("/([^,(|]\(+),/", '\\1', $reg);
        
        // remove all non-paranthetical commas: they are handled by first regex
        $reg = preg_replace("/,\(/", '(', $reg);
        
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
        // add leading comma to deal with stray comma declarations
        $list_of_children = ',' . rtrim($list_of_children, ',');
        $okay =
            preg_match(
                '/^,?'.$this->_pcre_regex.'$/',
                $list_of_children
            );
        
        return (bool) $okay;
    }
}

