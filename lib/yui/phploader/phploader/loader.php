<?PHP
/**
 *  Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 *  Code licensed under the BSD License:
 *  http://developer.yahoo.net/yui/license.html
 *  version: 1.0.0b2
 */
 
/**
 * Used to specify JavaScript and CSS module requirements.  It maintains a dependency
 * tree for these modules so when a module is requested, all of the other modules it 
 * depends on are included as well.  By default, the YUI Library is configured, and 
 * other modules and their dependencies can be added via PHP.
 * @module phploader
 */

define('YUI_AFTER',      'after');
define('YUI_BASE',       'base');
define('YUI_CSS',        'css');
define('YUI_DATA',       'DATA');
define('YUI_DEPCACHE',   'depCache');
define('YUI_DEBUG',      'DEBUG');
define('YUI_EMBED',      'EMBED');
define('YUI_FILTERS',    'filters');
define('YUI_FULLPATH',   'fullpath');
define('YUI_FULLJSON',   'FULLJSON');
define('YUI_GLOBAL',     'global');
define('YUI_JS',         'js');
define('YUI_JSON',       'JSON');
define('YUI_MODULES',    'modules');
define('YUI_SUBMODULES', 'submodules');
define('YUI_EXPOUND',    'expound');
define('YUI_NAME',       'name');
define('YUI_OPTIONAL',   'optional');
define('YUI_OVERRIDES',  'overrides');
define('YUI_PATH',       'path');
define('YUI_PKG',        'pkg');
define('YUI_PREFIX',     'prefix');
define('YUI_PROVIDES',   'provides');
define('YUI_RAW',        'RAW');
define('YUI_REPLACE',    'replace');
define('YUI_REQUIRES',   'requires');
define('YUI_ROLLUP',     'rollup');
define('YUI_SATISFIES',  'satisfies');
define('YUI_SEARCH',     'search');
define('YUI_SKIN',       'skin');
define('YUI_SKINNABLE',  'skinnable');
define('YUI_SUPERSEDES', 'supersedes');
define('YUI_TAGS',       'TAGS');
define('YUI_TYPE',       'type');
define('YUI_URL',        'url');


/**
 * The YUI PHP loader base class which provides dynamic server-side loading for YUI
 * @class YAHOO_util_Loader
 * @namespace PHP
 */
class YAHOO_util_Loader {
    
    /**
    * The base directory
    * @property base
    * @type string
    * @default http://yui.yahooapis.com/[YUI VERSION]/build/
    */
    var $base = "";

    /**
    * A filter to apply to result urls. This filter will modify the default path for 
    * all modules. The default path is the minified version of the files (e.g., event-min.js). 
    * Changing the filter alows for picking up the unminified (raw) or debug sources.
    * The default set of valid filters are:  YUI_DEBUG & YUI_RAW
    * @property filter
    * @type string (e.g.) 
    * @default empty string (minified vesion)
    */
    var $filter = "";
    
    /**
    * An array of filters & filter replacement rules.  Used with $filter.
    * @property filters
    * @type array
    * @default
    */
    var $filters = array();
    
    /**
    * A list of modules to apply the filter to.  If not supplied, all
    * modules will have any defined filters applied.  Tip: Useful for debugging.
    * @property filterList
    * @type array
    * @default null
    */
    var $filterList = null;

    /**
    * Should we allow rollups
    * @property allowRollups
    * @type boolean
    * @default true
    */
    var $allowRollups = true;

    /**
    * Whether or not to load optional dependencies for the requested modules
    * @property loadOptional
    * @type boolean
    * @default false
    */
    var $loadOptional = false;

    /**
    * Force rollup modules to be sorted as moved to the top of
    * the stack when performing an automatic rollup.  This has a very small performance consequence.
    * @property rollupsToTop
    * @type boolean
    * @default false
    */
    var $rollupsToTop = false;

    /**
    * The first time we output a module type we allow automatic rollups, this
    * array keeps track of module types we have processed
    * @property processedModuleTypes
    * @type array
    * @default
    */
    var $processedModuleTypes = array();

    /**
    * All required modules
    * @property requests
    * @type array
    * @default
    */
    var $requests = array();

    /**
    * List of modules that have been been outputted via getLink() / getComboLink()
    * @property loaded
    * @type array
    * @default
    */
    var $loaded = array();

    /**
    * List of all modules superceded by the list of required modules 
    * @property superceded
    * @type array
    * @default
    */
    var $superceded = array();

    /**
    * Keeps track of modules that were requested that are not defined
    * @property undefined
    * @type array
    * @default
    */
    var $undefined = array();
    
    /**
    * Used to determine if additional sorting of dependencies is required
    * @property dirty
    * @type boolean
    * @default true
    */
    var $dirty = true;
    
    /**
    * List of sorted modules
    * @property sorted
    * @type array
    * @default null
    */
    var $sorted = null;
    
    /**
    * List of modules the loader has aleady accounted for
    * @property accountedFor
    * @type array
    * @default
    */
    var $accountedFor = array();

    /**
    * The list of required skins
    * @property skins
    * @type array
    * @default
    */
    var $skins = array();
    
    /**
    * Contains the available module metadata
    * @property modules
    * @type array
    * @default YUI module metadata for the specified release
    */
    var $modules = array();

    /**
    * The APC cache key
    * @property fullCacheKey
    * @type string
    * @default null
    */
    var $fullCacheKey = null;

    /**
    * List of modules that have had their base pathes overridden
    * @property baseOverrides
    * @type array
    * @default
    */
    var $baseOverrides = array();
    
    /**
    * Used to determine if we have an APC cache hit
    * @property cacheFound
    * @type boolean
    * @default false
    */
    var $cacheFound = false;
    
    /**
    * Used to delay caching of module data
    * @property delayCache
    * @type boolean
    * @default false
    */
    var $delayCache = false;
    
    /* If the version is set, a querystring parameter is appended to the
    * end of all generated URLs.  This is a cache busting hack for environments
    * that always use the same path for the current version of the library.
    * @property version
    * @type string
    * @default null
    */
    var $version = null;
    var $versionKey = "_yuiversion";

    /* Holds the calculated skin definition
    * @property skin
    * @type array
    * @default
    */
    var $skin = array();
    
