<?php

require_once 'HTMLPurifier/URIScheme/http.php';

/**
 * Validates https (Secure HTTP) according to http scheme.
 */
class HTMLPurifier_URIScheme_https extends HTMLPurifier_URIScheme_http {
    
    var $default_port = 443;
    
}

