<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cache definition class
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are requried in order to use caching.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The cache definition class.
 *
 * Cache definitions need to be defined in db/caches.php files.
 * They can be constructed with the following options.
 *
 * Required settings:
 *     + mode
 *          [int] Sets the mode for the definition. Must be one of cache_store::MODE_*
 *
 * Optional settings:
 *     + simplekeys
 *          [bool] Set to true if your cache will only use simple keys for its items.
 *          Simple keys consist of digits, underscores and the 26 chars of the english language. a-zA-Z0-9_
 *          If true the keys won't be hashed before being passed to the cache store for gets/sets/deletes. It will be
 *          better for performance and possible only becase we know the keys are safe.
 *     + simpledata
 *          [bool] If set to true we know that the data is scalar or array of scalar.
 *     + requireidentifiers
 *          [array] An array of identifiers that must be provided to the cache when it is created.
 *     + requiredataguarantee
 *          [bool] If set to true then only stores that can guarantee data will remain available once set will be used.
 *     + requiremultipleidentifiers
 *          [bool] If set to true then only stores that support multiple identifiers will be used.
 *     + requirelockingread
 *          [bool] If set to true then a lock will be gained before reading from the cache store. It is recommended not to use
 *          this setting unless 100% absolutely positively required. Remember 99.9% of caches will NOT need this setting.
 *          This setting will only be used for application caches presently.
 *     + requirelockingwrite
 *          [bool] If set to true then a lock will be gained before writing to the cache store. As above this is not recommended
 *          unless truly needed. Please think about the order of your code and deal with race conditions there first.
 *          This setting will only be used for application caches presently.
 *     + maxsize
 *          [int] If set this will be used as the maximum number of entries within the cache store for this definition.
 *          Its important to note that cache stores don't actually have to acknowledge this setting or maintain it as a hard limit.
 *     + overrideclass
 *          [string] A class to use as the loader for this cache. This is an advanced setting and will allow the developer of the
 *          definition to take 100% control of the caching solution.
 *          Any class used here must inherit the cache_loader interface and must extend default cache loader for the mode they are
 *          using.
 *     + overrideclassfile
 *          [string] Suplements the above setting indicated the file containing the class to be used. This file is included when
 *          required.
 *     + datasource
 *          [string] A class to use as the data loader for this definition.
 *          Any class used here must inherit the cache_data_loader interface.
 *     + datasourcefile
 *          [string] Suplements the above setting indicated the file containing the class to be used. This file is included when
 *          required.
 *     + persistent
 *          [bool] This setting does two important things. First it tells the cache API to only instantiate the cache structure for
 *          this definition once, further requests will be given the original instance.
 *          Second the cache loader will keep an array of the items set and retrieved to the cache during the request.
 *          This has several advantages including better performance without needing to start passing the cache instance between
 *          function calls, the downside is that the cache instance + the items used stay within memory.
 *          Consider using this setting when you know that there are going to be many calls to the cache for the same information
 *          or when you are converting existing code to the cache and need to access the cache within functions but don't want
 *          to add it as an argument to the function.
 *     + persistentmaxsize
 *          [int] This supplements the above setting by limiting the number of items in the caches persistent array of items.
 *          Tweaking this setting lower will allow you to minimise the memory implications above while hopefully still managing to
 *          offset calls to the cache store.
 *     + ttl
 *          [int] A time to live for the data (in seconds). It is strongly recommended that you don't make use of this and
 *          instead try to create an event driven invalidation system.
 *          Not all cache stores will support this natively and there are undesired performance impacts if the cache store does not.
 *     + mappingsonly
 *          [bool] If set to true only the mapped cache store(s) will be used and the default mode store will not. This is a super
 *          advanced setting and should not be used unless absolutely required. It allows you to avoid the default stores for one
 *          reason or another.
 *     + invalidationevents
 *          [array] An array of events that should cause this cache to invalidate some or all of the items within it.
 *     + sharingoptions
 *          [int] The sharing options that are appropriate for this definition. Should be the sum of the possible options.
 *     + defaultsharing
 *          [int] The default sharing option to use. It's highly recommended that you don't set this unless there is a very
 *          specific reason not to use the system default.
 *
 * For examples take a look at lib/db/caches.php
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_definition {

    /** The cache can be shared with everyone */
    const SHARING_ALL = 1;
    /** The cache can be shared with other sites using the same siteid. */
    const SHARING_SITEID = 2;
    /** The cache can be shared with other sites of the same version. */
    const SHARING_VERSION = 4;
    /** The cache can be shared with other sites using the same key */
    const SHARING_INPUT = 8;

    /**
     * The default sharing options available.
     * All + SiteID + Version + Input.
     */
    const SHARING_DEFAULTOPTIONS = 15;
    /**
     * The default sharing option that gets used if none have been selected.
     * SiteID. It is the most restrictive.
     */
    const SHARING_DEFAULT = 2;

    /**
     * The identifier for the definition
     * @var string
     */
    protected $id;

    /**
     * The mode for the defintion. One of cache_store::MODE_*
     * @var int
     */
    protected $mode;

    /**
     * The component this definition is associated with.
     * @var string
     */
    protected $component;

    /**
     * The area this definition is associated with.
     * @var string
     */
    protected $area;

    /**
     * If set to true we know the keys are simple. a-zA-Z0-9_
     * @var bool
     */
    protected $simplekeys = false;

    /**
     * Set to true if we know the data is scalar or array of scalar.
     * @var bool
     */
    protected $simpledata = false;

    /**
     * An array of identifiers that must be provided when the definition is used to create a cache.
     * @var array
     */
    protected $requireidentifiers = array();

    /**
     * If set to true then only stores that guarantee data may be used with this definition.
     * @var bool
     */
    protected $requiredataguarantee = false;

    /**
     * If set to true then only stores that support multple identifiers may be used with this definition.
     * @var bool
     */
    protected $requiremultipleidentifiers = false;

    /**
     * If set to true then we know that this definition requires the locking functionality.
     * This gets set during construction based upon the settings requirelockingread and requirelockingwrite.
     * @var bool
     */
    protected $requirelocking = false;

    /**
     * Set to true if this definition requires read locking.
     * @var bool
     */
    protected $requirelockingread = false;

    /**
     * Gets set to true if this definition requires write locking.
     * @var bool
     */
    protected $requirelockingwrite = false;

    /**
     * Gets set to true if this definition requires searchable stores.
     * @since 2.4.4
     * @var bool
     */
    protected $requiresearchable = false;

    /**
     * Sets the maximum number of items that can exist in the cache.
     * Please note this isn't a hard limit, and doesn't need to be enforced by the caches. They can choose to do so optionally.
     * @var int
     */
    protected $maxsize = null;

    /**
     * The class to use as the cache loader for this definition.
     * @var string
     */
    protected $overrideclass = null;

    /**
     * The file in which the override class exists. This will be included if required.
     * @var string Absolute path
     */
    protected $overrideclassfile = null;

    /**
     * The data source class to use with this definition.
     * @var string
     */
    protected $datasource = null;

    /**
     * The file in which the data source class exists. This will be included if required.
     * @var string
     */
    protected $datasourcefile = null;

    /**
     * The data source class aggregate to use. This is a super advanced setting.
     * @var string
     */
    protected $datasourceaggregate = null;

    /**
     * Set to true if the definitions cache should be persistent
     * @var bool
     */
    protected $persistent = false;

    /**
     * The persistent item array max size.
     * @var int
     */
    protected $persistentmaxsize = false;

    /**
     * The TTL for data in this cache. Please don't use this, instead use event driven invalidation.
     * @var int
     */
    protected $ttl = 0;

    /**
     * Set to true if this cache should only use mapped cache stores and not the default mode cache store.
     * @var bool
     */
    protected $mappingsonly = false;

    /**
     * An array of events that should cause this cache to invalidate.
     * @var array
     */
    protected $invalidationevents = array();

    /**
     * An array of identifiers provided to this cache when it was initialised.
     * @var array
     */
    protected $identifiers = array();

    /**
     * Key prefix for use with single key cache stores
     * @var string
     */
    protected $keyprefixsingle = null;

    /**
     * Key prefix to use with cache stores that support multi keys.
     * @var array
     */
    protected $keyprefixmulti = null;

    /**
     * A hash identifier of this definition.
     * @var string
     */
    protected $definitionhash = null;

    /**
     * The selected sharing mode for this definition.
     * @var int
     */
    protected $sharingoptions;

    /**
     * The selected sharing option.
     * @var int One of self::SHARING_*
     */
    protected $selectedsharingoption = self::SHARING_DEFAULT;

    /**
     * The user input key to use if the SHARING_INPUT option has been selected.
     * @var string Must be ALPHANUMEXT
     */
    protected $userinputsharingkey = '';

    /**
     * Creates a cache definition given a definition from the cache configuration or from a caches.php file.
     *
     * @param string $id
     * @param array $definition
     * @param string $datasourceaggregate
     * @return cache_definition
     * @throws coding_exception
     */
    public static function load($id, array $definition, $datasourceaggregate = null) {
        global $CFG;

        if (!array_key_exists('mode', $definition)) {
            throw new coding_exception('You must provide a mode when creating a cache definition');
        }
        if (!array_key_exists('component', $definition)) {
            throw new coding_exception('You must provide a component when creating a cache definition');
        }
        if (!array_key_exists('area', $definition)) {
            throw new coding_exception('You must provide an area when creating a cache definition');
        }
        $mode = (int)$definition['mode'];
        $component = (string)$definition['component'];
        $area = (string)$definition['area'];

        // Set the defaults.
        $simplekeys = false;
        $simpledata = false;
        $requireidentifiers = array();
        $requiredataguarantee = false;
        $requiremultipleidentifiers = false;
        $requirelockingread = false;
        $requirelockingwrite = false;
        $requiresearchable = ($mode === cache_store::MODE_SESSION) ? true : false;
        $maxsize = null;
        $overrideclass = null;
        $overrideclassfile = null;
        $datasource = null;
        $datasourcefile = null;
        $persistent = false;
        $persistentmaxsize = false;
        $ttl = 0;
        $mappingsonly = false;
        $invalidationevents = array();
        $sharingoptions = self::SHARING_DEFAULT;
        $selectedsharingoption = self::SHARING_DEFAULT;
        $userinputsharingkey = '';

        if (array_key_exists('simplekeys', $definition)) {
            $simplekeys = (bool)$definition['simplekeys'];
        }
        if (array_key_exists('simpledata', $definition)) {
            $simpledata = (bool)$definition['simpledata'];
        }
        if (array_key_exists('requireidentifiers', $definition)) {
            $requireidentifiers = (array)$definition['requireidentifiers'];
        }
        if (array_key_exists('requiredataguarantee', $definition)) {
            $requiredataguarantee = (bool)$definition['requiredataguarantee'];
        }
        if (array_key_exists('requiremultipleidentifiers', $definition)) {
            $requiremultipleidentifiers = (bool)$definition['requiremultipleidentifiers'];
        }

        if (array_key_exists('requirelockingread', $definition)) {
            $requirelockingread = (bool)$definition['requirelockingread'];
        }
        if (array_key_exists('requirelockingwrite', $definition)) {
            $requirelockingwrite = (bool)$definition['requirelockingwrite'];
        }
        $requirelocking = $requirelockingwrite || $requirelockingread;

        if (array_key_exists('requiresearchable', $definition)) {
            $requiresearchable = (bool)$definition['requiresearchable'];
        }

        if (array_key_exists('maxsize', $definition)) {
            $maxsize = (int)$definition['maxsize'];
        }

        if (array_key_exists('overrideclass', $definition)) {
            $overrideclass = $definition['overrideclass'];
        }
        if (array_key_exists('overrideclassfile', $definition)) {
            $overrideclassfile = $definition['overrideclassfile'];
        }

        if (array_key_exists('datasource', $definition)) {
            $datasource = $definition['datasource'];
        }
        if (array_key_exists('datasourcefile', $definition)) {
            $datasourcefile = $definition['datasourcefile'];
        }

        if (array_key_exists('persistent', $definition)) {
            $persistent = (bool)$definition['persistent'];
        }
        if (array_key_exists('persistentmaxsize', $definition)) {
            $persistentmaxsize = (int)$definition['persistentmaxsize'];
        }
        if (array_key_exists('ttl', $definition)) {
            $ttl = (int)$definition['ttl'];
        }
        if (array_key_exists('mappingsonly', $definition)) {
            $mappingsonly = (bool)$definition['mappingsonly'];
        }
        if (array_key_exists('invalidationevents', $definition)) {
            $invalidationevents = (array)$definition['invalidationevents'];
        }
        if (array_key_exists('sharingoptions', $definition)) {
            $sharingoptions = (int)$definition['sharingoptions'];
        }
        if (array_key_exists('selectedsharingoption', $definition)) {
            $selectedsharingoption = (int)$definition['selectedsharingoption'];
        } else if (array_key_exists('defaultsharing', $definition)) {
            $selectedsharingoption = (int)$definition['defaultsharing'];
        } else if ($sharingoptions ^ $selectedsharingoption) {
            if ($sharingoptions & self::SHARING_SITEID) {
                $selectedsharingoption = self::SHARING_SITEID;
            } else if ($sharingoptions & self::SHARING_VERSION) {
                $selectedsharingoption = self::SHARING_VERSION;
            } else {
                $selectedsharingoption = self::SHARING_ALL;
            }
        }

        if (array_key_exists('userinputsharingkey', $definition) && !empty($definition['userinputsharingkey'])) {
            $userinputsharingkey = (string)$definition['userinputsharingkey'];
        }

        if (!is_null($overrideclass)) {
            if (!is_null($overrideclassfile)) {
                if (strpos($overrideclassfile, $CFG->dirroot) !== 0) {
                    $overrideclassfile = $CFG->dirroot.'/'.$overrideclassfile;
                }
                if (strpos($overrideclassfile, '../') !== false) {
                    throw new coding_exception('No path craziness allowed within override class file path.');
                }
                if (!file_exists($overrideclassfile)) {
                    throw new coding_exception('The override class file does not exist.');
                }
                require_once($overrideclassfile);
            }
            if (!class_exists($overrideclass)) {
                throw new coding_exception('The override class does not exist.');
            }

            // Make sure that the provided class extends the default class for the mode.
            if (get_parent_class($overrideclass) !== cache_helper::get_class_for_mode($mode)) {
                throw new coding_exception('The override class does not immediately extend the relevant cache class.');
            }
        }

        if (!is_null($datasource)) {
            if (!is_null($datasourcefile)) {
                if (strpos($datasourcefile, $CFG->dirroot) !== 0) {
                    $datasourcefile = $CFG->dirroot.'/'.$datasourcefile;
                }
                if (strpos($datasourcefile, '../') !== false) {
                    throw new coding_exception('No path craziness allowed within data source file path.');
                }
                if (!file_exists($datasourcefile)) {
                    throw new coding_exception('The data source class file does not exist.');
                }
                require_once($datasourcefile);
            }
            if (!class_exists($datasource)) {
                throw new coding_exception('The data source class does not exist.');
            }
            if (!array_key_exists('cache_data_source', class_implements($datasource))) {
                throw new coding_exception('Cache data source classes must implement the cache_data_source interface');
            }
        }

        $cachedefinition = new cache_definition();
        $cachedefinition->id = $id;
        $cachedefinition->mode = $mode;
        $cachedefinition->component = $component;
        $cachedefinition->area = $area;
        $cachedefinition->simplekeys = $simplekeys;
        $cachedefinition->simpledata = $simpledata;
        $cachedefinition->requireidentifiers = $requireidentifiers;
        $cachedefinition->requiredataguarantee = $requiredataguarantee;
        $cachedefinition->requiremultipleidentifiers = $requiremultipleidentifiers;
        $cachedefinition->requirelocking = $requirelocking;
        $cachedefinition->requirelockingread = $requirelockingread;
        $cachedefinition->requirelockingwrite = $requirelockingwrite;
        $cachedefinition->requiresearchable = $requiresearchable;
        $cachedefinition->maxsize = $maxsize;
        $cachedefinition->overrideclass = $overrideclass;
        $cachedefinition->overrideclassfile = $overrideclassfile;
        $cachedefinition->datasource = $datasource;
        $cachedefinition->datasourcefile = $datasourcefile;
        $cachedefinition->datasourceaggregate = $datasourceaggregate;
        $cachedefinition->persistent = $persistent;
        $cachedefinition->persistentmaxsize = $persistentmaxsize;
        $cachedefinition->ttl = $ttl;
        $cachedefinition->mappingsonly = $mappingsonly;
        $cachedefinition->invalidationevents = $invalidationevents;
        $cachedefinition->sharingoptions = $sharingoptions;
        $cachedefinition->selectedsharingoption = $selectedsharingoption;
        $cachedefinition->userinputsharingkey = $userinputsharingkey;

        return $cachedefinition;
    }

    /**
     * Creates an ah-hoc cache definition given the required params.
     *
     * Please note that when using an adhoc definition you cannot set any of the optional params.
     * This is because we cannot guarantee consistent access and we don't want to mislead people into thinking that.
     *
     * @param int $mode One of cache_store::MODE_*
     * @param string $component The component this definition relates to.
     * @param string $area The area this definition relates to.
     * @param array $options An array of options, available options are:
     *   - simplekeys : Set to true if the keys you will use are a-zA-Z0-9_
     *   - simpledata : Set to true if the type of the data you are going to store is scalar, or an array of scalar vars
     *   - overrideclass : The class to use as the loader.
     *   - persistent : If set to true the cache will persist construction requests.
     * @return cache_application|cache_session|cache_request
     */
    public static function load_adhoc($mode, $component, $area, array $options = array()) {
        $id = 'adhoc/'.$component.'_'.$area;
        $definition = array(
            'mode' => $mode,
            'component' => $component,
            'area' => $area,
        );
        if (!empty($options['simplekeys'])) {
            $definition['simplekeys'] = $options['simplekeys'];
        }
        if (!empty($options['simpledata'])) {
            $definition['simpledata'] = $options['simpledata'];
        }
        if (!empty($options['persistent'])) {
            $definition['persistent'] = $options['persistent'];
        }
        if (!empty($options['overrideclass'])) {
            $definition['overrideclass'] = $options['overrideclass'];
        }
        if (!empty($options['sharingoptions'])) {
            $definition['sharingoptions'] = $options['sharingoptions'];
        }
        return self::load($id, $definition, null);
    }

    /**
     * Returns the cache loader class that should be used for this definition.
     * @return string
     */
    public function get_cache_class() {
        if (!is_null($this->overrideclass)) {
            return $this->overrideclass;
        }
        return cache_helper::get_class_for_mode($this->mode);
    }

    /**
     * Returns the id of this definition.
     * @return string
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Returns the name for this definition
     * @return string
     */
    public function get_name() {
        $identifier = 'cachedef_'.clean_param($this->area, PARAM_STRINGID);
        $component = $this->component;
        if ($component === 'core') {
            $component = 'cache';
        }
        return new lang_string($identifier, $component);
    }

    /**
     * Returns the mode of this definition
     * @return int One more cache_store::MODE_
     */
    public function get_mode() {
        return $this->mode;
    }

    /**
     * Returns the area this definition is associated with.
     * @return string
     */
    public function get_area() {
        return $this->area;
    }

    /**
     * Returns the component this definition is associated with.
     * @return string
     */
    public function get_component() {
        return $this->component;
    }

    /**
     * Returns true if this definition is using simple keys.
     *
     * Simple keys contain only a-zA-Z0-9_
     *
     * @return bool
     */
    public function uses_simple_keys() {
        return $this->simplekeys;
    }

    /**
     * Returns the identifiers that are being used for this definition.
     * @return array
     */
    public function get_identifiers() {
        return $this->identifiers;
    }

    /**
     * Returns the ttl in seconds for this definition if there is one, or null if not.
     * @return int|null
     */
    public function get_ttl() {
        return $this->ttl;
    }

    /**
     * Returns the maximum number of items allowed in this cache.
     * @return int
     */
    public function get_maxsize() {
        return $this->maxsize;
    }

    /**
     * Returns true if this definition should only be used with mappings.
     * @return bool
     */
    public function is_for_mappings_only() {
        return $this->mappingsonly;
    }

    /**
     * Returns true if the data is known to be scalar or array of scalar.
     * @return bool
     */
    public function uses_simple_data() {
        return $this->simpledata;
    }

    /**
     * Returns true if this definition requires a data guarantee from the cache stores being used.
     * @return bool
     */
    public function require_data_guarantee() {
        return $this->requiredataguarantee;
    }

    /**
     * Returns true if this definition requires that the cache stores support multiple identifiers
     * @return bool
     */
    public function require_multiple_identifiers() {
        return $this->requiremultipleidentifiers;
    }

    /**
     * Returns true if this definition requires locking functionality. Either read or write locking.
     * @return bool
     */
    public function require_locking() {
        return $this->requirelocking;
    }

    /**
     * Returns true if this definition requires read locking.
     * @return bool
     */
    public function require_locking_read() {
        return $this->requirelockingread;
    }

    /**
     * Returns true if this definition requires write locking.
     * @return bool
     */
    public function require_locking_write() {
        return $this->requirelockingwrite;
    }

    /**
     * Returns true if this definition requires a searchable cache.
     * @since 2.4.4
     * @return bool
     */
    public function require_searchable() {
        return $this->requiresearchable;
    }

    /**
     * Returns true if this definition has an associated data source.
     * @return bool
     */
    public function has_data_source() {
        return !is_null($this->datasource);
    }

    /**
     * Returns an instance of the data source class used for this definition.
     *
     * @return cache_data_source
     * @throws coding_exception
     */
    public function get_data_source() {
        if (!$this->has_data_source()) {
            throw new coding_exception('This cache does not use a data source.');
        }
        return forward_static_call(array($this->datasource, 'get_instance_for_cache'), $this);
    }

    /**
     * Sets the identifiers for this definition, or updates them if they have already been set.
     *
     * @param array $identifiers
     * @throws coding_exception
     */
    public function set_identifiers(array $identifiers = array()) {
        foreach ($this->requireidentifiers as $identifier) {
            if (!array_key_exists($identifier, $identifiers)) {
                throw new coding_exception('Identifier required for cache has not been provided: '.$identifier);
            }
        }
        foreach ($identifiers as $name => $value) {
            $this->identifiers[$name] = (string)$value;
        }
        // Reset the key prefix's they need updating now.
        $this->keyprefixsingle = null;
        $this->keyprefixmulti = null;
    }

    /**
     * Returns the requirements of this definition as a binary flag.
     * @return int
     */
    public function get_requirements_bin() {
        $requires = 0;
        if ($this->require_data_guarantee()) {
            $requires += cache_store::SUPPORTS_DATA_GUARANTEE;
        }
        if ($this->require_multiple_identifiers()) {
            $requires += cache_store::SUPPORTS_MULTIPLE_IDENTIFIERS;
        }
        if ($this->require_searchable()) {
            $requires += cache_store::IS_SEARCHABLE;
        }
        return $requires;
    }

    /**
     * Returns true if this definitions cache should be made persistent.
     * @return bool
     */
    public function should_be_persistent() {
        return $this->persistent || $this->mode === cache_store::MODE_SESSION;
    }

    /**
     * Returns the max size for the persistent item array in the cache.
     * @return int
     */
    public function get_persistent_max_size() {
        return $this->persistentmaxsize;
    }

    /**
     * Generates a hash of this definition and returns it.
     * @return string
     */
    public function generate_definition_hash() {
        if ($this->definitionhash === null) {
            $this->definitionhash = md5("{$this->mode} {$this->component} {$this->area}");
        }
        return $this->definitionhash;
    }

    /**
     * Generates a single key prefix for this definition
     *
     * @return string
     */
    public function generate_single_key_prefix() {
        if ($this->keyprefixsingle === null) {
            $this->keyprefixsingle = $this->mode.'/'.$this->component.'/'.$this->area;
            $this->keyprefixsingle .= '/'.$this->get_cache_identifier();
            $identifiers = $this->get_identifiers();
            if ($identifiers) {
                foreach ($identifiers as $key => $value) {
                    $this->keyprefixsingle .= '/'.$key.'='.$value;
                }
            }
            $this->keyprefixsingle = md5($this->keyprefixsingle);
        }
        return $this->keyprefixsingle;
    }

    /**
     * Generates a multi key prefix for this definition
     *
     * @return array
     */
    public function generate_multi_key_parts() {
        if ($this->keyprefixmulti === null) {
            $this->keyprefixmulti = array(
                'mode' => $this->mode,
                'component' => $this->component,
                'area' => $this->area,
                'siteidentifier' => $this->get_cache_identifier()
            );
            if (!empty($this->identifiers)) {
                $identifiers = array();
                foreach ($this->identifiers as $key => $value) {
                    $identifiers[] = htmlentities($key, ENT_QUOTES, 'UTF-8').'='.htmlentities($value, ENT_QUOTES, 'UTF-8');
                }
                $this->keyprefixmulti['identifiers'] = join('&', $identifiers);
            }
        }
        return $this->keyprefixmulti;
    }

    /**
     * Check if this definition should invalidate on the given event.
     *
     * @param string $event
     * @return bool True if the definition should invalidate on the event. False otherwise.
     */
    public function invalidates_on_event($event) {
        return (in_array($event, $this->invalidationevents));
    }

    /**
     * Check if the definition has any invalidation events.
     *
     * @return bool True if it does, false otherwise
     */
    public function has_invalidation_events() {
        return !empty($this->invalidationevents);
    }

    /**
     * Returns all of the invalidation events for this definition.
     *
     * @return array
     */
    public function get_invalidation_events() {
        return $this->invalidationevents;
    }

    /**
     * Returns a cache identification string.
     *
     * @return string A string to be used as part of keys.
     */
    protected function get_cache_identifier() {
        $identifiers = array();
        if ($this->selectedsharingoption & self::SHARING_ALL) {
            // Nothing to do here.
        } else {
            if ($this->selectedsharingoption & self::SHARING_SITEID) {
                $identifiers[] = cache_helper::get_site_identifier();
            }
            if ($this->selectedsharingoption & self::SHARING_VERSION) {
                $identifiers[] = cache_helper::get_site_version();
            }
            if ($this->selectedsharingoption & self::SHARING_INPUT && !empty($this->userinputsharingkey)) {
                $identifiers[] = $this->userinputsharingkey;
            }
        }
        return join('/', $identifiers);
    }

    /**
     * Returns true if this definition requires identifiers.
     *
     * @param bool
     */
    public function has_required_identifiers() {
        return (count($this->requireidentifiers) > 0);
    }
}