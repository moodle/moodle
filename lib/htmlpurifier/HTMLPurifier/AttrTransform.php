<?php

/**
 * Processes an entire attribute array for corrections needing multiple values.
 * 
 * Occasionally, a certain attribute will need to be removed and popped onto
 * another value.  Instead of creating a complex return syntax for
 * HTMLPurifier_AttrDef, we just pass the whole attribute array to a
 * specialized object and have that do the special work.  That is the
 * family of HTMLPurifier_AttrTransform.
 * 
 * An attribute transformation can be assigned to run before or after
 * HTMLPurifier_AttrDef validation.  See HTMLPurifier_HTMLDefinition for
 * more details.
 */

class HTMLPurifier_AttrTransform
{
    
    /**
     * Abstract: makes changes to the attributes dependent on multiple values.
     * 
     * @param $attr Assoc array of attributes, usually from
     *              HTMLPurifier_Token_Tag::$attr
     * @param $config Mandatory HTMLPurifier_Config object.
     * @param $context Mandatory HTMLPurifier_Context object
     * @returns Processed attribute array.
     */
    function transform($attr, $config, &$context) {
        trigger_error('Cannot call abstract function', E_USER_ERROR);
    }
}

?>