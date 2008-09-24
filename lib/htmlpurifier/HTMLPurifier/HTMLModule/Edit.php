<?php

require_once 'HTMLPurifier/HTMLModule.php';
require_once 'HTMLPurifier/ChildDef/Chameleon.php';

/**
 * XHTML 1.1 Edit Module, defines editing-related elements. Text Extension
 * Module.
 */
class HTMLPurifier_HTMLModule_Edit extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Edit';
    
    function setup($config) {
        $contents = 'Chameleon: #PCDATA | Inline ! #PCDATA | Flow';
        $attr = array(
            'cite' => 'URI',
            // 'datetime' => 'Datetime', // not implemented
        );
        $this->addElement('del', true, 'Inline', $contents, 'Common', $attr);
        $this->addElement('ins', true, 'Inline', $contents, 'Common', $attr);
    }
    
    // HTML 4.01 specifies that ins/del must not contain block
    // elements when used in an inline context, chameleon is
    // a complicated workaround to acheive this effect
    
    // Inline context ! Block context (exclamation mark is
    // separator, see getChildDef for parsing)
    
    var $defines_child_def = true;
    function getChildDef($def) {
        if ($def->content_model_type != 'chameleon') return false;
        $value = explode('!', $def->content_model);
        return new HTMLPurifier_ChildDef_Chameleon($value[0], $value[1]);
    }
    
}

