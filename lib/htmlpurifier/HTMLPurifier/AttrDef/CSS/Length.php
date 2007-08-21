<?php

require_once 'HTMLPurifier/AttrDef.php';
require_once 'HTMLPurifier/AttrDef/CSS/Number.php';

/**
 * Represents a Length as defined by CSS.
 */
class HTMLPurifier_AttrDef_CSS_Length extends HTMLPurifier_AttrDef
{
    
    /**
     * Valid unit lookup table.
     * @warning The code assumes all units are two characters long.  Be careful
     *          if we have to change this behavior!
     */
    var $units = array('em' => true, 'ex' => true, 'px' => true, 'in' => true,
         'cm' => true, 'mm' => true, 'pt' => true, 'pc' => true);
    /**
     * Instance of HTMLPurifier_AttrDef_Number to defer number validation to
     */
    var $number_def;
    
    /**
     * @param $non_negative Bool indication whether or not negative values are
     *                      allowed.
     */
    function HTMLPurifier_AttrDef_CSS_Length($non_negative = false) {
        $this->number_def = new HTMLPurifier_AttrDef_CSS_Number($non_negative);
    }
    
    function validate($length, $config, &$context) {
        
        $length = $this->parseCDATA($length);
        if ($length === '') return false;
        if ($length === '0') return '0';
        $strlen = strlen($length);
        if ($strlen === 1) return false; // impossible!
        
        // we assume all units are two characters
        $unit = substr($length, $strlen - 2);
        if (!ctype_lower($unit)) $unit = strtolower($unit);
        $number = substr($length, 0, $strlen - 2);
        
        if (!isset($this->units[$unit])) return false;
        
        $number = $this->number_def->validate($number, $config, $context);
        if ($number === false) return false;
        
        return $number . $unit;
        
    }
    
}

