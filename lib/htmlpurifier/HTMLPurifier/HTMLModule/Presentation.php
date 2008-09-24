<?php

require_once 'HTMLPurifier/HTMLModule.php';

/**
 * XHTML 1.1 Presentation Module, defines simple presentation-related
 * markup. Text Extension Module.
 * @note The official XML Schema and DTD specs further divide this into
 *       two modules:
 *          - Block Presentation (hr)
 *          - Inline Presentation (b, big, i, small, sub, sup, tt)
 *       We have chosen not to heed this distinction, as content_sets
 *       provides satisfactory disambiguation.
 */
class HTMLPurifier_HTMLModule_Presentation extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Presentation';
    
    function setup($config) {
        $this->addElement('b',      true, 'Inline', 'Inline', 'Common');
        $this->addElement('big',    true, 'Inline', 'Inline', 'Common');
        $this->addElement('hr',     true, 'Block',  'Empty',  'Common');
        $this->addElement('i',      true, 'Inline', 'Inline', 'Common');
        $this->addElement('small',  true, 'Inline', 'Inline', 'Common');
        $this->addElement('sub',    true, 'Inline', 'Inline', 'Common');
        $this->addElement('sup',    true, 'Inline', 'Inline', 'Common');
        $this->addElement('tt',     true, 'Inline', 'Inline', 'Common');
    }
    
}

