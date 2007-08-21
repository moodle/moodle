<?php

require_once 'HTMLPurifier/URIScheme.php';

/**
 * Validates http (HyperText Transfer Protocol) as defined by RFC 2616
 */
class HTMLPurifier_URIScheme_http extends HTMLPurifier_URIScheme {
    
    var $default_port = 80;
    var $browsable = true;
    var $hierarchical = true;
    
    function validate(&$uri, $config, &$context) {
        parent::validate($uri, $config, $context);
        $uri->userinfo = null;
        return true;
    }
    
}

