<?php

require_once 'HTMLPurifier/AttrDef/HTML/FrameTarget.php';

/**
 * XHTML 1.1 Target Module, defines target attribute in link elements.
 */
class HTMLPurifier_HTMLModule_Target extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Target';
    var $elements = array('a');
    
    function HTMLPurifier_HTMLModule_Target() {
        foreach ($this->elements as $e) {
            $this->info[$e] = new HTMLPurifier_ElementDef();
            $this->info[$e]->standalone = false;
            $this->info[$e]->attr = array(
                'target' => new HTMLPurifier_AttrDef_HTML_FrameTarget()
            );
        }
    }
    
}

?>