    /* Holds the module rollup metadata
    * @property rollupModules
    * @type array
    * @default
    */
    var $rollupModules = array();
    
    /* Holds global module information.  Used for global dependency support.
    * Note: Does not appear to be in use by recent metadata.  Might be deprecated?
    * @property globalModules
    * @type array
    * @default
    */
    var $globalModules = array();
    
    /* Holds information about what modules satisfy the requirements of others
    * @property satisfactionMap
    * @type array
    * @default
    */
    var $satisfactionMap = array();
    
    /* Holds a cached module dependency list
    * @property depCache
    * @type array
    * @default
    */
    var $depCache = array();
    
    /**
    * Combined into a single request using the combo service to pontentially reduce the number of 
    * http requests required.  This option is not supported when loading custom modules.
    * @property combine
    * @type boolean
    * @default false
    */
    var $combine = false;

    /**
    * The base path to the combo service.  Uses the Yahoo! CDN service by default.
    * You do not have to set this property to use the combine option. YUI PHP Loader ships 
    * with an intrinsic, lightweight combo-handler as well (see combo.php).
    * @property comboBase
    * @type string
    * @default http://yui.yahooapis.com/combo?
    */
    var $comboBase = "http://yui.yahooapis.com/combo?";
    
    /**
    * Holds the current combo url for the loaded CSS resources.  This is 
    * built with addToCombo and retrieved with getComboLink.  Only used when the combine
    * is enabled.
    * @property cssComboLocation
    * @type string
    * @default null
    */
    var $cssComboLocation = null;
    
    /**
    * Holds the current combo url for the loaded JavaScript resources.  This is 
    * built with addToCombo and retrieved with getComboLink.  Only used when the combine
    * is enabled.
    * @property jsComboLocation
    * @type string
    * @default null
    */
    var $jsComboLocation  = null;

    /**
    * The YAHOO_util_Loader class constructor
    * @constructor
    * @param {string} yuiVersion Defines which version of YUI metadata to load
    * @param {string} cacheKey Unique APC cache key.  This is combined with the YUI base
    * so that updates to YUI will force a new cache entry.  However, if your custom config 
    * changes, this key should be changed (otherwise the old values will be used until the cache expires).
    * @param {array} modules A list of custom modules
    * @param {boolean} noYUI Pass true if you do not want the YUI metadata
    */
    function YAHOO_util_Loader($yuiVersion, $cacheKey=null, $modules=null, $noYUI=false) {
        if (!isset($yuiVersion)) {
            die("Error: The first parameter of YAHOO_util_Loader must specify which version of YUI to use!");
        }
        
        /* 
        * Include the metadata config file that corresponds to the requested YUI version
        * Note: we attempt to find a prebuilt config_{version}.php file which contains an associative array,
        * but if not available we'll attempt to find and parse the YUI json dependency file.
        */
        $parentDir = dirname(dirname(__FILE__));
        $phpConfigFile = $parentDir . '/lib/meta/config_' . $yuiVersion . '.php';
        $jsonConfigFile = $parentDir . '/lib/meta/json_' . $yuiVersion . '.txt';
        
        if (file_exists($phpConfigFile) && is_readable($phpConfigFile)) {
            require($phpConfigFile);
        } else if (file_exists($jsonConfigFile) && is_readable($jsonConfigFile) && function_exists('json_encode')) {
            $jsonConfigString = file_get_contents($jsonConfigFile);
            $inf = json_decode($jsonConfigString, true);
            $GLOBALS['yui_current'] = $inf;
        } else {
            die("Unable to find a suitable YUI metadata file!");
        }
        
        global $yui_current;

        $this->apcttl = 0;
        $this->curlAvail  = function_exists('curl_exec');
        $this->apcAvail   = function_exists('apc_fetch');
        $this->jsonAvail  = function_exists('json_encode');
        $this->customModulesInUse = empty($modules) ? false : true;
        $this->base = $yui_current[YUI_BASE];
        $this->comboDefaultVersion = $yuiVersion;
        $this->fullCacheKey = null;
        $cache = null;

        if ($cacheKey && $this->apcAvail) {
            $this->fullCacheKey = $this->base . $cacheKey;
            $cache = apc_fetch($this->fullCacheKey);
        } 
        
        if ($cache) {
            $this->cacheFound = true;
            $this->modules = $cache[YUI_MODULES];
            $this->skin = $cache[YUI_SKIN];
            $this->rollupModules = $cache[YUI_ROLLUP];
            $this->globalModules = $cache[YUI_GLOBAL];
            $this->satisfactionMap = $cache[YUI_SATISFIES];
            $this->depCache = $cache[YUI_DEPCACHE];
            $this->filters = $cache[YUI_FILTERS];
        } else {
            // set up the YUI info for the current version of the lib
            if ($noYUI) {
                $this->modules = array();
            } else {
                $this->modules = $yui_current['moduleInfo'];
            }

            if ($modules) {
                $this->modules = array_merge_recursive($this->modules, $modules);
            }

            $this->skin = $yui_current[YUI_SKIN];
            $this->skin['overrides'] = array();
            $this->skin[YUI_PREFIX] = "skin-";
            $this->filters = array(
                    YUI_RAW => array(
                            YUI_SEARCH => "/-min\.js/",
                            YUI_REPLACE => ".js"
                        ),
                    YUI_DEBUG => array(
                            YUI_SEARCH => "/-min\.js/",
                            YUI_REPLACE => "-debug.js"
                        )
               );

            foreach ($this->modules as $name=>$m) {

                if (isset($m[YUI_GLOBAL])) {
                    $this->globalModules[$name] = true;
                }

                if (isset($m[YUI_SUPERSEDES])) {
                    $this->rollupModules[$name] = $m;
                    foreach ($m[YUI_SUPERSEDES] as $sup) {
                        $this->mapSatisfyingModule($sup, $name);
                    }
                }
            }
        }
    }
    
