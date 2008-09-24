<?php

require_once 'HTMLPurifier/HTMLModule.php';

/**
 * XHTML 1.1 Ruby Annotation Module, defines elements that indicate
 * short runs of text alongside base text for annotation or pronounciation.
 */
class HTMLPurifier_HTMLModule_Ruby extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Ruby';
    
    function setup($config) {
        $this->addElement('ruby', true, 'Inline',
            'Custom: ((rb, (rt | (rp, rt, rp))) | (rbc, rtc, rtc?))',
            'Common');
        $this->addElement('rbc', true, false, 'Required: rb', 'Common');
        $this->addElement('rtc', true, false, 'Required: rt', 'Common');
        $rb =& $this->addElement('rb', true, false, 'Inline', 'Common');
        $rb->excludes = array('ruby' => true);
        $rt =& $this->addElement('rt', true, false, 'Inline', 'Common', array('rbspan' => 'Number'));
        $rt->excludes = array('ruby' => true);
        $this->addElement('rp', true, false, 'Optional: #PCDATA', 'Common');
    }
    
}

