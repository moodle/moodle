<?php

require_once 'HTMLPurifier/AttrDef.php';

HTMLPurifier_ConfigSchema::define(
    'Attr', 'AllowedRel', array(), 'lookup',
    'List of allowed forward document relationships in the rel attribute. '.
    'Common values may be nofollow or print. By default, this is empty, '.
    'meaning that no document relationships are allowed. This directive '.
    'was available since 1.6.0.'
);

HTMLPurifier_ConfigSchema::define(
    'Attr', 'AllowedRev', array(), 'lookup',
    'List of allowed reverse document relationships in the rev attribute. '.
    'This attribute is a bit of an edge-case; if you don\'t know what it '.
    'is for, stay away. This directive was available since 1.6.0.'
);

/**
 * Validates a rel/rev link attribute against a directive of allowed values
 * @note We cannot use Enum because link types allow multiple
 *       values.
 * @note Assumes link types are ASCII text
 */
class HTMLPurifier_AttrDef_HTML_LinkTypes extends HTMLPurifier_AttrDef
{
    
    /** Name config attribute to pull. */
    var $name;
    
    function HTMLPurifier_AttrDef_HTML_LinkTypes($name) {
        $configLookup = array(
            'rel' => 'AllowedRel',
            'rev' => 'AllowedRev'
        );
        if (!isset($configLookup[$name])) {
            trigger_error('Unrecognized attribute name for link '.
                'relationship.', E_USER_ERROR);
            return;
        }
        $this->name = $configLookup[$name];
    }
    
    function validate($string, $config, &$context) {
        
        $allowed = $config->get('Attr', $this->name);
        if (empty($allowed)) return false;
        
        $string = $this->parseCDATA($string);
        $parts = explode(' ', $string);
        
        // lookup to prevent duplicates
        $ret_lookup = array();
        foreach ($parts as $part) {
            $part = strtolower(trim($part));
            if (!isset($allowed[$part])) continue;
            $ret_lookup[$part] = true;
        }
        
        if (empty($ret_lookup)) return false;
        
        $ret_array = array();
        foreach ($ret_lookup as $part => $bool) $ret_array[] = $part;
        $string = implode(' ', $ret_array);
        
        return $string;
        
    }
    
}

