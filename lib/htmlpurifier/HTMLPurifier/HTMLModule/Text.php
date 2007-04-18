<?php

require_once 'HTMLPurifier/HTMLModule.php';

/**
 * XHTML 1.1 Text Module, defines basic text containers. Core Module.
 * @note In the normative XML Schema specification, this module
 *       is further abstracted into the following modules:
 *          - Block Phrasal (address, blockquote, pre, h1, h2, h3, h4, h5, h6)
 *          - Block Structural (div, p)
 *          - Inline Phrasal (abbr, acronym, cite, code, dfn, em, kbd, q, samp, strong, var)
 *          - Inline Structural (br, span)
 *       We have elected not to follow suite, but this may change.
 */
class HTMLPurifier_HTMLModule_Text extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Text';
    
    var $elements = array('abbr', 'acronym', 'address', 'blockquote',
        'br', 'cite', 'code', 'dfn', 'div', 'em', 'h1', 'h2', 'h3',
        'h4', 'h5', 'h6', 'kbd', 'p', 'pre', 'q', 'samp', 'span', 'strong',
        'var', 'nolink', 'tex', 'algebra'); //moodle modification
    
    var $info = array();
    
    var $content_sets = array(
        'Heading' => 'h1 | h2 | h3 | h4 | h5 | h6',
        'Block' => 'address | blockquote | div | p | pre | nolink | tex | algebra', //moodle modification
        'Inline' => 'abbr | acronym | br | cite | code | dfn | em | kbd | q | samp | span | strong | var',
        'Flow' => 'Heading | Block | Inline'
    );
    
    function HTMLPurifier_HTMLModule_Text() {
        foreach ($this->elements as $element) {
            $this->info[$element] = new HTMLPurifier_ElementDef();
            // attributes
            if ($element == 'br') {
                $this->info[$element]->attr = array(0 => array('Core'));
            } elseif ($element == 'blockquote' || $element == 'q') {
                $this->info[$element]->attr = array(0 => array('Common'), 'cite' => 'URI');
            } else {
                $this->info[$element]->attr = array(0 => array('Common'));
            }
            // content models
            if ($element == 'br') {
                $this->info[$element]->content_model_type = 'empty';
            } elseif ($element == 'blockquote') {
                $this->info[$element]->content_model = 'Heading | Block | List';
                $this->info[$element]->content_model_type = 'optional';
            } elseif ($element == 'div') {
                $this->info[$element]->content_model = '#PCDATA | Flow';
                $this->info[$element]->content_model_type = 'optional';
            } else {
                $this->info[$element]->content_model = '#PCDATA | Inline';
                $this->info[$element]->content_model_type = 'optional';
            }
        }
        // SGML permits exclusions for all descendants, but this is
        // not possible with DTDs or XML Schemas. W3C has elected to
        // use complicated compositions of content_models to simulate
        // exclusion for children, but we go the simpler, SGML-style
        // route of flat-out exclusions. Note that the Abstract Module
        // is blithely unaware of such distinctions.
        $this->info['pre']->excludes = array_flip(array(
            'img', 'big', 'small',
            'object', 'applet', 'font', 'basefont' // generally not allowed
        ));
        $this->info['p']->auto_close = array_flip(array(
            'address', 'blockquote', 'dd', 'dir', 'div', 'dl', 'dt',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'ol', 'p', 'pre',
            'table', 'ul', 'nolink', 'tex', 'algebra' //moodle modification
        ));
    }
    
}

?>