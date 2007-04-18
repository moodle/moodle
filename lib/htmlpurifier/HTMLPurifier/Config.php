<?php

/**
 * Configuration object that triggers customizable behavior.
 *
 * @warning This class is strongly defined: that means that the class
 *          will fail if an undefined directive is retrieved or set.
 * 
 * @note Many classes that could (although many times don't) use the
 *       configuration object make it a mandatory parameter.  This is
 *       because a configuration object should always be forwarded,
 *       otherwise, you run the risk of missing a parameter and then
 *       being stumped when a configuration directive doesn't work.
 */
class HTMLPurifier_Config
{
    
    /**
     * Two-level associative array of configuration directives
     */
    var $conf;
    
    /**
     * Reference HTMLPurifier_ConfigSchema for value checking
     */
    var $def;
    
    /**
     * Cached instance of HTMLPurifier_HTMLDefinition
     */
    var $html_definition;
    
    /**
     * Cached instance of HTMLPurifier_CSSDefinition
     */
    var $css_definition;
    
    /**
     * @param $definition HTMLPurifier_ConfigSchema that defines what directives
     *                    are allowed.
     */
    function HTMLPurifier_Config(&$definition) {
        $this->conf = $definition->defaults; // set up, copy in defaults
        $this->def  = $definition; // keep a copy around for checking
    }
    
    /**
     * Convenience constructor that creates a config object based on a mixed var
     * @static
     * @param mixed $config Variable that defines the state of the config
     *                      object. Can be: a HTMLPurifier_Config() object,
     *                      an array of directives based on loadArray(),
     *                      or a string filename of an ini file.
     * @return Configured HTMLPurifier_Config object
     */
    function create($config) {
        if (is_a($config, 'HTMLPurifier_Config')) return $config;
        $ret = HTMLPurifier_Config::createDefault();
        if (is_string($config)) $ret->loadIni($config);
        elseif (is_array($config)) $ret->loadArray($config);
        return $ret;
    }
    
    /**
     * Convenience constructor that creates a default configuration object.
     * @static
     * @return Default HTMLPurifier_Config object.
     */
    function createDefault() {
        $definition =& HTMLPurifier_ConfigSchema::instance();
        $config = new HTMLPurifier_Config($definition);
        return $config;
    }
    
    /**
     * Retreives a value from the configuration.
     * @param $namespace String namespace
     * @param $key String key
     */
    function get($namespace, $key, $from_alias = false) {
        if (!isset($this->def->info[$namespace][$key])) {
            trigger_error('Cannot retrieve value of undefined directive',
                E_USER_WARNING);
            return;
        }
        if ($this->def->info[$namespace][$key]->class == 'alias') {
            trigger_error('Cannot get value from aliased directive, use real name',
                E_USER_ERROR);
            return;
        }
        return $this->conf[$namespace][$key];
    }
    
    /**
     * Retreives an array of directives to values from a given namespace
     * @param $namespace String namespace
     */
    function getBatch($namespace) {
        if (!isset($this->def->info[$namespace])) {
            trigger_error('Cannot retrieve undefined namespace',
                E_USER_WARNING);
            return;
        }
        return $this->conf[$namespace];
    }
    
    /**
     * Sets a value to configuration.
     * @param $namespace String namespace
     * @param $key String key
     * @param $value Mixed value
     */
    function set($namespace, $key, $value, $from_alias = false) {
        if (!isset($this->def->info[$namespace][$key])) {
            trigger_error('Cannot set undefined directive to value',
                E_USER_WARNING);
            return;
        }
        if ($this->def->info[$namespace][$key]->class == 'alias') {
            if ($from_alias) {
                trigger_error('Double-aliases not allowed, please fix '.
                    'ConfigSchema bug');
            }
            $this->set($this->def->info[$namespace][$key]->namespace,
                       $this->def->info[$namespace][$key]->name,
                       $value, true);
            return;
        }
        $value = $this->def->validate(
                    $value,
                    $this->def->info[$namespace][$key]->type,
                    $this->def->info[$namespace][$key]->allow_null
                 );
        if (is_string($value)) {
            // resolve value alias if defined
            if (isset($this->def->info[$namespace][$key]->aliases[$value])) {
                $value = $this->def->info[$namespace][$key]->aliases[$value];
            }
            if ($this->def->info[$namespace][$key]->allowed !== true) {
                // check to see if the value is allowed
                if (!isset($this->def->info[$namespace][$key]->allowed[$value])) {
                    trigger_error('Value not supported', E_USER_WARNING);
                    return;
                }
            }
        }
        if ($this->def->isError($value)) {
            trigger_error('Value is of invalid type', E_USER_WARNING);
            return;
        }
        $this->conf[$namespace][$key] = $value;
        if ($namespace == 'HTML' || $namespace == 'Attr') {
            // reset HTML definition if relevant attributes changed
            $this->html_definition = null;
        }
        if ($namespace == 'CSS') {
            $this->css_definition = null;
        }
    }
    
    /**
     * Retrieves reference to the HTML definition.
     * @param $raw Return a copy that has not been setup yet. Must be
     *             called before it's been setup, otherwise won't work.
     */
    function &getHTMLDefinition($raw = false) {
        if (
            empty($this->html_definition) || // hasn't ever been setup
            ($raw && $this->html_definition->setup) // requesting new one
        ) {
            $this->html_definition = new HTMLPurifier_HTMLDefinition($this);
            if ($raw) return $this->html_definition; // no setup!
        }
        if (!$this->html_definition->setup) $this->html_definition->setup();
        return $this->html_definition;
    }
    
    /**
     * Retrieves reference to the CSS definition
     */
    function &getCSSDefinition() {
        if ($this->css_definition === null) {
            $this->css_definition = new HTMLPurifier_CSSDefinition();
            $this->css_definition->setup($this);
        }
        return $this->css_definition;
    }
    
    /**
     * Loads configuration values from an array with the following structure:
     * Namespace.Directive => Value
     * @param $config_array Configuration associative array
     */
    function loadArray($config_array) {
        foreach ($config_array as $key => $value) {
            $key = str_replace('_', '.', $key);
            if (strpos($key, '.') !== false) {
                // condensed form
                list($namespace, $directive) = explode('.', $key);
                $this->set($namespace, $directive, $value);
            } else {
                $namespace = $key;
                $namespace_values = $value;
                foreach ($namespace_values as $directive => $value) {
                    $this->set($namespace, $directive, $value);
                }
            }
        }
    }
    
    /**
     * Loads configuration values from an ini file
     * @param $filename Name of ini file
     */
    function loadIni($filename) {
        $array = parse_ini_file($filename, true);
        $this->loadArray($array);
    }
    
}

?>
