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
    var $elements = array('del', 'ins');
    var $content_sets = array('Inline' => 'del | ins');
    
    function HTMLPurifier_HTMLModule_Edit() {
        foreach ($this->elements as $element) {
            $this->info[$element] = new HTMLPurifier_ElementDef();
            $this->info[$element]->attr = array(
                0 => array('Common'),
                'cite' => 'URI',
                // 'datetime' => 'Datetime' // Datetime not implemented
            );
            // Inline context ! Block context (exclamation mark is
            // separator, see getChildDef for parsing)
            $this->info[$element]->content_model =
                '#PCDATA | Inline ! #PCDATA | Flow';
            // HTML 4.01 specifies that ins/del must not contain block
            // elements when used in an inline context, chameleon is
            // a complicated workaround to acheive this effect
            $this->info[$element]->content_model_type = 'chameleon';
        }
    }
    
    var $defines_child_def = true;
    function getChildDef($def) {
        if ($def->content_model_type != 'chameleon') return false;
        $value = explode('!', $def->content_model);
        return new HTMLPurifier_ChildDef_Chameleon($value[0], $value[1]);
    }
    
}

?>