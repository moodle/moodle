<?php

require_once 'HTMLPurifier/ChildDef.php';

/**
 * Definition that disallows all elements.
 * @warning validateChildren() in this class is actually never called, because
 *          empty elements are corrected in HTMLPurifier_Strategy_MakeWellFormed
 *          before child definitions are parsed in earnest by
 *          HTMLPurifier_Strategy_FixNesting.
 */
class HTMLPurifier_ChildDef_Empty extends HTMLPurifier_ChildDef
{
    var $allow_empty = true;
    var $type = 'empty';
    function HTMLPurifier_ChildDef_Empty() {}
    function validateChildren($tokens_of_children, $config, &$context) {
        return array();
    }
}

