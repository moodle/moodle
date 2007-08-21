<?php

require_once 'HTMLPurifier/HTMLModule.php';

class HTMLPurifier_HTMLModule_XMLCommonAttributes extends HTMLPurifier_HTMLModule
{
    var $name = 'XMLCommonAttributes';
    
    var $attr_collections = array(
        'Lang' => array(
            'xml:lang' => 'LanguageCode',
        )
    );
}

