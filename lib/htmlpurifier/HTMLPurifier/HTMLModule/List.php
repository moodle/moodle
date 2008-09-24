<?php

require_once 'HTMLPurifier/HTMLModule.php';

/**
 * XHTML 1.1 List Module, defines list-oriented elements. Core Module.
 */
class HTMLPurifier_HTMLModule_List extends HTMLPurifier_HTMLModule
{
    
    var $name = 'List';
    
    // According to the abstract schema, the List content set is a fully formed
    // one or more expr, but it invariably occurs in an optional declaration
    // so we're not going to do that subtlety. It might cause trouble
    // if a user defines "List" and expects that multiple lists are
    // allowed to be specified, but then again, that's not very intuitive.
    // Furthermore, the actual XML Schema may disagree. Regardless,
    // we don't have support for such nested expressions without using
    // the incredibly inefficient and draconic Custom ChildDef.
    
    var $content_sets = array('Flow' => 'List');
    
    function setup($config) {
        $this->addElement('ol', true, 'List', 'Required: li', 'Common');
        $this->addElement('ul', true, 'List', 'Required: li', 'Common');
        $this->addElement('dl', true, 'List', 'Required: dt | dd', 'Common');
        
        $this->addElement('li', true, false, 'Flow', 'Common');
        
        $this->addElement('dd', true, false, 'Flow', 'Common');
        $this->addElement('dt', true, false, 'Inline', 'Common');
    }
    
}

