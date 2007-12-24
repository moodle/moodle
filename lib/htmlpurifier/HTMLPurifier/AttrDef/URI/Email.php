<?php

require_once 'HTMLPurifier/AttrDef.php';

class HTMLPurifier_AttrDef_URI_Email extends HTMLPurifier_AttrDef
{
    
    /**
     * Unpacks a mailbox into its display-name and address
     */
    function unpack($string) {
        // needs to be implemented
    }
    
}

// sub-implementations
require_once 'HTMLPurifier/AttrDef/URI/Email/SimpleCheck.php';
