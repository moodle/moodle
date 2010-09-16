<?php

/**
 * Validates file as defined by RFC 1630 and RFC 1738.
 */
class HTMLPurifier_URIScheme_file extends HTMLPurifier_URIScheme {

    // Generally file:// URLs are not accessible from most
    // machines, so placing them as an img src is incorrect.
    public $browsable = false;

    public function validate(&$uri, $config, $context) {
        parent::validate($uri, $config, $context);
        // Authentication method is not supported
        $uri->userinfo = null;
        // file:// makes no provisions for accessing the resource
        $uri->port     = null;
        // While it seems to work on Firefox, the querystring has
        // no possible effect and is thus stripped.
        $uri->query    = null;
        return true;
    }

}

// vim: et sw=4 sts=4
