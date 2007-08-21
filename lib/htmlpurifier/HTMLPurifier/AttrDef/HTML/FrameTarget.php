<?php

HTMLPurifier_ConfigSchema::define(
    'Attr', 'AllowedFrameTargets', array(), 'lookup',
    'Lookup table of all allowed link frame targets.  Some commonly used '.
    'link targets include _blank, _self, _parent and _top. Values should '.
    'be lowercase, as validation will be done in a case-sensitive manner '.
    'despite W3C\'s recommendation. XHTML 1.0 Strict does not permit '.
    'the target attribute so this directive will have no effect in that '.
    'doctype. XHTML 1.1 does not enable the Target module by default, you '.
    'will have to manually enable it (see the module documentation for more details.)'
);

require_once 'HTMLPurifier/AttrDef/Enum.php';

/**
 * Special-case enum attribute definition that lazy loads allowed frame targets
 */
class HTMLPurifier_AttrDef_HTML_FrameTarget extends HTMLPurifier_AttrDef_Enum
{
    
    var $valid_values = false; // uninitialized value
    var $case_sensitive = false;
    
    function HTMLPurifier_AttrDef_HTML_FrameTarget() {}
    
    function validate($string, $config, &$context) {
        if ($this->valid_values === false) $this->valid_values = $config->get('Attr', 'AllowedFrameTargets');
        return parent::validate($string, $config, $context);
    }
    
}

