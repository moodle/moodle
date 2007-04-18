<?php

require_once 'HTMLPurifier/AttrDef.php';

// Enum = Enumerated
/**
 * Validates a keyword against a list of valid values.
 */
class HTMLPurifier_AttrDef_Enum extends HTMLPurifier_AttrDef
{
    
    /**
     * Lookup table of valid values.
     */
    var $valid_values   = array();
    
    /**
     * Bool indicating whether or not enumeration is case sensitive.
     * @note In general this is always case insensitive.
     */
    var $case_sensitive = false; // values according to W3C spec
    
    /**
     * @param $valid_values List of valid values
     * @param $case_sensitive Bool indicating whether or not case sensitive
     */
    function HTMLPurifier_AttrDef_Enum(
        $valid_values = array(), $case_sensitive = false
    ) {
        $this->valid_values = array_flip($valid_values);
        $this->case_sensitive = $case_sensitive;
    }
    
    function validate($string, $config, &$context) {
        $string = trim($string);
        if (!$this->case_sensitive) {
            $string = ctype_lower($string) ? $string : strtolower($string);
        }
        $result = isset($this->valid_values[$string]);
        
        return $result ? $string : false;
    }
    
}

?>