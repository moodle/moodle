<?php

require_once 'HTMLPurifier/HTMLModule/Tidy.php';

class HTMLPurifier_HTMLModule_Tidy_Proprietary extends
      HTMLPurifier_HTMLModule_Tidy
{
    
    var $name = 'Tidy_Proprietary';
    var $defaultLevel = 'light';
    
    function makeFixes() {
        return array();
    }
    
}

