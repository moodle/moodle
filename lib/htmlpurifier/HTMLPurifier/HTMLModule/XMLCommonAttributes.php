<?php

class HTMLPurifier_HTMLModule_XMLCommonAttributes extends HTMLPurifier_HTMLModule
{
    public $name = 'XMLCommonAttributes';

    public $attr_collections = array(
/* moodle comment - xml:lang breaks our multilang
        'Lang' => array(
            'xml:lang' => 'LanguageCode',
        )
*/
    );
}

// vim: et sw=4 sts=4
