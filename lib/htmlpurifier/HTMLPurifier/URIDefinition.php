<?php

require_once 'HTMLPurifier/Definition.php';
require_once 'HTMLPurifier/URIFilter.php';
require_once 'HTMLPurifier/URIParser.php';

require_once 'HTMLPurifier/URIFilter/DisableExternal.php';
require_once 'HTMLPurifier/URIFilter/DisableExternalResources.php';
require_once 'HTMLPurifier/URIFilter/HostBlacklist.php';
require_once 'HTMLPurifier/URIFilter/MakeAbsolute.php';

HTMLPurifier_ConfigSchema::define(
    'URI', 'DefinitionID', null, 'string/null', '
<p>
    Unique identifier for a custom-built URI definition. If you  want
    to add custom URIFilters, you must specify this value.
    This directive has been available since 2.1.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'URI', 'DefinitionRev', 1, 'int', '
<p>
    Revision identifier for your custom definition. See
    %HTML.DefinitionRev for details. This directive has been available
    since 2.1.0.
</p>
');

// informative URI directives

HTMLPurifier_ConfigSchema::define(
    'URI', 'DefaultScheme', 'http', 'string', '
<p>
    Defines through what scheme the output will be served, in order to 
    select the proper object validator when no scheme information is present.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'URI', 'Host', null, 'string/null', '
<p>
    Defines the domain name of the server, so we can determine whether or 
    an absolute URI is from your website or not.  Not strictly necessary, 
    as users should be using relative URIs to reference resources on your 
    website.  It will, however, let you use absolute URIs to link to 
    subdomains of the domain you post here: i.e. example.com will allow 
    sub.example.com.  However, higher up domains will still be excluded: 
    if you set %URI.Host to sub.example.com, example.com will be blocked. 
    <strong>Note:</strong> This directive overrides %URI.Base because
    a given page may be on a sub-domain, but you wish HTML Purifier to be
    more relaxed and allow some of the parent domains too.
    This directive has been available since 1.2.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'URI', 'Base', null, 'string/null', '
<p>
    The base URI is the URI of the document this purified HTML will be
    inserted into.  This information is important if HTML Purifier needs
    to calculate absolute URIs from relative URIs, such as when %URI.MakeAbsolute
    is on.  You may use a non-absolute URI for this value, but behavior
    may vary (%URI.MakeAbsolute deals nicely with both absolute and 
    relative paths, but forwards-compatibility is not guaranteed).
    <strong>Warning:</strong> If set, the scheme on this URI
    overrides the one specified by %URI.DefaultScheme. This directive has
    been available since 2.1.0.
</p>
');

class HTMLPurifier_URIDefinition extends HTMLPurifier_Definition
{
    
    var $type = 'URI';
    var $filters = array();
    var $registeredFilters = array();
    
    /**
     * HTMLPurifier_URI object of the base specified at %URI.Base
     */
    var $base;
    
    /**
     * String host to consider "home" base
     */
    var $host;
    
    /**
     * Name of default scheme based on %URI.DefaultScheme and %URI.Base
     */
    var $defaultScheme;
    
    function HTMLPurifier_URIDefinition() {
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternal());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternalResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_HostBlacklist());
        $this->registerFilter(new HTMLPurifier_URIFilter_MakeAbsolute());
    }
    
    function registerFilter($filter) {
        $this->registeredFilters[$filter->name] = $filter;
    }
    
    function addFilter($filter, $config) {
        $filter->prepare($config);
        $this->filters[$filter->name] = $filter;
    }
    
    function doSetup($config) {
        $this->setupMemberVariables($config);
        $this->setupFilters($config);
    }
    
    function setupFilters($config) {
        foreach ($this->registeredFilters as $name => $filter) {
            $conf = $config->get('URI', $name);
            if ($conf !== false && $conf !== null) {
                $this->addFilter($filter, $config);
            }
        }
        unset($this->registeredFilters);
    }
    
    function setupMemberVariables($config) {
        $this->host = $config->get('URI', 'Host');
        $base_uri = $config->get('URI', 'Base');
        if (!is_null($base_uri)) {
            $parser = new HTMLPurifier_URIParser();
            $this->base = $parser->parse($base_uri);
            $this->defaultScheme = $this->base->scheme;
            if (is_null($this->host)) $this->host = $this->base->host;
        }
        if (is_null($this->defaultScheme)) $this->defaultScheme = $config->get('URI', 'DefaultScheme');
    }
    
    function filter(&$uri, $config, &$context) {
        foreach ($this->filters as $name => $x) {
            $result = $this->filters[$name]->filter($uri, $config, $context);
            if (!$result) return false;
        }
        return true;
    }
    
}