    /**
    * Used to update the APC cache
    * @method updateCache
    */
    function updateCache() {
        if ($this->fullCacheKey) {
            $cache = array();
            $cache[YUI_MODULES] = $this->modules;
            $cache[YUI_SKIN] = $this->skin;
            $cache[YUI_ROLLUP] = $this->rollupModules;
            $cache[YUI_GLOBAL] = $this->globalModules;
            $cache[YUI_DEPCACHE] = $this->depCache;
            $cache[YUI_SATISFIES] = $this->satisfactionMap;
            $cache[YUI_FILTERS] = $this->filters;
            apc_store($this->fullCacheKey, $cache, $this->apcttl);
        }
    }

    /**
    * Used to load YUI and/or custom components
    * @method load
    * @param string $varname [, string $... ] List of component names
    */
    function load() {
        //Expects N-number of named components to load 
        $args = func_get_args();
        foreach ($args as $arg) {
            $this->loadSingle($arg);
        }
    }
    
    /**
    * Used to mark a module type as processed
    * @method setProcessedModuleType
    * @param string $moduleType
    */
    function setProcessedModuleType($moduleType='ALL') {
        $this->processedModuleTypes[$moduleType] = true;
    }
    
    /**
    * Used to determine if a module type has been processed
    * @method hasProcessedModuleType
    * @param string $moduleType
    */
    function hasProcessedModuleType($moduleType='ALL') {
        return isset($this->processedModuleTypes[$moduleType]);
    }

    /**
    * Used to specify modules that are already on the page that should not be loaded again
    * @method setLoaded
    * @param string $varname [, string $... ] List of module names
    */
    function setLoaded() {
        $args = func_get_args();

        foreach ($args as $arg) {
            if (isset($this->modules[$arg])) {
                $this->loaded[$arg] = $arg;
                $mod = $this->modules[$arg];

                $sups = $this->getSuperceded($arg);
                // accounting for by way of supersede
                foreach ($sups as $supname=>$val) {
                    $this->loaded[$supname] = $supname;
                }

                // prevent rollups for this module type
                $this->setProcessedModuleType($mod[YUI_TYPE]);
            } else {
                $msg = "YUI_LOADER: undefined module name provided to setLoaded(): " . $arg;
                error_log($msg, 0);
            }
        }
    }
    
    /**
    * Sets up skin for skinnable modules
    * @method skinSetup
    * @param string $name module name
    * @return {string}
    */
    function skinSetup($name) {
        $skinName = null;
        $dep = $this->modules[$name];

        if ($dep && isset($dep[YUI_SKINNABLE])) {
            $s = $this->skin;
            
            if (isset($s[YUI_OVERRIDES][$name])) {
                foreach ($s[YUI_OVERRIDES][$name] as $name2 => $over2) {
                    $skinName = $this->formatSkin($over2, $name);
                }
            } else {
                $skinName = $this->formatSkin($s["defaultSkin"], $name);
            }

            // adding new skin module
            $this->skins[] = $skinName;
            $skin = $this->parseSkin($skinName);

            // module-specific
            if (isset($skin[2])) {
                $dep = $this->modules[$skin[2]];
                $package = (isset($dep[YUI_PKG])) ? $dep[YUI_PKG] : $skin[2];
                $path = $package . '/' . $s[YUI_BASE] . $skin[1] . '/' . $skin[2] . '.css';
                $this->modules[$skinName] = array(
                        "name" => $skinName,
                        "type" => YUI_CSS,
                        "path" => $path,
                        "after" => $s[YUI_AFTER]
                    );

            // rollup skin
            } else {
                $path = $s[YUI_BASE] . $skin[1] . '/' . $s[YUI_PATH];
                $newmod = array(
                        "name" => $skinName,
                        "type" => YUI_CSS,
                        "path" => $path,
                        "rollup" => 3,
                        "after" => $s[YUI_AFTER]
                    );
                $this->modules[$skinName] = $newmod;
                $this->rollupModules[$skinName] = $newmod;
            }

        }    

        return $skinName;
    }
    
    /**
    * Parses a module's skin.  A modules skin is typically prefixed.
    * @method parseSkin
    * @param string $name the name of a module to parse
    * @return {array}
    */
    function parseSkin($moduleName) {
        if (strpos( $moduleName, $this->skin[YUI_PREFIX] ) === 0) {
            return explode('-', $moduleName);
        }

        return null;
    }
    
    /**
    * Add prefix to module skin
    * @method formatSkin
    * @param string $skin the skin name
    * @param string $moduleName the name of a module
    * @return {string} prefixed skin name
    */
    function formatSkin($skin, $moduleName) {
        $prefix = $this->skin[YUI_PREFIX];
        $s = $prefix . $skin;
        if ($moduleName) {
            $s = $s . '-' . $moduleName;
        }

        return $s;
    }
    
    /**
    * Loads the requested module
    * @method loadSingle
    * @param string $name the name of a module to load
    * @return {boolean}
    */
    function loadSingle($name) {
        $skin = $this->parseSkin($name);

        if ($skin) {
            $this->skins[] = $name;
            $this->dirty = true;
            return true;
        }

        if (!isset($this->modules[$name])) {
            $this -> undefined[$name] = $name;
            return false;
        }

        if (isset($this->loaded[$name]) || isset($this->accountedFor[$name])) {
            // skip
        } else {
            $this->requests[$name] = $name;
            $this->dirty = true;
        }
        
        return true;
    }

    /**
    * Used to output each of the required script tags
    * @method script
    * @return {string}
    */
    function script() {
        return $this->tags(YUI_JS);
    }
    
    /**
    * Used to output each of the required link tags
    * @method css
    * @return {string}
    */
    function css() {
        return $this->tags(YUI_CSS);
    }

    /**
    * Used to output each of the required html tags (i.e.) script or link
    * @method tags
    * @param {string} moduleType Type of html tag to return (i.e.) js or css.  Default is both.
    * @param {boolean} skipSort
    * @return {string}
    */
    function tags($moduleType=null, $skipSort=false) {
        return $this->processDependencies(YUI_TAGS, $moduleType, $skipSort);
    }
    
    /**
    * Used to embed the raw JavaScript inline
    * @method script_embed
    * @return {string} Returns the script tag(s) with the JavaScript inline
    */
    function script_embed() {
        return $this->embed(YUI_JS);
    }
    
