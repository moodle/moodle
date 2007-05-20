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
    var $elements = array('b', 'big', 'hr', 'i', 'small', 'sub', 'sup', 'tt');
    var $content_sets = array(
        'Block' => 'hr',
        'Inline' => 'b | big | i | small | sub | sup | tt'
    );
    
    function HTMLPurifier_HTMLModule_Presentation() {
        foreach ($this->elements as $element) {
            $this->info[$element] = new HTMLPurifier_ElementDef();
            $this->info[$element]->attr = array(0 => array('Common'));
            if ($element == 'hr') {
                $this->info[$element]->content_model_type = 'empty';
            } else {
                $this->info[$element]->content_model = '#PCDATA | Inline';
                $this->info[$element]->content_model_type = 'optional';
            }
        }
    }
    
}

?>