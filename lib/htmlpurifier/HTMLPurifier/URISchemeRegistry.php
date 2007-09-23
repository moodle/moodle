<?php

require_once 'HTMLPurifier/URIScheme/http.php';
require_once 'HTMLPurifier/URIScheme/https.php';
require_once 'HTMLPurifier/URIScheme/mailto.php';
require_once 'HTMLPurifier/URIScheme/ftp.php';
require_once 'HTMLPurifier/URIScheme/nntp.php';
require_once 'HTMLPurifier/URIScheme/news.php';

HTMLPurifier_ConfigSchema::define(
    'URI', 'AllowedSchemes', array(
        'http'  => true, // "Hypertext Transfer Protocol", nuf' said
        'https' => true, // HTTP over SSL (Secure Socket Layer)
        // quite useful, but not necessary
        'mailto' => true,// Email
        'ftp'   => true, // "File Transfer Protocol"
        // for Usenet, these two are similar, but distinct
        'nntp'  => true, // individual Netnews articles
        'news'  => true  // newsgroup or individual Netnews articles
    ), 'lookup',
    'Whitelist that defines the schemes that a URI is allowed to have.  This '.
    'prevents XSS attacks from using pseudo-schemes like javascript or mocha.'
);

HTMLPurifier_ConfigSchema::define(
    'URI', 'OverrideAllowedSchemes', true, 'bool',
    'If this is set to true (which it is by default), you can override '.
    '%URI.AllowedSchemes by simply registering a HTMLPurifier_URIScheme '.
    'to the registry.  If false, you will also have to update that directive '.
    'in order to add more schemes.'
);

/**
 * Registry for retrieving specific URI scheme validator objects.
 */
class HTMLPurifier_URISchemeRegistry
{
    
    /**
     * Retrieve sole instance of the registry.
     * @static
     * @param $prototype Optional prototype to overload sole instance with,
     *                   or bool true to reset to default registry.
     * @note Pass a registry object $prototype with a compatible interface and
     *       the function will copy it and return it all further times.
     */
    function &instance($prototype = null) {
        static $instance = null;
        if ($prototype !== null) {
            $instance = $prototype;
        } elseif ($instance === null || $prototype == true) {
            $instance = new HTMLPurifier_URISchemeRegistry();
        }
        return $instance;
    }
    
    /**
     * Cache of retrieved schemes.
     * @protected
     */
    var $schemes = array();
    
    /**
     * Retrieves a scheme validator object
     * @param $scheme String scheme name like http or mailto
     * @param $config HTMLPurifier_Config object
     * @param $config HTMLPurifier_Context object
     */
    function &getScheme($scheme, $config, &$context) {
        if (!$config) $config = HTMLPurifier_Config::createDefault();
        $null = null; // for the sake of passing by reference
        
        // important, otherwise attacker could include arbitrary file
        $allowed_schemes = $config->get('URI', 'AllowedSchemes');
        if (!$config->get('URI', 'OverrideAllowedSchemes') &&
            !isset($allowed_schemes[$scheme])
        ) {
            return $null;
        }
        
        if (isset($this->schemes[$scheme])) return $this->schemes[$scheme];
        if (!isset($allowed_schemes[$scheme])) return $null;
        
        $class = 'HTMLPurifier_URIScheme_' . $scheme;
        if (!class_exists($class)) return $null;
        $this->schemes[$scheme] = new $class();
        return $this->schemes[$scheme];
    }
    
    /**
     * Registers a custom scheme to the cache, bypassing reflection.
     * @param $scheme Scheme name
     * @param $scheme_obj HTMLPurifier_URIScheme object
     */
    function register($scheme, &$scheme_obj) {
        $this->schemes[$scheme] =& $scheme_obj;
    }
    
}


