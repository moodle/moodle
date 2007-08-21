<?php

require_once 'HTMLPurifier/URI.php';

/**
 * Parses a URI into the components and fragment identifier as specified
 * by RFC 2396.
 * @todo Replace regexps with a native PHP parser
 */
class HTMLPurifier_URIParser
{
    
    /**
     * Parses a URI
     * @param $uri string URI to parse
     * @return HTMLPurifier_URI representation of URI
     */
    function parse($uri) {
        $r_URI = '!'.
            '(([^:/?#<>\'"]+):)?'. // 2. Scheme
            '(//([^/?#<>\'"]*))?'. // 4. Authority
            '([^?#<>\'"]*)'.       // 5. Path
            '(\?([^#<>\'"]*))?'.   // 7. Query
            '(#([^<>\'"]*))?'.     // 8. Fragment
            '!';
        
        $matches = array();
        $result = preg_match($r_URI, $uri, $matches);
        
        if (!$result) return false; // *really* invalid URI
        
        // seperate out parts
        $scheme     = !empty($matches[1]) ? $matches[2] : null;
        $authority  = !empty($matches[3]) ? $matches[4] : null;
        $path       = $matches[5]; // always present, can be empty
        $query      = !empty($matches[6]) ? $matches[7] : null;
        $fragment   = !empty($matches[8]) ? $matches[9] : null;
        
        // further parse authority
        if ($authority !== null) {
            // ridiculously inefficient: it's a stacked regex!
            $HEXDIG = '[A-Fa-f0-9]';
            $unreserved = 'A-Za-z0-9-._~'; // make sure you wrap with []
            $sub_delims = '!$&\'()'; // needs []
            $pct_encoded = "%$HEXDIG$HEXDIG";
            $r_userinfo = "(?:[$unreserved$sub_delims:]|$pct_encoded)*";
            $r_authority = "/^(($r_userinfo)@)?(\[[^\]]+\]|[^:]*)(:(\d*))?/";
            $matches = array();
            preg_match($r_authority, $authority, $matches);
            $userinfo   = !empty($matches[1]) ? $matches[2] : null;
            $host       = !empty($matches[3]) ? $matches[3] : '';
            $port       = !empty($matches[4]) ? (int) $matches[5] : null;
        } else {
            $port = $host = $userinfo = null;
        }
        
        return new HTMLPurifier_URI(
            $scheme, $userinfo, $host, $port, $path, $query, $fragment);
    }
    
}

