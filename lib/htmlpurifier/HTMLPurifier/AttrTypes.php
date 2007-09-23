<?php

require_once 'HTMLPurifier/AttrDef/Lang.php';
require_once 'HTMLPurifier/AttrDef/Enum.php';
require_once 'HTMLPurifier/AttrDef/HTML/Bool.php';
require_once 'HTMLPurifier/AttrDef/HTML/ID.php';
require_once 'HTMLPurifier/AttrDef/HTML/Length.php';
require_once 'HTMLPurifier/AttrDef/HTML/MultiLength.php';
require_once 'HTMLPurifier/AttrDef/HTML/Nmtokens.php';
require_once 'HTMLPurifier/AttrDef/HTML/Pixels.php';
require_once 'HTMLPurifier/AttrDef/HTML/Color.php';
require_once 'HTMLPurifier/AttrDef/Integer.php';
require_once 'HTMLPurifier/AttrDef/Text.php';
require_once 'HTMLPurifier/AttrDef/URI.php';

/**
 * Provides lookup array of attribute types to HTMLPurifier_AttrDef objects
 */
class HTMLPurifier_AttrTypes
{
    /**
     * Lookup array of attribute string identifiers to concrete implementations
     * @protected
     */
    var $info = array();
    
    /**
     * Constructs the info array, supplying default implementations for attribute
     * types.
     */
    function HTMLPurifier_AttrTypes() {
        // pseudo-types, must be instantiated via shorthand
        $this->info['Enum']    = new HTMLPurifier_AttrDef_Enum();
        $this->info['Bool']    = new HTMLPurifier_AttrDef_HTML_Bool();
        
        $this->info['CDATA']    = new HTMLPurifier_AttrDef_Text();
        $this->info['ID']       = new HTMLPurifier_AttrDef_HTML_ID();
        $this->info['Length']   = new HTMLPurifier_AttrDef_HTML_Length();
        $this->info['MultiLength'] = new HTMLPurifier_AttrDef_HTML_MultiLength();
        $this->info['NMTOKENS'] = new HTMLPurifier_AttrDef_HTML_Nmtokens();
        $this->info['Pixels']   = new HTMLPurifier_AttrDef_HTML_Pixels();
        $this->info['Text']     = new HTMLPurifier_AttrDef_Text();
        $this->info['URI']      = new HTMLPurifier_AttrDef_URI();
        $this->info['LanguageCode'] = new HTMLPurifier_AttrDef_Lang();
        $this->info['Color']    = new HTMLPurifier_AttrDef_HTML_Color();
        
        // unimplemented aliases
        $this->info['ContentType'] = new HTMLPurifier_AttrDef_Text();
        
        // number is really a positive integer (one or more digits)
        // FIXME: ^^ not always, see start and value of list items
        $this->info['Number']   = new HTMLPurifier_AttrDef_Integer(false, false, true);
    }
    
    /**
     * Retrieves a type
     * @param $type String type name
     * @return Object AttrDef for type
     */
    function get($type) {
        
        // determine if there is any extra info tacked on
        if (strpos($type, '#') !== false) list($type, $string) = explode('#', $type, 2);
        else $string = '';
        
        if (!isset($this->info[$type])) {
            trigger_error('Cannot retrieve undefined attribute type ' . $type, E_USER_ERROR);
            return;
        }
        
        return $this->info[$type]->make($string);
        
    }
    
    /**
     * Sets a new implementation for a type
     * @param $type String type name
     * @param $impl Object AttrDef for type
     */
    function set($type, $impl) {
        $this->info[$type] = $impl;
    }
}


