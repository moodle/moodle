<?php

/**
 * Validates a URI as defined by RFC 3986.
 * @note Scheme-specific mechanics deferred to HTMLPurifier_URIScheme
 */
class HTMLPurifier_AttrDef_URI extends HTMLPurifier_AttrDef
{
    
    protected $parser;
    protected $embedsResource;
    
    /**
     * @param $embeds_resource_resource Does the URI here result in an extra HTTP request?
     */
    public function __construct($embeds_resource = false) {
        $this->parser = new HTMLPurifier_URIParser();
        $this->embedsResource = (bool) $embeds_resource;
    }
    
    public function validate($uri, $config, $context) {
        
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
            $uri_def = $config->getDefinition('URI');
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


