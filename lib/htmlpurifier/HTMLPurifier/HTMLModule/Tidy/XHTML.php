<?php

require_once 'HTMLPurifier/HTMLModule/Tidy.php';
require_once 'HTMLPurifier/AttrTransform/Lang.php';

class HTMLPurifier_HTMLModule_Tidy_XHTML extends
      HTMLPurifier_HTMLModule_Tidy
{
    
    var $name = 'Tidy_XHTML';
    var $defaultLevel = 'medium';
    
    function makeFixes() {
        $r = array();
        $r['@lang'] = new HTMLPurifier_AttrTransform_Lang();
        return $r;
    }
    
}

