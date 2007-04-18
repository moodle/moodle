<?php

require_once 'HTMLPurifier/URIScheme.php';

/**
 * Validates news (Usenet) as defined by generic RFC 1738
 */
class HTMLPurifier_URIScheme_news extends HTMLPurifier_URIScheme {
    
    var $browsable = false;
    
    function validateComponents(
        $userinfo, $host, $port, $path, $query, $config, &$context
    ) {
        list($userinfo, $host, $port, $path, $query) = 
            parent::validateComponents(
                $userinfo, $host, $port, $path, $query, $config, $context );
        // typecode check needed on path
        return array(null, null, null, $path, null);
    }
    
}

?>