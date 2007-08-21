<?php

/**
 * Allows multiple validators to attempt to validate attribute.
 * 
 * Composite is just what it sounds like: a composite of many validators.
 * This means that multiple HTMLPurifier_AttrDef objects will have a whack
 * at the string.  If one of them passes, that's what is returned.  This is
 * especially useful for CSS values, which often are a choice between
 * an enumerated set of predefined values or a flexible data type.
 */
class HTMLPurifier_AttrDef_CSS_Composite extends HTMLPurifier_AttrDef
{
    
    /**
     * List of HTMLPurifier_AttrDef objects that may process strings
     * @protected
     */
    var $defs;
    
    /**
     * @param $defs List of HTMLPurifier_AttrDef objects
     */
    function HTMLPurifier_AttrDef_CSS_Composite($defs) {
        $this->defs = $defs;
    }
    
    function validate($string, $config, &$context) {
        foreach ($this->defs as $i => $def) {
            $result = $this->defs[$i]->validate($string, $config, $context);
            if ($result !== false) return $result;
        }
        return false;
    }
    
}

