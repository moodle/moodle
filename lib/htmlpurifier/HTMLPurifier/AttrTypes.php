<?php

require_once 'HTMLPurifier/AttrDef/HTML/ID.php';
require_once 'HTMLPurifier/AttrDef/HTML/Length.php';
require_once 'HTMLPurifier/AttrDef/HTML/MultiLength.php';
require_once 'HTMLPurifier/AttrDef/HTML/Nmtokens.php';
require_once 'HTMLPurifier/AttrDef/HTML/Pixels.php';
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
     * @public
     */
    var $info = array();
    
    /**
     * Constructs the info array
     */
    function HTMLPurifier_AttrTypes() {
        $this->info['CDATA']    = new HTMLPurifier_AttrDef_Text();
        $this->info['ID']       = new HTMLPurifier_AttrDef_HTML_ID();
        $this->info['Length']   = new HTMLPurifier_AttrDef_HTML_Length();
        $this->info['MultiLength'] = new HTMLPurifier_AttrDef_HTML_MultiLength();
        $this->info['NMTOKENS'] = new HTMLPurifier_AttrDef_HTML_Nmtokens();
        $this->info['Pixels']   = new HTMLPurifier_AttrDef_HTML_Pixels();
        $this->info['Text']     = new HTMLPurifier_AttrDef_Text();
        $this->info['URI']      = new HTMLPurifier_AttrDef_URI();
        
        // number is really a positive integer (one or more digits)
        $this->info['Number']   = new HTMLPurifier_AttrDef_Integer(false, false, true);
    }
}

?>
