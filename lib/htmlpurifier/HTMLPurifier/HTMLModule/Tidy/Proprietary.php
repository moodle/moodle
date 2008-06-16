<?php

class HTMLPurifier_HTMLModule_Tidy_Proprietary extends HTMLPurifier_HTMLModule_Tidy
{
    
    public $name = 'Tidy_Proprietary';
    public $defaultLevel = 'light';
    
    public function makeFixes() {
        return array();
    }
    
}

