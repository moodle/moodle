<?php

require_once 'HTMLPurifier/URIFilter.php';

HTMLPurifier_ConfigSchema::define(
    'URI', 'DisableExternal', false, 'bool',
    'Disables links to external websites.  This is a highly effective '.
    'anti-spam and anti-pagerank-leech measure, but comes at a hefty price: no'.
    'links or images outside of your domain will be allowed.  Non-linkified '.
    'URIs will still be preserved.  If you want to be able to link to '.
    'subdomains or use absolute URIs, specify %URI.Host for your website. '.
    'This directive has been available since 1.2.0.'
);

class HTMLPurifier_URIFilter_DisableExternal extends HTMLPurifier_URIFilter
{
    var $name = 'DisableExternal';
    var $ourHostParts = false;
    function prepare($config) {
        $our_host = $config->get('URI', 'Host');
        if ($our_host !== null) $this->ourHostParts = array_reverse(explode('.', $our_host));
    }
    function filter(&$uri, $config, &$context) {
        if (is_null($uri->host)) return true;
        if ($this->ourHostParts === false) return false;
        $host_parts = array_reverse(explode('.', $uri->host));
        foreach ($this->ourHostParts as $i => $x) {
            if (!isset($host_parts[$i])) return false;
            if ($host_parts[$i] != $this->ourHostParts[$i]) return false;
        }
        return true;
    }
}

