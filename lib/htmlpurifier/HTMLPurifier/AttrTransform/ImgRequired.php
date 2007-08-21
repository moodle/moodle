<?php

require_once 'HTMLPurifier/AttrTransform.php';

// must be called POST validation

HTMLPurifier_ConfigSchema::define(
    'Attr', 'DefaultInvalidImage', '', 'string',
    'This is the default image an img tag will be pointed to if it does '.
    'not have a valid src attribute.  In future versions, we may allow the '.
    'image tag to be removed completely, but due to design issues, this is '.
    'not possible right now.'
);

HTMLPurifier_ConfigSchema::define(
    'Attr', 'DefaultInvalidImageAlt', 'Invalid image', 'string',
    'This is the content of the alt tag of an invalid image if the user '.
    'had not previously specified an alt attribute.  It has no effect when the '.
    'image is valid but there was no alt attribute present.'
);

/**
 * Transform that supplies default values for the src and alt attributes
 * in img tags, as well as prevents the img tag from being removed
 * because of a missing alt tag. This needs to be registered as both
 * a pre and post attribute transform.
 */
class HTMLPurifier_AttrTransform_ImgRequired extends HTMLPurifier_AttrTransform
{
    
    function transform($attr, $config, &$context) {
        
        $src = true;
        if (!isset($attr['src'])) {
            if ($config->get('Core', 'RemoveInvalidImg')) return $attr;
            $attr['src'] = $config->get('Attr', 'DefaultInvalidImage');
            $src = false;
        }
        
        if (!isset($attr['alt'])) {
            if ($src) {
                $attr['alt'] = basename($attr['src']);
            } else {
                $attr['alt'] = $config->get('Attr', 'DefaultInvalidImageAlt');
            }
        }
        
        return $attr;
        
    }
    
}

