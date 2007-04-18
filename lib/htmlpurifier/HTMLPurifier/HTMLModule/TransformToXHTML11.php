<?php

/**
 * Proprietary module that transforms XHTML 1.0 deprecated aspects into
 * XHTML 1.1 compliant ones, when possible. For maximum effectiveness,
 * HTMLPurifier_HTMLModule_TransformToStrict must also be loaded
 * (otherwise, elements that were deprecated from Transitional to Strict
 * will not be transformed).
 * 
 * XHTML 1.1 compliant document are automatically XHTML 1.0 compliant too,
 * although they may not be as friendly to legacy browsers.
 */

class HTMLPurifier_HTMLModule_TransformToXHTML11 extends HTMLPurifier_HTMLModule
{
    
    var $name = 'TransformToXHTML11';
    var $attr_collections = array(
        'Lang' => array(
            'lang' => false // remove it
        )
    );
    
    var $info_attr_transform_post = array(
        'lang' => false // remove it
    );
    
}

?>