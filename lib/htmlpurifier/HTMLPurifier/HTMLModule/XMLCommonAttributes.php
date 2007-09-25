<?php

require_once 'HTMLPurifier/HTMLModule.php';

class HTMLPurifier_HTMLModule_XMLCommonAttributes extends HTMLPurifier_HTMLModule
{
    var $name = 'XMLCommonAttributes';
    
    var $attr_collections = array(
/* moodle comment - xml:lang breaks our multilang
        'Lang' => array(
            'xml:lang' => 'LanguageCode',
        )
*/
    );
}

