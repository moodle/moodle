<?php

class HTMLPurifier_HTMLModule_CommonAttributes extends HTMLPurifier_HTMLModule
{
    var $name = 'CommonAttributes';
    
    var $attr_collections = array(
        'Core' => array(
            0 => array('Style'),
            // 'xml:space' => false,
            'class' => 'NMTOKENS',
            'id' => 'ID',
            'title' => 'CDATA',
        ),
        'Lang' => array(
            'xml:lang' => false, // see constructor
        ),
        'I18N' => array(
            0 => array('Lang'), // proprietary, for xml:lang/lang
        ),
        'Common' => array(
            0 => array('Core', 'I18N')
        )
    );
    
    function HTMLPurifier_HTMLModule_CommonAttributes() {
        $this->attr_collections['Lang']['xml:lang'] = new HTMLPurifier_AttrDef_Lang();
    }
}

?>