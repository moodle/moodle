<?php

class HTMLPurifier_URIDefinition extends HTMLPurifier_Definition
{
    
    public $type = 'URI';
    protected $filters = array();
    protected $registeredFilters = array();
    
    /**
     * HTMLPurifier_URI object of the base specified at %URI.Base
     */
    public $base;
    
    /**
     * String host to consider "home" base, derived off of $base
     */
    public $host;
    
    /**
     * Name of default scheme based on %URI.DefaultScheme and %URI.Base
     */
    public $defaultScheme;
    
    public function __construct() {
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternal());
        $this->registerFilter(new HTMLPurifier_URIFilter_DisableExternalResources());
        $this->registerFilter(new HTMLPurifier_URIFilter_HostBlacklist());
        $this->registerFilter(new HTMLPurifier_URIFilter_MakeAbsolute());
    }
    
    public function registerFilter($filter) {
        $this->registeredFilters[$filter->name] = $filter;
    }
    
    public function addFilter($filter, $config) {
        $filter->prepare($config);
        $this->filters[$filter->name] = $filter;
    }
    
    protected function doSetup($config) {
        $this->setupMemberVariables($config);
        $this->setupFilters($config);
    }
    
    protected function setupFilters($config) {
        foreach ($this->registeredFilters as $name => $filter) {
            $conf = $config->get('URI', $name);
            if ($conf !== false && $conf !== null) {
                $this->addFilter($filter, $config);
            }
        }
        unset($this->registeredFilters);
    }
    
    protected function setupMemberVariables($config) {
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
    
    public function filter(&$uri, $config, $context) {
        foreach ($this->filters as $name => $x) {
            $result = $this->filters[$name]->filter($uri, $config, $context);
            if (!$result) return false;
        }
        return true;
    }
    
}