    /**
    * Used to embed the raw CSS 
    * @method css_embed
    * @return {string} (e.g.) Returns the style tag(s) with the CSS inline
    */
    function css_embed() {
        return $this->embed(YUI_CSS);
    }

    /**
    * Used to output each of the required html tags inline (i.e.) script and/or style
    * @method embed
    * @param {string} moduleType Type of html tag to return (i.e.) js or css.  Default is both.
    * @param {boolean} skipSort
    * @return {string} Returns the style tag(s) with the CSS inline and/or the script tag(s) with the JavaScript inline
    */
    function embed($moduleType=null, $skipSort=false) {
        return $this->processDependencies(YUI_EMBED, $moduleType, $skipSort);
    }

    /**
    * Used to fetch an array of the required JavaScript components 
    * @method script_data
    * @return {array} Returns an array of data about each of the identified JavaScript components
    */
    function script_data() {
        return $this->data(YUI_JS);
    }

    /**
    * Used to fetch an array of the required CSS components
    * @method css_data
    * @return {array} Returns an array of data about each of the identified JavaScript components
    */
    function css_data() {
        return $this->data(YUI_CSS);
    }
    
    /**
    * Used to output an Array which contains data about the required JavaScript & CSS components
    * @method data
    * @param {string} moduleType Type of html tag to return (i.e.) js or css.  Default is both.
    * @param {boolean} allowRollups
    * @param {boolean} skipSort
    * @return {string}
    */
    function data($moduleType=null, $allowRollups=false, $skipSort=false) {
        if (!$allowRollups) {
            $this->setProcessedModuleType($moduleType);
        }

        $type = YUI_DATA;

        return $this->processDependencies($type, $moduleType, $skipSort);
    }

    /**
    * Used to fetch a JSON object with the required JavaScript components 
    * @method script_json
    * @return {string} Returns a JSON object containing urls for each JavaScript component
    */
    function script_json() {
        return $this->json(YUI_JS);
    }
    
    /**
    * Used to fetch a JSON object with the required CSS components
    * @method css_json
    * @return {string} Returns a JSON object containing urls for each CSS component
    */
    function css_json() {
        return $this->json(YUI_CSS);
    }

    /**
    *  Used to fetch a JSON object with the required JavaScript and CSS components
    * @method json
    * @param {string} moduleType
    * @param {boolean} allowRollups
    * @param {boolean} skipSort
    * @param {boolean} full
    * @return {string} Returns a JSON object with the required JavaScript and CSS components
    */
    function json($moduleType=null, $allowRollups=false, $skipSort=false, $full=false) {
        if (!$allowRollups) {
            $this->setProcessedModuleType($moduleType);
        }

        // the original JSON output only sent the provides data, not the requires
        $type = YUI_JSON;

        if ($full) {
            $type = YUI_FULLJSON;
        }

        return $this->processDependencies($type, $moduleType, $skipSort);
    }
 
    /**
    * Used to produce the raw JavaScript code inline without the actual script tags
    * @method script_raw
    * @return {string} Returns the raw JavaScript code inline without the actual script tags
    */
    function script_raw() {
        return $this->raw(YUI_JS);
    }

    /**
    * Used to produce the raw CSS code inline without the actual style tags
    * @method css_raw
    * @return {string} Returns the raw CSS code inline without the actual style tags
    */
    function css_raw() {
        return $this->raw(YUI_CSS);
    }

    /**
    * Used to produce the raw Javacript and CSS code inline without the actual script or style tags
    * @method raw
    * @param {string} moduleType
    * @param {boolean} allowRollups
    * @param {boolean} skipSort
    * @return {string} Returns the raw JavaScript and/or CSS code inline without the actual style tags
    */
    function raw($moduleType=null, $allowRollups=false, $skipSort=false) {
        return $this->processDependencies(YUI_RAW, $moduleType, $skipSort);
    }
    
    /**
    * General logging function.  Writes a message to the PHP error log.
    * @method log
    * @param {string} msg Message to write
    */
    function log($msg) {
        error_log($msg, 0);
    }
    
    /**
    * Markes a module as being accounted for.  Used in dependency testing.
    * @method accountFor
    * @param {string} name Module to mark as being accounted for
    */
    function accountFor($name) {
        $this->accountedFor[$name] = $name;
        
        if (isset($this->modules[$name])) {
            $dep = $this->modules[$name];
            $sups = $this->getSuperceded($name);
            foreach ($sups as $supname=>$val) {
                // accounting for by way of supersede package
                $this->accountedFor[$supname] = true;
            }
        }
    }

    /**
    * Used during dependecy processing to prune modules from the list of modules requiring further processing
    * @method prune
    * @param {array} deps List of module dependencies
    * @param {string} moduleType Type of modules to prune (i.e.) js or css
    * @return {array}
    */
    function prune($deps, $moduleType) {
        if ($moduleType) {
            $newdeps = array();
            foreach ($deps as $name=>$val) {
                $dep = $this->modules[$name];
                if ($dep[YUI_TYPE] == $moduleType) {
                    $newdeps[$name] = true;
                }
            }
            return $newdeps;
        } else {
            return $deps;
        }
   }
   
   /**
   * Use to get a list of modules superseded by the given module name
   * @method getSuperceded
   * @param {string} name Module name
   * @return {array}
   */
   function getSuperceded($name) {
        $key = YUI_SUPERSEDES . $name;

        if (isset($this->depCache[$key])) {
            return $this->depCache[$key];
        }

        $sups = array();

        if (isset($this->modules[$name])) {
            $m = $this->modules[$name];
            if (isset($m[YUI_SUPERSEDES])) {
                foreach ($m[YUI_SUPERSEDES] as $supName) {
                    $sups[$supName] = true;
                    if (isset($this->modules[$supName])) {
                        $supsups = $this->getSuperceded($supName);
                        if (count($supsups) > 0) {
                            $sups = array_merge($sups, $supsups);
                        }
                    } 
                }
            }
        }

        $this->depCache[$key] = $sups;
        return $sups;
    }
    
