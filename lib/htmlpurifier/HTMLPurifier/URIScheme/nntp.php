<?php

require_once 'HTMLPurifier/URIScheme.php';

/**
 * Validates nntp (Network News Transfer Protocol) as defined by generic RFC 1738
 */
class HTMLPurifier_URIScheme_nntp extends HTMLPurifier_URIScheme {
    
    var $default_port = 119;
    var $browsable = false;
    
    function validate(&$uri, $config, &$context) {
        parent::validate($uri, $config, $context);
        $uri->userinfo = null;
        $uri->query    = null;
        return true;
    }
    
}

