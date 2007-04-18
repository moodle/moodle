<?php

/**
 * XHTML 1.1 Legacy module defines elements that were previously 
 * deprecated.
 * 
 * @note Not all legacy elements have been implemented yet, which
 *       is a bit of a reverse problem as compared to browsers! In
 *       addition, this legacy module may implement a bit more than
 *       mandated by XHTML 1.1.
 * 
 * This module can be used in combination with TransformToStrict in order
 * to transform as many deprecated elements as possible, but retain
 * questionably deprecated elements that do not have good alternatives
 * as well as transform elements that don't have an implementation.
 * See docs/ref-strictness.txt for more details.
 */

class HTMLPurifier_HTMLModule_Legacy extends HTMLPurifier_HTMLModule
{
    
    // incomplete
    
    var $name = 'Legacy';
    var $elements = array('u', 's', 'strike');
    var $non_standalone_elements = array('li', 'ol', 'address', 'blockquote');
    
    function HTMLPurifier_HTMLModule_Legacy() {
        // setup new elements
        foreach ($this->elements as $name) {
            $this->info[$name] = new HTMLPurifier_ElementDef();
            // for u, s, strike, as more elements get added, add
            // conditionals as necessary
            $this->info[$name]->content_model = 'Inline | #PCDATA';
            $this->info[$name]->content_model_type = 'optional';
            $this->info[$name]->attr[0] = array('Common');
        }
        
        // setup modifications to old elements
        foreach ($this->non_standalone_elements as $name) {
            $this->info[$name] = new HTMLPurifier_ElementDef();
            $this->info[$name]->standalone = false;
        }
        
        $this->info['li']->attr['value'] = new HTMLPurifier_AttrDef_Integer();
        $this->info['ol']->attr['start'] = new HTMLPurifier_AttrDef_Integer();
        
        $this->info['address']->content_model = 'Inline | #PCDATA | p';
        $this->info['address']->content_model_type = 'optional';
        $this->info['address']->child = false;
        
        $this->info['blockquote']->content_model = 'Flow | #PCDATA';
        $this->info['blockquote']->content_model_type = 'optional';
        $this->info['blockquote']->child = false;
        
    }
    
}

?>