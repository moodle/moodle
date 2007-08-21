<?php

require_once 'HTMLPurifier/HTMLModule.php';

class HTMLPurifier_HTMLModule_NonXMLCommonAttributes extends HTMLPurifier_HTMLModule
{
    var $name = 'NonXMLCommonAttributes';
    
    var $attr_collections = array(
        'Lang' => array(
            'lang' => 'LanguageCode',
        )
    );
}

