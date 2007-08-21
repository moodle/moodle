<?php

require_once 'HTMLPurifier/URIFilter.php';

HTMLPurifier_ConfigSchema::define(
    'URI', 'HostBlacklist', array(), 'list',
    'List of strings that are forbidden in the host of any URI. Use it to '.
    'kill domain names of spam, etc. Note that it will catch anything in '.
    'the domain, so <tt>moo.com</tt> will catch <tt>moo.com.example.com</tt>. '.
    'This directive has been available since 1.3.0.'
);

class HTMLPurifier_URIFilter_HostBlacklist extends HTMLPurifier_URIFilter
{
    var $name = 'HostBlacklist';
    var $blacklist = array();
    function prepare($config) {
        $this->blacklist = $config->get('URI', 'HostBlacklist');
    }
    function filter(&$uri, $config, &$context) {
        foreach($this->blacklist as $blacklisted_host_fragment) {
            if (strpos($uri->host, $blacklisted_host_fragment) !== false) {
                return false;
            }
        }
        return true;
    }
}
