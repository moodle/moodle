<?php

require_once 'HTMLPurifier/AttrDef.php';
require_once 'HTMLPurifier/URIParser.php';
require_once 'HTMLPurifier/URIScheme.php';
require_once 'HTMLPurifier/URISchemeRegistry.php';
require_once 'HTMLPurifier/AttrDef/URI/Host.php';
require_once 'HTMLPurifier/PercentEncoder.php';
require_once 'HTMLPurifier/AttrDef/URI/Email.php';

// special case filtering directives 

HTMLPurifier_ConfigSchema::define(
    'URI', 'Munge', null, 'string/null', '
<p>
    Munges all browsable (usually http, https and ftp)
    absolute URI\'s into another URI, usually a URI redirection service.
    This directive accepts a URI, formatted with a <code>%s</code> where 
    the url-encoded original URI should be inserted (sample: 
    <code>http://www.google.com/url?q=%s</code>).
</p>
<p>
    Uses for this directive:
</p>
<ul>
    <li>
        Prevent PageRank leaks, while being fairly transparent 
        to users (you may also want to add some client side JavaScript to 
        override the text in the statusbar). <strong>Notice</strong>:
        Many security experts believe that this form of protection does not deter spam-bots. 
    </li>
    <li>
        Redirect users to a splash page telling them they are leaving your
        website. While this is poor usability practice, it is often mandated
        in corporate environments.
    </li>
</ul>
<p>
    This directive has been available since 1.3.0.
</p>
');

// disabling directives

HTMLPurifier_ConfigSchema::define(
    'URI', 'Disable', false, 'bool', '
<p>
    Disables all URIs in all forms. Not sure why you\'d want to do that 
    (after all, the Internet\'s founded on the notion of a hyperlink). 
    This directive has been available since 1.3.0.
</p>
');
HTMLPurifier_ConfigSchema::defineAlias('Attr', 'DisableURI', 'URI', 'Disable');

HTMLPurifier_ConfigSchema::define(
    'URI', 'DisableResources', false, 'bool', '
<p>
    Disables embedding resources, essentially meaning no pictures. You can 
    still link to them though. See %URI.DisableExternalResources for why 
    this might be a good idea. This directive has been available since 1.3.0.
</p>
');

/**
 * Validates a URI as defined by RFC 3986.
 * @note Scheme-specific mechanics deferred to HTMLPurifier_URIScheme
 */
class HTMLPurifier_AttrDef_URI extends HTMLPurifier_AttrDef
{
    
    var $parser;
    var $embedsResource;
    
    /**
     * @param $embeds_resource_resource Does the URI here result in an extra HTTP request?
     */
    function HTMLPurifier_AttrDef_URI($embeds_resource = false) {
        $this->parser = new HTMLPurifier_URIParser();
        $this->embedsResource = (bool) $embeds_resource;
    }
    
    function validate($uri, $config, &$context) {
        
        if ($config->get('URI', 'Disable')) return false;
        
        $uri = $this->parseCDATA($uri);
        
        // parse the URI
        $uri = $this->parser->parse($uri);
        if ($uri === false) return false;
        
        // add embedded flag to context for validators
        $context->register('EmbeddedURI', $this->embedsResource); 
        
        $ok = false;
        do {
            
            // generic validation
            $result = $uri->validate($config, $context);
            if (!$result) break;
            
            // chained filtering
            $uri_def =& $config->getDefinition('URI');
            $result = $uri_def->filter($uri, $config, $context);
            if (!$result) break;
            
            // scheme-specific validation 
            $scheme_obj = $uri->getSchemeObj($config, $context);
            if (!$scheme_obj) break;
            if ($this->embedsResource && !$scheme_obj->browsable) break;
            $result = $scheme_obj->validate($uri, $config, $context);
            if (!$result) break;
            
            // survived gauntlet
            $ok = true;
            
        } while (false);
        
        $context->destroy('EmbeddedURI');
        if (!$ok) return false;
        
        // back to string
        $result = $uri->toString();
        
        // munge entire URI if necessary
        if (
            !is_null($uri->host) && // indicator for authority
            !empty($scheme_obj->browsable) &&
            !is_null($munge = $config->get('URI', 'Munge'))
        ) {
            $result = str_replace('%s', rawurlencode($result), $munge);
        }
        
        return $result;
        
    }
    
}