    /**
    * Identify dependencies for a give module name
    * @method getAllDependencies
    * @param {string} mname Module name
    * @param {boolean} loadOptional Load optional dependencies
    * @param {array} completed
    * @return {array}
    */
    function getAllDependencies($mname, $loadOptional=false, $completed=array()) {
        $key = YUI_REQUIRES . $mname;
        if ($loadOptional) {
            $key .= YUI_OPTIONAL;
        }
        
        if (isset($this->depCache[$key])) {
            return $this->depCache[$key];
        }
        
        $m = $this->modules[$mname];
        $mProvides = $this->getProvides($mname);
        $reqs = array();
    
	    //Some modules pretend to be others (override if this is the case)
        if (isset($this->modules[$mname][YUI_EXPOUND])) {
			if (!isset($completed[$mname])) {
				$reqs = array_merge($completed, $this->getAllDependencies($this->modules[$mname][YUI_EXPOUND], $loadOptional, array($mname => true)));
			}
        }

        //Add any requirements defined on the module itself
        if (isset($m[YUI_REQUIRES])) {
            $origreqs = $m[YUI_REQUIRES];
            foreach($origreqs as $r) {
            	if (!isset($reqs[$r])) {
            		$reqs[$r] = true;
                	$reqs = array_merge($reqs, $this->getAllDependencies($r, $loadOptional, $reqs));
            	}
            }
        }
         
        //Add any submodule requirements not provided by the rollups
        if (isset($m[YUI_SUBMODULES])) {
            foreach($m[YUI_SUBMODULES] as $submodule) {
                $subreqs = $submodule[YUI_REQUIRES];
                foreach($subreqs as $sr) {     
                    if (!in_array($sr, $mProvides) && !in_array($sr, $this->accountedFor)) {
		            	if (!isset($reqs[$sr])) {
	                    	$reqs[$sr] = true; 
	                        $reqs = array_merge($reqs, $this->getAllDependencies($sr, $loadOptional, $reqs));
		            	}
                    }
                }
            }
        }
        
        //Add any superseded requirements not provided by the rollup and/or rollup submodules
        if (isset($m[YUI_SUPERSEDES])) {
            foreach($m[YUI_SUPERSEDES] as $supersededModule) {
                if (isset($this->modules[$supersededModule][YUI_REQUIRES])) {
                    foreach($this->modules[$supersededModule][YUI_REQUIRES] as $supersededModuleReq) {
                        if (!in_array($supersededModuleReq, $mProvides)) {
			            	if (!isset($reqs[$supersededModuleReq])) {
	                            $reqs[$supersededModuleReq] = true;
	                            $reqs = array_merge($reqs, $this->getAllDependencies($supersededModuleReq, $loadOptional, $reqs));
			            	}
                        }
                    }
                }
                
                //Add any submodule requirements not provided by the rollup or originally requested module
                if (isset($this->modules[$supersededModule][YUI_SUBMODULES])) {
                    foreach($this->modules[$supersededModule][YUI_SUBMODULES] as $supersededSubmodule) {
                        $ssmProvides = $this->getProvides($supersededModule);
                        $supersededSubreqs = $supersededSubmodule[YUI_REQUIRES];
                        foreach($supersededSubreqs as $ssr) {     
                            if (!in_array($ssr, $ssmProvides)) {
				            	if (!isset($reqs[$ssr])) {
	                                $reqs[$ssr] = true;
	                                $reqs = array_merge($reqs, $this->getAllDependencies($ssr, $loadOptional, $reqs));
				            	}
                            }
                        }
                    }
                }
            }
        }

        if ($loadOptional && isset($m[YUI_OPTIONAL])) {
            $o = $m[YUI_OPTIONAL];
            foreach($o as $opt) {
                $reqs[$opt] = true;
            }
        }

        $this->depCache[$key] = $reqs;
        
        return $reqs;
    }

    // @todo restore global dependency support
    function getGlobalDependencies() {
        return $this->globalModules;
    }

    /**
     * Returns true if the supplied $satisfied module is satisfied by the
     * supplied $satisfier module
     */
    function moduleSatisfies($satisfied, $satisfier) {
        if($satisfied == $satisfier) {
            return true;
        }

        if (isset($this->satisfactionMap[$satisfied])) {
            $satisfiers = $this->satisfactionMap[$satisfied];
            return isset($satisfiers[$satisfier]);
        }

        return false;
    }

    /**
    * Used to override the base dir for specific set of modules (Note: not supported when using the combo service)
    * @method overrideBase
    * @param {string} base Base path (e.g.) 2.6.0/build
    * @param {array} modules Module names of which to override base
    */
    function overrideBase($base, $modules) {
        foreach ($modules as $name) {
            $this->baseOverrides[$name] = $base;
        }
    }
    
