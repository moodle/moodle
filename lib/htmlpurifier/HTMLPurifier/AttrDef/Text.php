<?php

require_once 'HTMLPurifier/AttrDef.php';

/**
 * Validates arbitrary text according to the HTML spec.
 */
class HTMLPurifier_AttrDef_Text extends HTMLPurifier_AttrDef
{
    
    function validate($string, $config, &$context) {
        return $this->parseCDATA($string);
    }
    
}

