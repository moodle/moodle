<?php

require_once 'HTMLPurifier/HTMLModule.php';

/**
 * XHTML 1.1 List Module, defines list-oriented elements. Core Module.
 */
class HTMLPurifier_HTMLModule_List extends HTMLPurifier_HTMLModule
{
    
    var $name = 'List';
    var $elements = array('dl', 'dt', 'dd', 'ol', 'ul', 'li');
    var $info = array();
    // According to the abstract schema, the List content set is a fully formed
    // one or more expr, but it invariably occurs in an optional declaration
    // so we're not going to do that subtlety. It might cause trouble
    // if a user defines "List" and expects that multiple lists are
    // allowed to be specified, but then again, that's not very intuitive.
    // Furthermore, the actual XML Schema may disagree. Regardless,
    // we don't have support for such nested expressions without using
    // the incredibly inefficient and draconic Custom ChildDef.
    var $content_sets = array('List' => 'dl | ol | ul', 'Flow' => 'List');
    
    function HTMLPurifier_HTMLModule_List() {
        foreach ($this->elements as $element) {
            $this->info[$element] = new HTMLPurifier_ElementDef();
            $this->info[$element]->attr = array(0 => array('Common'));
            if ($element == 'li' || $element == 'dd') {
                $this->info[$element]->content_model = '#PCDATA | Flow';
                $this->info[$element]->content_model_type = 'optional';
            } elseif ($element == 'ol' || $element == 'ul') {
                $this->info[$element]->content_model = 'li';
                $this->info[$element]->content_model_type = 'required';
            }
        }
        $this->info['dt']->content_model = '#PCDATA | Inline';
        $this->info['dt']->content_model_type = 'optional';
        $this->info['dl']->content_model = 'dt | dd';
        $this->info['dl']->content_model_type = 'required';
        // this could be a LOT more robust
        $this->info['li']->auto_close = array('li' => true);
    }
    
}

?>