    /**
    * Used to determine if one module is satisfied by provided array of modules
    * @method listSatisfies
    * @param {string} satisfied Module name
    * @param {array} moduleList List of modules names
    * @return {boolean}
    */
    function listSatisfies($satisfied, $moduleList) {
        if (isset($moduleList[$satisfied])) {
            return true;
        } else {
            if (isset($this->satisfactionMap[$satisfied])) {
                $satisfiers = $this->satisfactionMap[$satisfied];
                foreach ($satisfiers as $name=>$val) {
                    if (isset($moduleList[$name])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
    
    /**
    * Determine if rollup replacement threshold has been met
    * @method checkThreshold
    * @param {string} Module name
    * @param {array} moduleList List of modules names
    * @return {boolean}
    */
    function checkThreshold($module, $moduleList) {
        if (count($moduleList) > 0 && isset($module[YUI_ROLLUP])) {
            $matched = 0;
            $thresh = $module[YUI_ROLLUP];
            foreach ($moduleList as $moduleName=>$moddef) {
                if (in_array($moduleName, $module[YUI_SUPERSEDES])) {
                    $matched++;
                }
            }
            
            return ($matched >= $thresh);
        }

        return false;
    }
    
    /**
    * Used to sort dependencies in the proper order (Note: only call this if the loader is dirty)
    * @method sortDependencies
    * @param {string} Module name
    * @param {array} moduleList List of modules names
    * @return {boolean}
    */
    function sortDependencies($moduleType, $skipSort=false) {
        $reqs = array();
        $top = array();
        $bot = array();
        $notdone = array();
        $sorted = array();
        $found = array();

        // add global dependenices so they are included when calculating rollups
        $globals = $this->getGlobalDependencies($moduleType);
        
        foreach ($globals as $name=>$dep) {
            $reqs[$name] = true;
        }
        
        // get and store the full list of dependencies.
        foreach ($this->requests as $name=>$val) {
            $reqs[$name] = true;
            $dep = $this->modules[$name];
            $newreqs = $this->getAllDependencies($name, $this->loadOptional);
            
            foreach ($newreqs as $newname=>$newval) {
                if (!isset($reqs[$newname])) {
                    $reqs[$newname] = true;
                }
            }
        }

        // if we skip the sort, we just return the list that includes everything that
        // was requested, all of their requirements, and global modules.  This is
        // filtered by module type if supplied
        if ($skipSort) {
            return $this->prune($reqs, $moduleType);
        }

        // if we are sorting again after new modules have been requested, we
        // do not rollup, and we can remove the accounted for modules
        if (count($this->accountedFor) > 0 || count($this->loaded) > 0) {
            foreach ($this->accountedFor as $name=>$val) {
                if (isset($reqs[$name])) {
                    unset($reqs[$name]);
                }
            }
            
            // removing satisfied req (loaded)
            foreach ($this->loaded as $name=>$val) {
                if (isset($reqs[$name])) {
                    unset($reqs[$name]);
                }
            }
        } else if ($this->allowRollups) {
            // First we go through the meta-modules we know about to 
            // see if the replacement threshold has been met.
            $rollups = $this->rollupModules;

            if (count($rollups > 0)) {
                foreach ($rollups as $name => $rollup) {
                    if (!isset($reqs[$name]) && $this->checkThreshold($rollup, $reqs) ) {
                        $reqs[$name] = true;
                        $dep = $this->modules[$name];
                        $newreqs = $this->getAllDependencies($name, $this->loadOptional, $reqs);
                        foreach ($newreqs as $newname=>$newval) {
                            if (!isset($reqs[$newname])) {
                                $reqs[$newname] = true;
                            }
                        }
                    }
                }
            }
        }

        // clear out superceded packages
        foreach ($reqs as $name => $val) {
            $dep = $this->modules[$name];

            if (isset($dep[YUI_SUPERSEDES])) {
                $override = $dep[YUI_SUPERSEDES];
                
                foreach ($override as $i=>$val) {
                    if (isset($reqs[$val])) {
                        unset($reqs[$val]);
                    }

                    if (isset($reqs[$i])) {
                        unset($reqs[$i]);
                    }
                }
            }
        }

        // move globals to the top
        foreach ($reqs as $name => $val) {
            $dep = $this->modules[$name];
            if (isset($dep[YUI_GLOBAL]) && $dep[YUI_GLOBAL]) {
                $top[$name] = $name;
            } else {
                $notdone[$name] = $name;
            }
        }

        // merge new order if we have globals   
        if (count($top > 0)) {
            $notdone = array_merge($top, $notdone);
        }

        // keep track of what is accounted for
        foreach ($this->loaded as $name=>$module) {
            $this->accountFor($name);
        }

        // keep going until everything is sorted
        $count = 0;
        while (count($notdone) > 0) {
            if ($count++ > 200) {
                $msg = "YUI_LOADER ERROR: sorting could not be completed, there may be a circular dependency";
                error_log($msg, 0);
                return array_merge($sorted, $notdone);
            }
            
            // each pass only processed what has not been completed
            foreach ($notdone as $name => $val) {
                $dep = $this->modules[$name];                
                $newreqs = $this->getAllDependencies($name, $this->loadOptional);
                $this->accountFor($name);    
                
                //Detect if this module needs to be included after another one of the upcoming dependencies
                if (isset($dep[YUI_AFTER])) {
                    $after = $dep[YUI_AFTER];
                    
                    foreach($after as $a) {
                        if (in_array($a, $notdone)) {
                            $newreqs[$a] = true;
                        }
                    }
                }

                if (!empty($newreqs)) {
                    foreach ($newreqs as $depname=>$depval) {
                        // check if the item is accounted for in the $done list
                        if (isset($this->accountedFor[$depname]) || $this->listSatisfies($depname, $sorted)) {
                        	//unset($notdone[$depname]);
                        } else {
                            $tmp = array();
                            $found = false;
                            foreach ($notdone as $newname => $newval) {
                                if ($this->moduleSatisfies($depname, $newname)) {
                                    $tmp[$newname] = $newname;
                                    unset($notdone[$newname]);
                                    $found = true;
                                    break; // found something that takes care of the dependency, so jump out
                                }
                            }
                            
                            if ($found) {
                                // this should put the module that handles the dependency on top, immediately
                                // over the the item with the missing dependency
                                $notdone = array_merge($tmp, $notdone);
                            } else {
                                //Requirement was missing and not found within the current notdone list.  Add and try again.
                                $notdone[$depname] = $depname;
                            }
                            
                            break(2); // break out of this iteration so we can get the missed dependency
                        }
                    }
                }
            
                $sorted[$name] = $name;
                unset($notdone[$name]);
            }
        }
        
        //Deal with module skins
        foreach ($sorted as $name => $val) {
            $skinName = $this->skinSetup($name);
        }

        if ( count($this->skins) > 0 ) {
            foreach ($this->skins as $name => $val) {
                $sorted[$val] = true;
            }
        }

        $this->dirty = false;
        $this->sorted = $sorted;

        // store the results, set clear the diry flag
        return $this->prune($sorted, $moduleType);
    }
 
    function mapSatisfyingModule($satisfied, $satisfier) {
        if (!isset($this->satisfactionMap[$satisfied])) {
            $this->satisfactionMap[$satisfied] = array();
        }

        $this->satisfactionMap[$satisfied][$satisfier] = true;
    }
    
    /**
    * Used to process the dependency list and retrieve the actual CSS and/or JavaScript resources
    * in requested output format (e.g.) json, link/script nodes, embeddable code, php array, etc.
    * @method processDependencies
    * @param {string} outputType the format you like the response to be in
    * @param {string} moduleType Type of module to return (i.e.) js or css
    * @param {boolean} skipSort
    * @param {boolean} showLoaded
    * @return {varies} output format based on requested outputType
    */
    function processDependencies($outputType, $moduleType, $skipSort=false, $showLoaded=false) {
        $html = '';

        // sort the output with css on top unless the output type is json
        if ((!$moduleType) && (strpos($outputType, YUI_JSON) === false) && $outputType != YUI_DATA) {
            $this->delayCache = true;
            $css = $this->processDependencies($outputType, YUI_CSS, $skipSort, $showLoaded);
            $js  = $this->processDependencies($outputType, YUI_JS, $skipSort, $showLoaded);

            // If the data has not been cached, cache what we have
            if (!$this->cacheFound) {
                $this->updateCache();
            }

            return $css . $js;
        }
        
        $json = array();

        if ($showLoaded || (!$this->dirty && count($this->sorted) > 0)) {
            $sorted = $this->prune($this->sorted, $moduleType);
        } else {
            $sorted = $this->sortDependencies($moduleType, $skipSort);
        }

        foreach ($sorted as $name => $val) {
            if ($showLoaded || !isset($this->loaded[$name])) {
                $dep = $this->modules[$name];
                // only generate the tag once
                switch ($outputType) {
                    case YUI_EMBED:
                        $html .= $this->getContent($name, $dep[YUI_TYPE])."\n";
                        break;
                    case YUI_RAW:
                        $html .= $this->getRaw($name)."\n";
                        break;
                    case YUI_JSON:
                    case YUI_DATA:
                        //$json[$dep[YUI_TYPE]][$this->getUrl($name)] = $this->getProvides($name);
                        $json[$dep[YUI_TYPE]][] = array(
                                $this->getUrl($name) => $this->getProvides($name)
                            );
                        break;
                    case YUI_FULLJSON:
                        $json[$dep[YUI_NAME]] = array();
                        $item = $json[$dep[YUI_NAME]];
                        $item[YUI_TYPE] = $dep[YUI_TYPE];
                        $item[YUI_URL] = $this->getUrl($name);
                        $item[YUI_PROVIDES] = $this->getProvides($name);
                        $item[YUI_REQUIRES] = $dep[YUI_REQUIRES];
                        $item[YUI_OPTIONAL] = $dep[YUI_OPTIONAL];
                        break;
                    case YUI_TAGS:
                    default:
                        if ($this->combine === true && $this->customModulesInUse === false) {
                            $this->addToCombo($name, $dep[YUI_TYPE]);
                            $html = $this->getComboLink($dep[YUI_TYPE]);
                        } else {
                           $html .= $this->getLink($name, $dep[YUI_TYPE])."\n";
                        }
                }
            }
        }

        // If the data has not been cached, and we are not running two
        // rotations for separating css and js, cache what we have
        if (!$this->cacheFound && !$this->delayCache) {
            $this->updateCache();
        }

        if (!empty($json)) {
            if ($this->canJSON()) {
                $html .= json_encode($json);
            } else {
                $html .= "<!-- JSON not available, request failed -->";
            }
        }

        // after the first pass we no longer try to use meta modules
        $this->setProcessedModuleType($moduleType);

        // keep track of all the stuff we loaded so that we don't reload 
        // scripts if the page makes multiple calls to tags
        $this->loaded = array_merge($this->loaded, $sorted);
        if ($this->combine === true) {
            $this->clearComboLink($outputType);
        }

        // return the raw data structure
        if ($outputType == YUI_DATA) {
            return $json;
        }

        if ( count($this->undefined) > 0 ) {
            $html .= "<!-- The following modules were requested but are not defined: " . join($this -> undefined, ",") . " -->\n";
        }

        return $html;
    }

    /**
    * Retrieve the calculated url for the component in question
    * @method getUrl
    * @param {string} name YUI component name
    */
    function getUrl($name) {
        // figure out how to set targets and filters
        $url = "";
        $b = $this->base;
        if (isset($this->baseOverrides[$name])) {
            $b = $this->baseOverrides[$name];
        }

        if (isset($this->modules[$name])) {
            $m = $this->modules[$name];
            if (isset($m[YUI_FULLPATH])) {
                $url = $m[YUI_FULLPATH];
            } else {
                $url = $b . $m[YUI_PATH];
            }
        } else {
            $url = $b . $name;
        }

        if ($this->filter) {
            if (count($this->filterList) > 0 && !isset($this->filterList[$name])) {
                // skip the filter
            } else if (isset($this->filters[$this->filter])) {
                $filter = $this->filters[$this->filter];
                $url = preg_replace($filter[YUI_SEARCH], $filter[YUI_REPLACE], $url);
            }
        }

        if ($this->version) {
            $pre = (strstr($url, '?')) ? '&' : '?';
            $url .= $pre . $this->versionKey . '=' . $this->version;
        }
        
        return $url;
    }

    /**
    * Retrieve the contents of a remote resource
    * @method getRemoteContent
    * @param {string} url URL to fetch data from
    * @return raw source
    */
    function getRemoteContent($url) {
        $remote_content = null;
        if ($this->apcAvail === true) {
            $remote_content = apc_fetch($url);
        }        

        if (!$remote_content) {
            if($this->curlAvail === true) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FAILONERROR, 1); 

                //Doesn't work in safe mode or with openbase_dir enabled, see http://au.php.net/manual/ro/function.curl-setopt.php#71313.
                $open_basedir = ini_get("open_basedir");
                if (empty($open_basedir) && !ini_get('safe_mode')) {
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
                }

                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
                // curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s

                $remote_content = curl_exec($ch);

                // save the contents of the remote url for 30 minutes
                if ($this->apcAvail === true) {
                    apc_store($url, $remote_content, $this->apcttl);
                }

                curl_close ($ch);
            } else {
                $remote_content = "<!--// cURL was not detected, so the content cannot be fetched -->";
            }   
        }

        return $remote_content;
    }
    
    /**
    * Retrieve the raw source contents for a given module name
    * @method getRaw
    * @param {string} name The module name you wish to fetch the source from
    * @return {string} raw source
    */
    function getRaw($name) {        
        if(!$this->curlAvail) {
            return "<!--// cURL was not detected, so the content cannot be fetched -->";
        }

        $url = $this->getUrl($name);
        return $this->getRemoteContent($url);
    }

    /**
    * Retrieve the style or script node with embedded source for a given module name and resource type
    * @method getContent
    * @param {string} name The module name to fetch the source from
    * @param {string} type Resource type (i.e.) YUI_JS or YUI_CSS
    * @return {string} style or script node with embedded source
    */
    function getContent($name, $type) {
        if(!$this->curlAvail) {
            return "<!--// cURL was not detected, so the content cannot be fetched/embedded -->" . $this->getLink($name, $type);
        }

        $url = $this->getUrl($name);

        if (!$url) {
            return '<!-- PATH FOR "'. $name . '" NOT SPECIFIED -->';
        } else if ($type == YUI_CSS) {
            return '<style type="text/css">' . $this->getRemoteContent($url) . '</style>';
        } else {
            return '<script type="text/javascript">' . $this->getRemoteContent($url) . '</script>'; 
        }
    }
    
    /**
    * Retrieve the link or script include for a given module name and resource type
    * @method getLink
    * @param {string} name The module name to fetch the include for
    * @param {string} type Resource type (i.e.) YUI_JS or YUI_CSS
    * @return {string} link or script include
    */
    function getLink($name, $type) {
        $url = $this->getUrl($name);

        if (!$url) {
            return '<!-- PATH FOR "'. $name . '" NOT SPECIFIED -->';
        } else if ($type == YUI_CSS) {
            return '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
        } else {
            return '<script type="text/javascript" src="' . $url . '"></script>';
        }
    }
  
    /**
    * Retrieves the combo link or script include for the currently loaded modules of a specific resource type
    * @method getComboLink
    * @param {string} type Resource type (i.e.) YUI_JS or YUI_CSS
    * @return {string} link or script include
    */
    function getComboLink($type) {
        $url = '';
        
        if ($type == YUI_CSS) {
            if ($this->cssComboLocation !== null) {
                $url = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$this->cssComboLocation}\" />";
            } else {
                $url = "<!-- NO YUI CSS COMPONENTS IDENTIFIED -->";
            }
        } else if ($type == YUI_JS) {
            if ($this->jsComboLocation !== null) {
                if ($this->cssComboLocation !== null) {
                    $url = "\n";
                }
                $url .= "<script type=\"text/javascript\" src=\"{$this->jsComboLocation}\"></script>";
            } else {
                $url = "<!-- NO YUI JAVASCRIPT COMPONENTS IDENTIFIED -->";
            }
        }
        
        //Allow for RAW & DEBUG over minified default
        if ($this->filter) {
            if (count($this->filterList) > 0 && !isset($this->filterList[$name])) {
                // skip the filter
            } else if (isset($this->filters[$this->filter])) {
                $filter = $this->filters[$this->filter];
                $url = preg_replace($filter[YUI_SEARCH], $filter[YUI_REPLACE], $url);
            }
        }
        
        return $url;
    }
    
    /**
    * Clears the combo url of already loaded modules for a specific resource type.  Prevents
    * duplicate loading of modules if the page makes multiple calls to tags, css, or script.
    * @method clearComboLink
    * @param {string} type Resource type (i.e.) YUI_JS or YUI_CSS
    */
    function clearComboLink($type) {
        if ($type == YUI_CSS) {
            $this->cssComboLocation = null;
        } else if ($type == YUI_JS) {
            $this->jsComboLocation = null;
        } else {
            $this->cssComboLocation = null;
            $this->jsComboLocation  = null;
        }
    }
    
    /**
    * Adds a module the combo collection for a specified resource type
    * @method addToCombo
    * @param {string} name The module name to add
    * @param {string} type Resource type (i.e.) YUI_JS or YUI_CSS
    */
    function addToCombo($name, $type) {
        $pathToModule = $this->comboDefaultVersion . '/build/' . $this->modules[$name][YUI_PATH];
        if ($type == YUI_CSS) {
            //If this is the first css component then add the combo base path
            if ($this->cssComboLocation === null) {
                $this->cssComboLocation = $this->comboBase . $pathToModule;
            } else {
                //Prep for next component
                $this->cssComboLocation .= '&' . $pathToModule;
            }
        } else {
            //If this is the first js component then add the combo base path
            if ($this->jsComboLocation === null) {
                $this->jsComboLocation = $this->comboBase . $pathToModule;
            } else {
                //Prep for next component
                $this->jsComboLocation .= '&' . $pathToModule;
            }
        }
    }
  
    /**
    * Detects if environment supports JSON encode/decode
    * @method canJSON
    * @return boolean
    */
    function canJSON() {
        return $this->jsonAvail;
    }
    
    /**
    * Identifies what module(s) are provided by a given module name (e.g.) yaho-dom-event provides yahoo, dom, and event
    * @method getProvides
    * @param {string} name Module name
    * @return {array}
    */
    function getProvides($name) {
        $p = array($name);
        if (isset($this->modules[$name])) {
            $m = $this->modules[$name];
            if (isset($m[YUI_SUPERSEDES])) {
                foreach ($m[YUI_SUPERSEDES] as $i) {
                    $p[] = $i;
                }
            }
        }

        return $p;
    }
    
    /**
    * Identifies what module(s) have been loaded via the load method and/or marked as loaded via the setLoaded method
    * @method getLoadedModules
    * @return {array}
    */
    function getLoadedModules() {
        $loaded = array();
        foreach ($this->loaded as $i=>$value) {
            if (isset($this->modules[$i])) {
                $dep = $this->modules[$i];
                $loaded[$dep[YUI_TYPE]][] = array(
                        $this->getUrl($i) => $this->getProvides($i)
                    );
            } else {
                $msg = "YUI_LOADER ERROR: encountered undefined module: " . $i;
                error_log($msg, 0);
            }
        }
        return $loaded;
    }

    /**
    * Identifies what module(s) have been loaded via the load method and/or marked as loaded via the setLoaded method
    * @method getLoadedModulesAsJSON
    * @return {json}
    */
    function getLoadedModulesAsJSON() {
        if (!$this->canJSON()) {
            return "{\"Error\", \"json library not available\"}";
        }

        return json_encode($this->getLoadedModules());
    }
}

?>
