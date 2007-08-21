<?php

require_once 'HTMLPurifier/AttrTransform.php';

// this MUST be placed in post, as it assumes that any value in dir is valid

HTMLPurifier_ConfigSchema::define(
    'Attr', 'DefaultTextDir', 'ltr', 'string',
    'Defines the default text direction (ltr or rtl) of the document '.
    'being parsed.  This generally is the same as the value of the dir '.
    'attribute in HTML, or ltr if that is not specified.'
);
HTMLPurifier_ConfigSchema::defineAllowedValues(
    'Attr', 'DefaultTextDir', array( 'ltr', 'rtl' )
);

/**
 * Post-trasnform that ensures that bdo tags have the dir attribute set.
 */
class HTMLPurifier_AttrTransform_BdoDir extends HTMLPurifier_AttrTransform
{
    
    function transform($attr, $config, &$context) {
        if (isset($attr['dir'])) return $attr;
        $attr['dir'] = $config->get('Attr', 'DefaultTextDir');
        return $attr;
    }
    
}

