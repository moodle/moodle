<?php

/**
 * Validator for the components of a URI for a specific scheme
 */
class HTMLPurifier_URIScheme
{
    
    /**
     * Scheme's default port (integer)
     * @public
     */
    var $default_port = null;
    
    /**
     * Whether or not URIs of this schem are locatable by a browser
     * http and ftp are accessible, while mailto and news are not.
     * @public
     */
    var $browsable = false;
    
    /**
     * Validates the components of a URI
     * @note This implementation should be called by children if they define
     *       a default port, as it does port processing.
     * @note Fragment is omitted as that is scheme independent
     * @param $userinfo User info found before at sign in authority
     * @param $host Hostname in authority
     * @param $port Port found after colon in authority
     * @param $path Path of URI
     * @param $query Query of URI, found after question mark
     * @param $config HTMLPurifier_Config object
     * @param $context HTMLPurifier_Context object
     */
    function validateComponents(
        $userinfo, $host, $port, $path, $query, $config, &$context
    ) {
        if ($this->default_port == $port) $port = null;
        return array($userinfo, $host, $port, $path, $query);
    }
    
}

?>