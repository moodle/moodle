<?php

require_once 'HTMLPurifier/AttrDef.php';
require_once 'HTMLPurifier/AttrDef/URI/IPv4.php';
require_once 'HTMLPurifier/AttrDef/URI/IPv6.php';

/**
 * Validates a host according to the IPv4, IPv6 and DNS (future) specifications.
 */
class HTMLPurifier_AttrDef_URI_Host extends HTMLPurifier_AttrDef
{
    
    /**
     * Instance of HTMLPurifier_AttrDef_URI_IPv4 sub-validator
     */
    var $ipv4;
    
    /**
     * Instance of HTMLPurifier_AttrDef_URI_IPv6 sub-validator
     */
    var $ipv6;
    
    function HTMLPurifier_AttrDef_URI_Host() {
        $this->ipv4 = new HTMLPurifier_AttrDef_URI_IPv4();
        $this->ipv6 = new HTMLPurifier_AttrDef_URI_IPv6();
    }
    
    function validate($string, $config, &$context) {
        $length = strlen($string);
        if ($string === '') return '';
        if ($length > 1 && $string[0] === '[' && $string[$length-1] === ']') {
            //IPv6
            $ip = substr($string, 1, $length - 2);
            $valid = $this->ipv6->validate($ip, $config, $context);
            if ($valid === false) return false;
            return '['. $valid . ']';
        }
        
        // need to do checks on unusual encodings too
        $ipv4 = $this->ipv4->validate($string, $config, $context);
        if ($ipv4 !== false) return $ipv4;
        
        // validate a domain name here, do filtering, etc etc etc
        
        // We could use this, but it would break I18N domain names
        //$match = preg_match('/^[a-z0-9][\w\-\.]*[a-z0-9]$/i', $string);
        //if (!$match) return false;
        
        return $string;
    }
    
}

?>