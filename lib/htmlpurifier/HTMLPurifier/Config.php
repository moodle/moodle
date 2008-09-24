<?php

require_once 'HTMLPurifier/ConfigSchema.php';

// member variables
require_once 'HTMLPurifier/HTMLDefinition.php';
require_once 'HTMLPurifier/CSSDefinition.php';
require_once 'HTMLPurifier/URIDefinition.php';
require_once 'HTMLPurifier/Doctype.php';
require_once 'HTMLPurifier/DefinitionCacheFactory.php';

// accomodations for versions earlier than 4.3.10 and 5.0.2
// borrowed from PHP_Compat, LGPL licensed, by Aidan Lister <aidan@php.net>
if (!defined('PHP_EOL')) {
    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        case 'WIN':
            define('PHP_EOL', "\r\n");
            break;
        case 'DAR':
            define('PHP_EOL', "\r");
            break;
        default:
            define('PHP_EOL', "\n");
    }
}

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
     * HTML Purifier's version
     */
    var $version = '2.1.5';
    
    /**
     * Two-level associative array of configuration directives
     */
    var $conf;
    
    /**
     * Reference HTMLPurifier_ConfigSchema for value checking
     */
    var $def;
    
    /**
     * Indexed array of definitions
     */
    var $definitions;
    
    /**
     * Bool indicator whether or not config is finalized
     */
    var $finalized = false;
    
    /**
     * Bool indicator whether or not to automatically finalize 
     * the object if a read operation is done
     */
    var $autoFinalize = true;
    
    /**
     * Namespace indexed array of serials for specific namespaces (see
     * getSerial for more info).
     */
    var $serials = array();
    
    /**
     * Serial for entire configuration object
     */
    var $serial;
    
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
        if (is_a($config, 'HTMLPurifier_Config')) {
            // pass-through
            return $config;
        }
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
        if (!$this->finalized && $this->autoFinalize) $this->finalize();
        if (!isset($this->def->info[$namespace][$key])) {
            // can't add % due to SimpleTest bug
            trigger_error('Cannot retrieve value of undefined directive ' . htmlspecialchars("$namespace.$key"),
                E_USER_WARNING);
            return;
        }
        if ($this->def->info[$namespace][$key]->class == 'alias') {
            $d = $this->def->info[$namespace][$key];
            trigger_error('Cannot get value from aliased directive, use real name ' . $d->namespace . '.' . $d->name,
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
        if (!$this->finalized && $this->autoFinalize) $this->finalize();
        if (!isset($this->def->info[$namespace])) {
            trigger_error('Cannot retrieve undefined namespace ' . htmlspecialchars($namespace),
                E_USER_WARNING);
            return;
        }
        return $this->conf[$namespace];
    }
    
    /**
     * Returns a md5 signature of a segment of the configuration object
     * that uniquely identifies that particular configuration
     * @note Revision is handled specially and is removed from the batch
     *       before processing!
     * @param $namespace Namespace to get serial for
     */
    function getBatchSerial($namespace) {
        if (empty($this->serials[$namespace])) {
            $batch = $this->getBatch($namespace);
            unset($batch['DefinitionRev']);
            $this->serials[$namespace] = md5(serialize($batch));
        }
        return $this->serials[$namespace];
    }
    
    /**
     * Returns a md5 signature for the entire configuration object
     * that uniquely identifies that particular configuration
     */
    function getSerial() {
        if (empty($this->serial)) {
            $this->serial = md5(serialize($this->getAll()));
        }
        return $this->serial;
    }
    
    /**
     * Retrieves all directives, organized by namespace
     */
    function getAll() {
        if (!$this->finalized && $this->autoFinalize) $this->finalize();
        return $this->conf;
    }
    
    /**
     * Sets a value to configuration.
     * @param $namespace String namespace
     * @param $key String key
     * @param $value Mixed value
     */
    function set($namespace, $key, $value, $from_alias = false) {
        if ($this->isFinalized('Cannot set directive after finalization')) return;
        if (!isset($this->def->info[$namespace][$key])) {
            trigger_error('Cannot set undefined directive ' . htmlspecialchars("$namespace.$key") . ' to value',
                E_USER_WARNING);
            return;
        }
        if ($this->def->info[$namespace][$key]->class == 'alias') {
            if ($from_alias) {
                trigger_error('Double-aliases not allowed, please fix '.
                    'ConfigSchema bug with' . "$namespace.$key");
            }
            $this->set($this->def->info[$namespace][$key]->namespace,
                       $this->def->info[$namespace][$key]->name,
                       $value, true);
            return;
        }
        $value = $this->def->validate(
                    $value,
                    $type = $this->def->info[$namespace][$key]->type,
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
                    trigger_error('Value not supported, valid values are: ' .
                        $this->_listify($this->def->info[$namespace][$key]->allowed), E_USER_WARNING);
                    return;
                }
            }
        }
        if ($this->def->isError($value)) {
            trigger_error('Value for ' . "$namespace.$key" . ' is of invalid type, should be ' . $type, E_USER_WARNING);
            return;
        }
        $this->conf[$namespace][$key] = $value;
        
        // reset definitions if the directives they depend on changed
        // this is a very costly process, so it's discouraged 
        // with finalization
        if ($namespace == 'HTML' || $namespace == 'CSS') {
            $this->definitions[$namespace] = null;
        }
        
        $this->serials[$namespace] = false;
    }
    
    /**
     * Convenience function for error reporting
     * @private
     */
    function _listify($lookup) {
        $list = array();
        foreach ($lookup as $name => $b) $list[] = $name;
        return implode(', ', $list);
    }
    
    /**
     * Retrieves reference to the HTML definition.
     * @param $raw Return a copy that has not been setup yet. Must be
     *             called before it's been setup, otherwise won't work.
     */
    function &getHTMLDefinition($raw = false) {
        $def =& $this->getDefinition('HTML', $raw);
        return $def; // prevent PHP 4.4.0 from complaining
    }
    
    /**
     * Retrieves reference to the CSS definition
     */
    function &getCSSDefinition($raw = false) {
        $def =& $this->getDefinition('CSS', $raw);
        return $def;
    }
    
    /**
     * Retrieves a definition
     * @param $type Type of definition: HTML, CSS, etc
     * @param $raw  Whether or not definition should be returned raw
     */
    function &getDefinition($type, $raw = false) {
        if (!$this->finalized && $this->autoFinalize) $this->finalize();
        $factory = HTMLPurifier_DefinitionCacheFactory::instance();
        $cache = $factory->create($type, $this);
        if (!$raw) {
            // see if we can quickly supply a definition
            if (!empty($this->definitions[$type])) {
                if (!$this->definitions[$type]->setup) {
                    $this->definitions[$type]->setup($this);
                    $cache->set($this->definitions[$type], $this);
                }
                return $this->definitions[$type];
            }
            // memory check missed, try cache
            $this->definitions[$type] = $cache->get($this);
            if ($this->definitions[$type]) {
                // definition in cache, return it
                return $this->definitions[$type];
            }
        } elseif (
            !empty($this->definitions[$type]) &&
            !$this->definitions[$type]->setup
        ) {
            // raw requested, raw in memory, quick return
            return $this->definitions[$type];
        }
        // quick checks failed, let's create the object
        if ($type == 'HTML') {
            $this->definitions[$type] = new HTMLPurifier_HTMLDefinition();
        } elseif ($type == 'CSS') {
            $this->definitions[$type] = new HTMLPurifier_CSSDefinition();
        } elseif ($type == 'URI') {
            $this->definitions[$type] = new HTMLPurifier_URIDefinition();
        } else {
            trigger_error("Definition of $type type not supported");
            $false = false;
            return $false;
        }
        // quick abort if raw
        if ($raw) {
            if (is_null($this->get($type, 'DefinitionID'))) {
                // fatally error out if definition ID not set
                trigger_error("Cannot retrieve raw version without specifying %$type.DefinitionID", E_USER_ERROR);
                $false = new HTMLPurifier_Error();
                return $false;
            }
            return $this->definitions[$type];
        }
        // set it up
        $this->definitions[$type]->setup($this);
        // save in cache
        $cache->set($this->definitions[$type], $this);
        return $this->definitions[$type];
    }
    
    /**
     * Loads configuration values from an array with the following structure:
     * Namespace.Directive => Value
     * @param $config_array Configuration associative array
     */
    function loadArray($config_array) {
        if ($this->isFinalized('Cannot load directives after finalization')) return;
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
     * Returns a list of array(namespace, directive) for all directives
     * that are allowed in a web-form context as per an allowed
     * namespaces/directives list.
     * @param $allowed List of allowed namespaces/directives
     * @static
     */
    function getAllowedDirectivesForForm($allowed) {
        $schema = HTMLPurifier_ConfigSchema::instance();
        if ($allowed !== true) {
             if (is_string($allowed)) $allowed = array($allowed);
             $allowed_ns = array();
             $allowed_directives = array();
             $blacklisted_directives = array();
             foreach ($allowed as $ns_or_directive) {
                 if (strpos($ns_or_directive, '.') !== false) {
                     // directive
                     if ($ns_or_directive[0] == '-') {
                         $blacklisted_directives[substr($ns_or_directive, 1)] = true;
                     } else {
                         $allowed_directives[$ns_or_directive] = true;
                     }
                 } else {
                     // namespace
                     $allowed_ns[$ns_or_directive] = true;
                 }
             }
        }
        $ret = array();
        foreach ($schema->info as $ns => $keypairs) {
            foreach ($keypairs as $directive => $def) {
                if ($allowed !== true) {
                    if (isset($blacklisted_directives["$ns.$directive"])) continue;
                    if (!isset($allowed_directives["$ns.$directive"]) && !isset($allowed_ns[$ns])) continue;
                }
                if ($def->class == 'alias') continue;
                if ($directive == 'DefinitionID' || $directive == 'DefinitionRev') continue;
                $ret[] = array($ns, $directive);
            }
        }
        return $ret;
    }
    
    /**
     * Loads configuration values from $_GET/$_POST that were posted
     * via ConfigForm
     * @param $array $_GET or $_POST array to import
     * @param $index Index/name that the config variables are in
     * @param $allowed List of allowed namespaces/directives 
     * @param $mq_fix Boolean whether or not to enable magic quotes fix
     * @static
     */
    function loadArrayFromForm($array, $index, $allowed = true, $mq_fix = true) {
        $ret = HTMLPurifier_Config::prepareArrayFromForm($array, $index, $allowed, $mq_fix);
        $config = HTMLPurifier_Config::create($ret);
        return $config;
    }
    
    /**
     * Merges in configuration values from $_GET/$_POST to object. NOT STATIC.
     * @note Same parameters as loadArrayFromForm
     */
    function mergeArrayFromForm($array, $index, $allowed = true, $mq_fix = true) {
         $ret = HTMLPurifier_Config::prepareArrayFromForm($array, $index, $allowed, $mq_fix);
         $this->loadArray($ret);
    }
    
    /**
     * Prepares an array from a form into something usable for the more
     * strict parts of HTMLPurifier_Config
     * @static
     */
    function prepareArrayFromForm($array, $index, $allowed = true, $mq_fix = true) {
        $array = (isset($array[$index]) && is_array($array[$index])) ? $array[$index] : array();
        $mq = get_magic_quotes_gpc() && $mq_fix;
        
        $allowed = HTMLPurifier_Config::getAllowedDirectivesForForm($allowed);
        $ret = array();
        foreach ($allowed as $key) {
            list($ns, $directive) = $key;
            $skey = "$ns.$directive";
            if (!empty($array["Null_$skey"])) {
                $ret[$ns][$directive] = null;
                continue;
            }
            if (!isset($array[$skey])) continue;
            $value = $mq ? stripslashes($array[$skey]) : $array[$skey];
            $ret[$ns][$directive] = $value;
        }
        return $ret;
    }
    
    /**
     * Loads configuration values from an ini file
     * @param $filename Name of ini file
     */
    function loadIni($filename) {
        if ($this->isFinalized('Cannot load directives after finalization')) return;
        $array = parse_ini_file($filename, true);
        $this->loadArray($array);
    }
    
    /**
     * Checks whether or not the configuration object is finalized.
     * @param $error String error message, or false for no error
     */
    function isFinalized($error = false) {
        if ($this->finalized && $error) {
            trigger_error($error, E_USER_ERROR);
        }
        return $this->finalized;
    }
    
    /**
     * Finalizes configuration only if auto finalize is on and not
     * already finalized
     */
    function autoFinalize() {
        if (!$this->finalized && $this->autoFinalize) $this->finalize();
    }
    
    /**
     * Finalizes a configuration object, prohibiting further change
     */
    function finalize() {
        $this->finalized = true;
    }
    
}


