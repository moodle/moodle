<?php

require_once 'HTMLPurifier/URIScheme.php';

/**
 * Validates ftp (File Transfer Protocol) URIs as defined by generic RFC 1738.
 */
class HTMLPurifier_URIScheme_ftp extends HTMLPurifier_URIScheme {
    
    var $default_port = 21;
    var $browsable = true; // usually
    
    function validateComponents(
        $userinfo, $host, $port, $path, $query, $config, &$context
    ) {
        list($userinfo, $host, $port, $path, $query) = 
            parent::validateComponents(
                $userinfo, $host, $port, $path, $query, $config, $context );
        $semicolon_pos = strrpos($path, ';'); // reverse
        if ($semicolon_pos !== false) {
            // typecode check
            $type = substr($path, $semicolon_pos + 1); // no semicolon
            $path = substr($path, 0, $semicolon_pos);
            $type_ret = '';
            if (strpos($type, '=') !== false) {
                // figure out whether or not the declaration is correct
                list($key, $typecode) = explode('=', $type, 2);
                if ($key !== 'type') {
                    // invalid key, tack it back on encoded
                    $path .= '%3B' . $type;
                } elseif ($typecode === 'a' || $typecode === 'i' || $typecode === 'd') {
                    $type_ret = ";type=$typecode";
                }
            } else {
                $path .= '%3B' . $type;
            }
            $path = str_replace(';', '%3B', $path);
            $path .= $type_ret;
        }
        return array($userinfo, $host, $port, $path, null);
    }
    
}

?>