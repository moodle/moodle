<?php

require_once 'HTMLPurifier/Strategy/Composite.php';

require_once 'HTMLPurifier/Strategy/RemoveForeignElements.php';
require_once 'HTMLPurifier/Strategy/MakeWellFormed.php';
require_once 'HTMLPurifier/Strategy/FixNesting.php';
require_once 'HTMLPurifier/Strategy/ValidateAttributes.php';

/**
 * Core strategy composed of the big four strategies.
 */
class HTMLPurifier_Strategy_Core extends HTMLPurifier_Strategy_Composite
{
    
    function HTMLPurifier_Strategy_Core() {
        $this->strategies[] = new HTMLPurifier_Strategy_RemoveForeignElements();
        $this->strategies[] = new HTMLPurifier_Strategy_MakeWellFormed();
        $this->strategies[] = new HTMLPurifier_Strategy_FixNesting();
        $this->strategies[] = new HTMLPurifier_Strategy_ValidateAttributes();
    }
    
}

