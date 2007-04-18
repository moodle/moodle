<?php

require_once 'HTMLPurifier/URIScheme.php';

// VERY RELAXED! Shouldn't cause problems, not even Firefox checks if the
// email is valid, but be careful!

/**
 * Validates mailto (for E-mail) according to RFC 2368
 * @todo Validate the email address
 * @todo Filter allowed query parameters
 */

class HTMLPurifier_URIScheme_mailto extends HTMLPurifier_URIScheme {
    
    var $browsable = false;
    
    function validateComponents(
        $userinfo, $host, $port, $path, $query, $config, &$context
    ) {
        list($userinfo, $host, $port, $path, $query) = 
            parent::validateComponents(
                $userinfo, $host, $port, $path, $query, $config, $context );
        // we need to validate path against RFC 2368's addr-spec
        return array(null, null, null, $path, $query);
    }
    
}

?>