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
 *       This module, functionally, does not distinguish between these
 *       sub-modules, but the code is internally structured to reflect
 *       these distinctions.
 */
class HTMLPurifier_HTMLModule_Text extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Text';
    var $content_sets = array(
        'Flow' => 'Heading | Block | Inline'
    );
    
    function setup($config) {
        
        // Inline Phrasal -------------------------------------------------
        $this->addElement('abbr',    true, 'Inline', 'Inline', 'Common');
        $this->addElement('acronym', true, 'Inline', 'Inline', 'Common');
        $this->addElement('cite',    true, 'Inline', 'Inline', 'Common');
        $this->addElement('code',    true, 'Inline', 'Inline', 'Common');
        $this->addElement('dfn',     true, 'Inline', 'Inline', 'Common');
        $this->addElement('em',      true, 'Inline', 'Inline', 'Common');
        $this->addElement('kbd',     true, 'Inline', 'Inline', 'Common');
        $this->addElement('q',       true, 'Inline', 'Inline', 'Common', array('cite' => 'URI'));
        $this->addElement('samp',    true, 'Inline', 'Inline', 'Common');
        $this->addElement('strong',  true, 'Inline', 'Inline', 'Common');
        $this->addElement('var',     true, 'Inline', 'Inline', 'Common');
        
        // Inline Structural ----------------------------------------------
        $this->addElement('span', true, 'Inline', 'Inline', 'Common');
        $this->addElement('br',   true, 'Inline', 'Empty',  'Core');

        // Moodle specific elements - start
        $this->addElement('nolink',  true, 'Inline', 'Flow');
        $this->addElement('tex',     true, 'Inline', 'Flow');
        $this->addElement('algebra', true, 'Inline', 'Flow');
        $this->addElement('lang',    true, 'Inline', 'Flow', 'I18N');
        // Moodle specific elements - end
        
        // Block Phrasal --------------------------------------------------
        $this->addElement('address',     true, 'Block', 'Inline', 'Common');
        $this->addElement('blockquote',  true, 'Block', 'Optional: Heading | Block | List', 'Common', array('cite' => 'URI') );
        $pre =& $this->addElement('pre', true, 'Block', 'Inline', 'Common');
        $pre->excludes = $this->makeLookup(
            'img', 'big', 'small', 'object', 'applet', 'font', 'basefont' );
        $this->addElement('h1', true, 'Heading', 'Inline', 'Common');
        $this->addElement('h2', true, 'Heading', 'Inline', 'Common');
        $this->addElement('h3', true, 'Heading', 'Inline', 'Common');
        $this->addElement('h4', true, 'Heading', 'Inline', 'Common');
        $this->addElement('h5', true, 'Heading', 'Inline', 'Common');
        $this->addElement('h6', true, 'Heading', 'Inline', 'Common');
        
        // Block Structural -----------------------------------------------
        $this->addElement('p', true, 'Block', 'Inline', 'Common');
        $this->addElement('div', true, 'Block', 'Flow', 'Common');
        
    }
    
}

