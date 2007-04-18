<?php

require_once 'HTMLPurifier/AttrDef.php';

/**
 * Validates an IPv4 address
 * @author Feyd @ forums.devnetwork.net (public domain)
 */
class HTMLPurifier_AttrDef_URI_IPv4 extends HTMLPurifier_AttrDef
{
    
    /**
     * IPv4 regex, protected so that IPv6 can reuse it
     * @protected
     */
    var $ip4;
    
    function HTMLPurifier_AttrDef_URI_IPv4() {
        $oct = '(?:25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])'; // 0-255
        $this->ip4 = "(?:{$oct}\\.{$oct}\\.{$oct}\\.{$oct})";
    }
    
    function validate($aIP, $config, &$context) {
        
        if (preg_match('#^' . $this->ip4 . '$#s', $aIP))
        {
                return $aIP;
        }
        
        return false;
        
    }
    
}

?>