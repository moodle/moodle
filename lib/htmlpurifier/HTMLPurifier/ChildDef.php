<?php

// HTMLPurifier_ChildDef and inheritance have three types of output:
// true = leave nodes as is
// false = delete parent node and all children
// array(...) = replace children nodes with these

HTMLPurifier_ConfigSchema::define(
    'Core', 'EscapeInvalidChildren', false, 'bool',
    'When true, a child is found that is not allowed in the context of the '.
    'parent element will be transformed into text as if it were ASCII. When '.
    'false, that element and all internal tags will be dropped, though text '.
    'will be preserved.  There is no option for dropping the element but '.
    'preserving child nodes.'
);

/**
 * Defines allowed child nodes and validates tokens against it.
 */
class HTMLPurifier_ChildDef
{
    /**
     * Type of child definition, usually right-most part of class name lowercase.
     * Used occasionally in terms of context.
     * @public
     */
    var $type;
    
    /**
     * Bool that indicates whether or not an empty array of children is okay
     * 
     * This is necessary for redundant checking when changes affecting
     * a child node may cause a parent node to now be disallowed.
     * 
     * @public
     */
    var $allow_empty;
    
    /**
     * Lookup array of all elements that this definition could possibly allow
     */
    var $elements = array();
    
    /**
     * Validates nodes according to definition and returns modification.
     * 
     * @public
     * @param $tokens_of_children Array of HTMLPurifier_Token
     * @param $config HTMLPurifier_Config object
     * @param $context HTMLPurifier_Context object
     * @return bool true to leave nodes as is
     * @return bool false to remove parent node
     * @return array of replacement child tokens
     */
    function validateChildren($tokens_of_children, $config, &$context) {
        trigger_error('Call to abstract function', E_USER_ERROR);
    }
}


