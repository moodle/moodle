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
 * Defines classes used for plugins management
 *
 * This library provides a unified interface to various plugin types in
 * Moodle. It is mainly used by the plugins management admin page and the
 * plugins check page during the upgrade.
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Singleton class providing general plugins management functionality.
 */
class core_plugin_manager {

    /** the plugin is shipped with standard Moodle distribution */
    const PLUGIN_SOURCE_STANDARD    = 'std';
    /** the plugin is added extension */
    const PLUGIN_SOURCE_EXTENSION   = 'ext';

    /** the plugin uses neither database nor capabilities, no versions */
    const PLUGIN_STATUS_NODB        = 'nodb';
    /** the plugin is up-to-date */
    const PLUGIN_STATUS_UPTODATE    = 'uptodate';
    /** the plugin is about to be installed */
    const PLUGIN_STATUS_NEW         = 'new';
    /** the plugin is about to be upgraded */
    const PLUGIN_STATUS_UPGRADE     = 'upgrade';
    /** the standard plugin is about to be deleted */
    const PLUGIN_STATUS_DELETE     = 'delete';
    /** the version at the disk is lower than the one already installed */
    const PLUGIN_STATUS_DOWNGRADE   = 'downgrade';
    /** the plugin is installed but missing from disk */
    const PLUGIN_STATUS_MISSING     = 'missing';

    /** the given requirement/dependency is fulfilled */
    const REQUIREMENT_STATUS_OK = 'ok';
    /** the plugin requires higher core/other plugin version than is currently installed */
    const REQUIREMENT_STATUS_OUTDATED = 'outdated';
    /** the required dependency is not installed */
    const REQUIREMENT_STATUS_MISSING = 'missing';

    /** the required dependency is available in the plugins directory */
    const REQUIREMENT_AVAILABLE = 'available';
    /** the required dependency is available in the plugins directory */
    const REQUIREMENT_UNAVAILABLE = 'unavailable';

    /** @var core_plugin_manager holds the singleton instance */
    protected static $singletoninstance;
    /** @var array of raw plugins information */
    protected $pluginsinfo = null;
    /** @var array of raw subplugins information */
    protected $subpluginsinfo = null;
    /** @var array cache information about availability in the plugins directory if requesting "at least" version */
    protected $remotepluginsinfoatleast = null;
    /** @var array cache information about availability in the plugins directory if requesting exact version */
    protected $remotepluginsinfoexact = null;
    /** @var array list of installed plugins $name=>$version */
    protected $installedplugins = null;
    /** @var array list of all enabled plugins $name=>$name */
    protected $enabledplugins = null;
    /** @var array list of all enabled plugins $name=>$diskversion */
    protected $presentplugins = null;
    /** @var array reordered list of plugin types */
    protected $plugintypes = null;
    /** @var \core\update\code_manager code manager to use for plugins code operations */
    protected $codemanager = null;
    /** @var \core\update\api client instance to use for accessing download.moodle.org/api/ */
    protected $updateapiclient = null;

    /**
     * Direct initiation not allowed, use the factory method {@link self::instance()}
     */
    protected function __construct() {
    }

    /**
     * Sorry, this is singleton
     */
    protected function __clone() {
    }

    /**
     * Factory method for this class
     *
     * @return core_plugin_manager the singleton instance
     */
    public static function instance() {
        if (is_null(static::$singletoninstance)) {
            static::$singletoninstance = new static();
        }
        return static::$singletoninstance;
    }

    /**
     * Reset all caches.
     * @param bool $phpunitreset
     */
    public static function reset_caches($phpunitreset = false) {
        if ($phpunitreset) {
            static::$singletoninstance = null;
        } else {
            if (static::$singletoninstance) {
                static::$singletoninstance->pluginsinfo = null;
                static::$singletoninstance->subpluginsinfo = null;
                static::$singletoninstance->remotepluginsinfoatleast = null;
                static::$singletoninstance->remotepluginsinfoexact = null;
                static::$singletoninstance->installedplugins = null;
                static::$singletoninstance->enabledplugins = null;
                static::$singletoninstance->presentplugins = null;
                static::$singletoninstance->plugintypes = null;
                static::$singletoninstance->codemanager = null;
                static::$singletoninstance->updateapiclient = null;
            }
        }
        $cache = cache::make('core', 'plugin_manager');
        $cache->purge();
    }

    /**
     * Returns the result of {@link core_component::get_plugin_types()} ordered for humans
     *
     * @see self::reorder_plugin_types()
     * @return array (string)name => (string)location
     */
    public function get_plugin_types() {
        if (func_num_args() > 0) {
            if (!func_get_arg(0)) {
                throw new coding_exception('core_plugin_manager->get_plugin_types() does not support relative paths.');
            }
        }
        if ($this->plugintypes) {
            return $this->plugintypes;
        }

        $this->plugintypes = $this->reorder_plugin_types(core_component::get_plugin_types());
        return $this->plugintypes;
    }

    /**
     * Load list of installed plugins,
     * always call before using $this->installedplugins.
     *
     * This method is caching results for all plugins.
     */
    protected function load_installed_plugins() {
        global $DB, $CFG;

        if ($this->installedplugins) {
            return;
        }

        if (empty($CFG->version)) {
            // Nothing installed yet.
            $this->installedplugins = array();
            return;
        }

        $cache = cache::make('core', 'plugin_manager');
        $installed = $cache->get('installed');

        if (is_array($installed)) {
            $this->installedplugins = $installed;
            return;
        }

        $this->installedplugins = array();

        $versions = $DB->get_records('config_plugins', array('name'=>'version'));
        foreach ($versions as $version) {
            $parts = explode('_', $version->plugin, 2);
            if (!isset($parts[1])) {
                // Invalid component, there must be at least one "_".
                continue;
            }
            // Do not verify here if plugin type and name are valid.
            $this->installedplugins[$parts[0]][$parts[1]] = $version->value;
        }

        foreach ($this->installedplugins as $key => $value) {
            ksort($this->installedplugins[$key]);
        }

        $cache->set('installed', $this->installedplugins);
    }

    /**
     * Return list of installed plugins of given type.
     * @param string $type
     * @return array $name=>$version
     */
    public function get_installed_plugins($type) {
        $this->load_installed_plugins();
        if (isset($this->installedplugins[$type])) {
            return $this->installedplugins[$type];
        }
        return array();
    }

    /**
     * Load list of all enabled plugins,
     * call before using $this->enabledplugins.
     *
     * This method is caching results from individual plugin info classes.
     */
    protected function load_enabled_plugins() {
        global $CFG;

        if ($this->enabledplugins) {
            return;
        }

        if (empty($CFG->version)) {
            $this->enabledplugins = array();
            return;
        }

        $cache = cache::make('core', 'plugin_manager');
        $enabled = $cache->get('enabled');

        if (is_array($enabled)) {
            $this->enabledplugins = $enabled;
            return;
        }

        $this->enabledplugins = array();

        require_once($CFG->libdir.'/adminlib.php');

        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $fulldir) {
            $plugininfoclass = static::resolve_plugininfo_class($plugintype);
            if (class_exists($plugininfoclass)) {
                $enabled = $plugininfoclass::get_enabled_plugins();
                if (!is_array($enabled)) {
                    continue;
                }
                $this->enabledplugins[$plugintype] = $enabled;
            }
        }

        $cache->set('enabled', $this->enabledplugins);
    }

    /**
     * Get list of enabled plugins of given type,
     * the result may contain missing plugins.
     *
     * @param string $type
     * @return array|null  list of enabled plugins of this type, null if unknown
     */
    public function get_enabled_plugins($type) {
        $this->load_enabled_plugins();
        if (isset($this->enabledplugins[$type])) {
            return $this->enabledplugins[$type];
        }
        return null;
    }

    /**
     * Load list of all present plugins - call before using $this->presentplugins.
     */
    protected function load_present_plugins() {
        if ($this->presentplugins) {
            return;
        }

        $cache = cache::make('core', 'plugin_manager');
        $present = $cache->get('present');

        if (is_array($present)) {
            $this->presentplugins = $present;
            return;
        }

        $this->presentplugins = array();

        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $type => $typedir) {
            $plugs = core_component::get_plugin_list($type);
            foreach ($plugs as $plug => $fullplug) {
                $module = new stdClass();
                $plugin = new stdClass();
                $plugin->version = null;
                include($fullplug.'/version.php');

                // Check if the legacy $module syntax is still used.
                if (!is_object($module) or (count((array)$module) > 0)) {
                    debugging('Unsupported $module syntax detected in version.php of the '.$type.'_'.$plug.' plugin.');
                    $skipcache = true;
                }

                // Check if the component is properly declared.
                if (empty($plugin->component) or ($plugin->component !== $type.'_'.$plug)) {
                    debugging('Plugin '.$type.'_'.$plug.' does not declare valid $plugin->component in its version.php.');
                    $skipcache = true;
                }

                $this->presentplugins[$type][$plug] = $plugin;
            }
        }

        if (empty($skipcache)) {
            $cache->set('present', $this->presentplugins);
        }
    }

    /**
     * Get list of present plugins of given type.
     *
     * @param string $type
     * @return array|null  list of presnet plugins $name=>$diskversion, null if unknown
     */
    public function get_present_plugins($type) {
        $this->load_present_plugins();
        if (isset($this->presentplugins[$type])) {
            return $this->presentplugins[$type];
        }
        return null;
    }

    /**
     * Returns a tree of known plugins and information about them
     *
     * @return array 2D array. The first keys are plugin type names (e.g. qtype);
     *      the second keys are the plugin local name (e.g. multichoice); and
     *      the values are the corresponding objects extending {@link \core\plugininfo\base}
     */
    public function get_plugins() {
        $this->init_pluginsinfo_property();

        // Make sure all types are initialised.
        foreach ($this->pluginsinfo as $plugintype => $list) {
            if ($list === null) {
                $this->get_plugins_of_type($plugintype);
            }
        }

        return $this->pluginsinfo;
    }

    /**
     * Returns list of known plugins of the given type.
     *
     * This method returns the subset of the tree returned by {@link self::get_plugins()}.
     * If the given type is not known, empty array is returned.
     *
     * @param string $type plugin type, e.g. 'mod' or 'workshopallocation'
     * @return \core\plugininfo\base[] (string)plugin name (e.g. 'workshop') => corresponding subclass of {@link \core\plugininfo\base}
     */
    public function get_plugins_of_type($type) {
        global $CFG;

        $this->init_pluginsinfo_property();

        if (!array_key_exists($type, $this->pluginsinfo)) {
            return array();
        }

        if (is_array($this->pluginsinfo[$type])) {
            return $this->pluginsinfo[$type];
        }

        $types = core_component::get_plugin_types();

        if (!isset($types[$type])) {
            // Orphaned subplugins!
            $plugintypeclass = static::resolve_plugininfo_class($type);
            $this->pluginsinfo[$type] = $plugintypeclass::get_plugins($type, null, $plugintypeclass, $this);
            return $this->pluginsinfo[$type];
        }

        /** @var \core\plugininfo\base $plugintypeclass */
        $plugintypeclass = static::resolve_plugininfo_class($type);
        $plugins = $plugintypeclass::get_plugins($type, $types[$type], $plugintypeclass, $this);
        $this->pluginsinfo[$type] = $plugins;

        return $this->pluginsinfo[$type];
    }

    /**
     * Init placeholder array for plugin infos.
     */
    protected function init_pluginsinfo_property() {
        if (is_array($this->pluginsinfo)) {
            return;
        }
        $this->pluginsinfo = array();

        $plugintypes = $this->get_plugin_types();

        foreach ($plugintypes as $plugintype => $plugintyperootdir) {
            $this->pluginsinfo[$plugintype] = null;
        }

        // Add orphaned subplugin types.
        $this->load_installed_plugins();
        foreach ($this->installedplugins as $plugintype => $unused) {
            if (!isset($plugintypes[$plugintype])) {
                $this->pluginsinfo[$plugintype] = null;
            }
        }
    }

    /**
     * Find the plugin info class for given type.
     *
     * @param string $type
     * @return string name of pluginfo class for give plugin type
     */
    public static function resolve_plugininfo_class($type) {
        $plugintypes = core_component::get_plugin_types();
        if (!isset($plugintypes[$type])) {
            return '\core\plugininfo\orphaned';
        }

        $parent = core_component::get_subtype_parent($type);

        if ($parent) {
            $class = '\\'.$parent.'\plugininfo\\' . $type;
            if (class_exists($class)) {
                $plugintypeclass = $class;
            } else {
                if ($dir = core_component::get_component_directory($parent)) {
                    // BC only - use namespace instead!
                    if (file_exists("$dir/adminlib.php")) {
                        global $CFG;
                        include_once("$dir/adminlib.php");
                    }
                    if (class_exists('plugininfo_' . $type)) {
                        $plugintypeclass = 'plugininfo_' . $type;
                        debugging('Class "'.$plugintypeclass.'" is deprecated, migrate to "'.$class.'"', DEBUG_DEVELOPER);
                    } else {
                        debugging('Subplugin type "'.$type.'" should define class "'.$class.'"', DEBUG_DEVELOPER);
                        $plugintypeclass = '\core\plugininfo\general';
                    }
                } else {
                    $plugintypeclass = '\core\plugininfo\general';
                }
            }
        } else {
            $class = '\core\plugininfo\\' . $type;
            if (class_exists($class)) {
                $plugintypeclass = $class;
            } else {
                debugging('All standard types including "'.$type.'" should have plugininfo class!', DEBUG_DEVELOPER);
                $plugintypeclass = '\core\plugininfo\general';
            }
        }

        if (!in_array('core\plugininfo\base', class_parents($plugintypeclass))) {
            throw new coding_exception('Class ' . $plugintypeclass . ' must extend \core\plugininfo\base');
        }

        return $plugintypeclass;
    }

    /**
     * Returns list of all known subplugins of the given plugin.
     *
     * For plugins that do not provide subplugins (i.e. there is no support for it),
     * empty array is returned.
     *
     * @param string $component full component name, e.g. 'mod_workshop'
     * @return array (string) component name (e.g. 'workshopallocation_random') => subclass of {@link \core\plugininfo\base}
     */
    public function get_subplugins_of_plugin($component) {

        $pluginfo = $this->get_plugin_info($component);

        if (is_null($pluginfo)) {
            return array();
        }

        $subplugins = $this->get_subplugins();

        if (!isset($subplugins[$pluginfo->component])) {
            return array();
        }

        $list = array();

        foreach ($subplugins[$pluginfo->component] as $subdata) {
            foreach ($this->get_plugins_of_type($subdata->type) as $subpluginfo) {
                $list[$subpluginfo->component] = $subpluginfo;
            }
        }

        return $list;
    }

    /**
     * Returns list of plugins that define their subplugins and the information
     * about them from the db/subplugins.php file.
     *
     * @return array with keys like 'mod_quiz', and values the data from the
     *      corresponding db/subplugins.php file.
     */
    public function get_subplugins() {

        if (is_array($this->subpluginsinfo)) {
            return $this->subpluginsinfo;
        }

        $plugintypes = core_component::get_plugin_types();

        $this->subpluginsinfo = array();
        foreach (core_component::get_plugin_types_with_subplugins() as $type => $ignored) {
            foreach (core_component::get_plugin_list($type) as $plugin => $componentdir) {
                $component = $type.'_'.$plugin;
                $subplugins = core_component::get_subplugins($component);
                if (!$subplugins) {
                    continue;
                }
                $this->subpluginsinfo[$component] = array();
                foreach ($subplugins as $subplugintype => $ignored) {
                    $subplugin = new stdClass();
                    $subplugin->type = $subplugintype;
                    $subplugin->typerootdir = $plugintypes[$subplugintype];
                    $this->subpluginsinfo[$component][$subplugintype] = $subplugin;
                }
            }
        }
        return $this->subpluginsinfo;
    }

    /**
     * Returns the name of the plugin that defines the given subplugin type
     *
     * If the given subplugin type is not actually a subplugin, returns false.
     *
     * @param string $subplugintype the name of subplugin type, eg. workshopform or quiz
     * @return false|string the name of the parent plugin, eg. mod_workshop
     */
    public function get_parent_of_subplugin($subplugintype) {
        $parent = core_component::get_subtype_parent($subplugintype);
        if (!$parent) {
            return false;
        }
        return $parent;
    }

    /**
     * Returns a localized name of a given plugin
     *
     * @param string $component name of the plugin, eg mod_workshop or auth_ldap
     * @return string
     */
    public function plugin_name($component) {

        $pluginfo = $this->get_plugin_info($component);

        if (is_null($pluginfo)) {
            throw new moodle_exception('err_unknown_plugin', 'core_plugin', '', array('plugin' => $component));
        }

        return $pluginfo->displayname;
    }

    /**
     * Returns a localized name of a plugin typed in singular form
     *
     * Most plugin types define their names in core_plugin lang file. In case of subplugins,
     * we try to ask the parent plugin for the name. In the worst case, we will return
     * the value of the passed $type parameter.
     *
     * @param string $type the type of the plugin, e.g. mod or workshopform
     * @return string
     */
    public function plugintype_name($type) {

        if (get_string_manager()->string_exists('type_' . $type, 'core_plugin')) {
            // For most plugin types, their names are defined in core_plugin lang file.
            return get_string('type_' . $type, 'core_plugin');

        } else if ($parent = $this->get_parent_of_subplugin($type)) {
            // If this is a subplugin, try to ask the parent plugin for the name.
            if (get_string_manager()->string_exists('subplugintype_' . $type, $parent)) {
                return $this->plugin_name($parent) . ' / ' . get_string('subplugintype_' . $type, $parent);
            } else {
                return $this->plugin_name($parent) . ' / ' . $type;
            }

        } else {
            return $type;
        }
    }

    /**
     * Returns a localized name of a plugin type in plural form
     *
     * Most plugin types define their names in core_plugin lang file. In case of subplugins,
     * we try to ask the parent plugin for the name. In the worst case, we will return
     * the value of the passed $type parameter.
     *
     * @param string $type the type of the plugin, e.g. mod or workshopform
     * @return string
     */
    public function plugintype_name_plural($type) {

        if (get_string_manager()->string_exists('type_' . $type . '_plural', 'core_plugin')) {
            // For most plugin types, their names are defined in core_plugin lang file.
            return get_string('type_' . $type . '_plural', 'core_plugin');

        } else if ($parent = $this->get_parent_of_subplugin($type)) {
            // If this is a subplugin, try to ask the parent plugin for the name.
            if (get_string_manager()->string_exists('subplugintype_' . $type . '_plural', $parent)) {
                return $this->plugin_name($parent) . ' / ' . get_string('subplugintype_' . $type . '_plural', $parent);
            } else {
                return $this->plugin_name($parent) . ' / ' . $type;
            }

        } else {
            return $type;
        }
    }

    /**
     * Returns information about the known plugin, or null
     *
     * @param string $component frankenstyle component name.
     * @return \core\plugininfo\base|null the corresponding plugin information.
     */
    public function get_plugin_info($component) {
        list($type, $name) = core_component::normalize_component($component);
        $plugins = $this->get_plugins_of_type($type);
        if (isset($plugins[$name])) {
            return $plugins[$name];
        } else {
            return null;
        }
    }

    /**
     * Check to see if the current version of the plugin seems to be a checkout of an external repository.
     *
     * @param string $component frankenstyle component name
     * @return false|string
     */
    public function plugin_external_source($component) {

        $plugininfo = $this->get_plugin_info($component);

        if (is_null($plugininfo)) {
            return false;
        }

        $pluginroot = $plugininfo->rootdir;

        if (is_dir($pluginroot.'/.git')) {
            return 'git';
        }

        if (is_file($pluginroot.'/.git')) {
            return 'git-submodule';
        }

        if (is_dir($pluginroot.'/CVS')) {
            return 'cvs';
        }

        if (is_dir($pluginroot.'/.svn')) {
            return 'svn';
        }

        if (is_dir($pluginroot.'/.hg')) {
            return 'mercurial';
        }

        return false;
    }

    /**
     * Get a list of any other plugins that require this one.
     * @param string $component frankenstyle component name.
     * @return array of frankensyle component names that require this one.
     */
    public function other_plugins_that_require($component) {
        $others = array();
        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                $required = $plugin->get_other_required_plugins();
                if (isset($required[$component])) {
                    $others[] = $plugin->component;
                }
            }
        }
        return $others;
    }

    /**
     * Check a dependencies list against the list of installed plugins.
     * @param array $dependencies compenent name to required version or ANY_VERSION.
     * @return bool true if all the dependencies are satisfied.
     */
    public function are_dependencies_satisfied($dependencies) {
        foreach ($dependencies as $component => $requiredversion) {
            $otherplugin = $this->get_plugin_info($component);
            if (is_null($otherplugin)) {
                return false;
            }

            if ($requiredversion != ANY_VERSION and $otherplugin->versiondisk < $requiredversion) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks all dependencies for all installed plugins
     *
     * This is used by install and upgrade. The array passed by reference as the second
     * argument is populated with the list of plugins that have failed dependencies (note that
     * a single plugin can appear multiple times in the $failedplugins).
     *
     * @param int $moodleversion the version from version.php.
     * @param array $failedplugins to return the list of plugins with non-satisfied dependencies
     * @return bool true if all the dependencies are satisfied for all plugins.
     */
    public function all_plugins_ok($moodleversion, &$failedplugins = array()) {

        $return = true;
        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {

                if (!$plugin->is_core_dependency_satisfied($moodleversion)) {
                    $return = false;
                    $failedplugins[] = $plugin->component;
                }

                if (!$this->are_dependencies_satisfied($plugin->get_other_required_plugins())) {
                    $return = false;
                    $failedplugins[] = $plugin->component;
                }
            }
        }

        return $return;
    }

    /**
     * Resolve requirements and dependencies of a plugin.
     *
     * Returns an array of objects describing the requirement/dependency,
     * indexed by the frankenstyle name of the component. The returned array
     * can be empty. The objects in the array have following properties:
     *
     *  ->(numeric)hasver
     *  ->(numeric)reqver
     *  ->(string)status
     *  ->(string)availability
     *
     * @param \core\plugininfo\base $plugin the plugin we are checking
     * @param null|string|int|double $moodleversion explicit moodle core version to check against, defaults to $CFG->version
     * @param null|string|int $moodlebranch explicit moodle core branch to check against, defaults to $CFG->branch
     * @return array of objects
     */
    public function resolve_requirements(\core\plugininfo\base $plugin, $moodleversion=null, $moodlebranch=null) {
        global $CFG;

        if ($plugin->versiondisk === null) {
            // Missing from disk, we have no version.php to read from.
            return array();
        }

        if ($moodleversion === null) {
            $moodleversion = $CFG->version;
        }

        if ($moodlebranch === null) {
            $moodlebranch = $CFG->branch;
        }

        $reqs = array();
        $reqcore = $this->resolve_core_requirements($plugin, $moodleversion);

        if (!empty($reqcore)) {
            $reqs['core'] = $reqcore;
        }

        foreach ($plugin->get_other_required_plugins() as $reqplug => $reqver) {
            $reqs[$reqplug] = $this->resolve_dependency_requirements($plugin, $reqplug, $reqver, $moodlebranch);
        }

        return $reqs;
    }

    /**
     * Helper method to resolve plugin's requirements on the moodle core.
     *
     * @param \core\plugininfo\base $plugin the plugin we are checking
     * @param string|int|double $moodleversion moodle core branch to check against
     * @return stdObject
     */
    protected function resolve_core_requirements(\core\plugininfo\base $plugin, $moodleversion) {

        $reqs = (object)array(
            'hasver' => null,
            'reqver' => null,
            'status' => null,
            'availability' => null,
        );

        $reqs->hasver = $moodleversion;

        if (empty($plugin->versionrequires)) {
            $reqs->reqver = ANY_VERSION;
        } else {
            $reqs->reqver = $plugin->versionrequires;
        }

        if ($plugin->is_core_dependency_satisfied($moodleversion)) {
            $reqs->status = self::REQUIREMENT_STATUS_OK;
        } else {
            $reqs->status = self::REQUIREMENT_STATUS_OUTDATED;
        }

        return $reqs;
    }

    /**
     * Helper method to resolve plugin's dependecies on other plugins.
     *
     * @param \core\plugininfo\base $plugin the plugin we are checking
     * @param string $otherpluginname
     * @param string|int $requiredversion
     * @param string|int $moodlebranch explicit moodle core branch to check against, defaults to $CFG->branch
     * @return stdClass
     */
    protected function resolve_dependency_requirements(\core\plugininfo\base $plugin, $otherpluginname,
            $requiredversion, $moodlebranch) {

        $reqs = (object)array(
            'hasver' => null,
            'reqver' => null,
            'status' => null,
            'availability' => null,
        );

        $otherplugin = $this->get_plugin_info($otherpluginname);

        if ($otherplugin !== null) {
            // The required plugin is installed.
            $reqs->hasver = $otherplugin->versiondisk;
            $reqs->reqver = $requiredversion;
            // Check it has sufficient version.
            if ($requiredversion == ANY_VERSION or $otherplugin->versiondisk >= $requiredversion) {
                $reqs->status = self::REQUIREMENT_STATUS_OK;
            } else {
                $reqs->status = self::REQUIREMENT_STATUS_OUTDATED;
            }

        } else {
            // The required plugin is not installed.
            $reqs->hasver = null;
            $reqs->reqver = $requiredversion;
            $reqs->status = self::REQUIREMENT_STATUS_MISSING;
        }

        if ($reqs->status !== self::REQUIREMENT_STATUS_OK) {
            if ($this->is_remote_plugin_available($otherpluginname, $requiredversion, false)) {
                $reqs->availability = self::REQUIREMENT_AVAILABLE;
            } else {
                $reqs->availability = self::REQUIREMENT_UNAVAILABLE;
            }
        }

        return $reqs;
    }

    /**
     * Is the given plugin version available in the plugins directory?
     *
     * See {@link self::get_remote_plugin_info()} for the full explanation of how the $version
     * parameter is interpretted.
     *
     * @param string $component plugin frankenstyle name
     * @param string|int $version ANY_VERSION or the version number
     * @param bool $exactmatch false if "given version or higher" is requested
     * @return boolean
     */
    public function is_remote_plugin_available($component, $version, $exactmatch) {

        $info = $this->get_remote_plugin_info($component, $version, $exactmatch);

        if (empty($info)) {
            // There is no available plugin of that name.
            return false;
        }

        if (empty($info->version)) {
            // Plugin is known, but no suitable version was found.
            return false;
        }

        return true;
    }

    /**
     * Can the given plugin version be installed via the admin UI?
     *
     * This check should be used whenever attempting to install a plugin from
     * the plugins directory (new install, available update, missing dependency).
     *
     * @param string $component
     * @param int $version version number
     * @param string $reason returned code of the reason why it is not
     * @return boolean
     */
    public function is_remote_plugin_installable($component, $version, &$reason=null) {
        global $CFG;

        // Make sure the feature is not disabled.
        if (!empty($CFG->disableupdateautodeploy)) {
            $reason = 'disabled';
            return false;
        }

        // Make sure the version is available.
        if (!$this->is_remote_plugin_available($component, $version, true)) {
            $reason = 'remoteunavailable';
            return false;
        }

        // Make sure the plugin type root directory is writable.
        list($plugintype, $pluginname) = core_component::normalize_component($component);
        if (!$this->is_plugintype_writable($plugintype)) {
            $reason = 'notwritableplugintype';
            return false;
        }

        $remoteinfo = $this->get_remote_plugin_info($component, $version, true);
        $localinfo = $this->get_plugin_info($component);

        if ($localinfo) {
            // If the plugin is already present, prevent downgrade.
            if ($localinfo->versiondb > $remoteinfo->version->version) {
                $reason = 'cannotdowngrade';
                return false;
            }

            // Make sure we have write access to all the existing code.
            if (is_dir($localinfo->rootdir)) {
                if (!$this->is_plugin_folder_removable($component)) {
                    $reason = 'notwritableplugin';
                    return false;
                }
            }
        }

        // Looks like it could work.
        return true;
    }

    /**
     * Given the list of remote plugin infos, return just those installable.
     *
     * This is typically used on lists returned by
     * {@link self::available_updates()} or {@link self::missing_dependencies()}
     * to perform bulk installation of remote plugins.
     *
     * @param array $remoteinfos list of {@link \core\update\remote_info}
     * @return array
     */
    public function filter_installable($remoteinfos) {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            return array();
        }
        if (empty($remoteinfos)) {
            return array();
        }
        $installable = array();
        foreach ($remoteinfos as $index => $remoteinfo) {
            if ($this->is_remote_plugin_installable($remoteinfo->component, $remoteinfo->version->version)) {
                $installable[$index] = $remoteinfo;
            }
        }
        return $installable;
    }

    /**
     * Returns information about a plugin in the plugins directory.
     *
     * This is typically used when checking for available dependencies (in
     * which case the $version represents minimal version we need), or
     * when installing an available update or a new plugin from the plugins
     * directory (in which case the $version is exact version we are
     * interested in). The interpretation of the $version is controlled
     * by the $exactmatch argument.
     *
     * If a plugin with the given component name is found, data about the
     * plugin are returned as an object. The ->version property of the object
     * contains the information about the particular plugin version that
     * matches best the given critera. The ->version property is false if no
     * suitable version of the plugin was found (yet the plugin itself is
     * known).
     *
     * See {@link \core\update\api::validate_pluginfo_format()} for the
     * returned data structure.
     *
     * @param string $component plugin frankenstyle name
     * @param string|int $version ANY_VERSION or the version number
     * @param bool $exactmatch false if "given version or higher" is requested
     * @return \core\update\remote_info|bool
     */
    public function get_remote_plugin_info($component, $version, $exactmatch) {

        if ($exactmatch and $version == ANY_VERSION) {
            throw new coding_exception('Invalid request for exactly any version, it does not make sense.');
        }

        $client = $this->get_update_api_client();

        if ($exactmatch) {
            // Use client's get_plugin_info() method.
            if (!isset($this->remotepluginsinfoexact[$component][$version])) {
                $this->remotepluginsinfoexact[$component][$version] = $client->get_plugin_info($component, $version);
            }
            return $this->remotepluginsinfoexact[$component][$version];

        } else {
            // Use client's find_plugin() method.
            if (!isset($this->remotepluginsinfoatleast[$component][$version])) {
                $this->remotepluginsinfoatleast[$component][$version] = $client->find_plugin($component, $version);
            }
            return $this->remotepluginsinfoatleast[$component][$version];
        }
    }

    /**
     * Obtain the plugin ZIP file from the given URL
     *
     * The caller is supposed to know both downloads URL and the MD5 hash of
     * the ZIP contents in advance, typically by using the API requests against
     * the plugins directory.
     *
     * @param string $url
     * @param string $md5
     * @return string|bool full path to the file, false on error
     */
    public function get_remote_plugin_zip($url, $md5) {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            return false;
        }
        return $this->get_code_manager()->get_remote_plugin_zip($url, $md5);
    }

    /**
     * Extracts the saved plugin ZIP file.
     *
     * Returns the list of files found in the ZIP. The format of that list is
     * array of (string)filerelpath => (bool|string) where the array value is
     * either true or a string describing the problematic file.
     *
     * @see zip_packer::extract_to_pathname()
     * @param string $zipfilepath full path to the saved ZIP file
     * @param string $targetdir full path to the directory to extract the ZIP file to
     * @param string $rootdir explicitly rename the root directory of the ZIP into this non-empty value
     * @return array list of extracted files as returned by {@link zip_packer::extract_to_pathname()}
     */
    public function unzip_plugin_file($zipfilepath, $targetdir, $rootdir = '') {
        return $this->get_code_manager()->unzip_plugin_file($zipfilepath, $targetdir, $rootdir);
    }

    /**
     * Detects the plugin's name from its ZIP file.
     *
     * Plugin ZIP packages are expected to contain a single directory and the
     * directory name would become the plugin name once extracted to the Moodle
     * dirroot.
     *
     * @param string $zipfilepath full path to the ZIP files
     * @return string|bool false on error
     */
    public function get_plugin_zip_root_dir($zipfilepath) {
        return $this->get_code_manager()->get_plugin_zip_root_dir($zipfilepath);
    }

    /**
     * Return a list of missing dependencies.
     *
     * This should provide the full list of plugins that should be installed to
     * fulfill the requirements of all plugins, if possible.
     *
     * @param bool $availableonly return only available missing dependencies
     * @return array of \core\update\remote_info|bool indexed by the component name
     */
    public function missing_dependencies($availableonly=false) {

        $dependencies = array();

        foreach ($this->get_plugins() as $plugintype => $pluginfos) {
            foreach ($pluginfos as $pluginname => $pluginfo) {
                foreach ($this->resolve_requirements($pluginfo) as $reqname => $reqinfo) {
                    if ($reqname === 'core') {
                        continue;
                    }
                    if ($reqinfo->status != self::REQUIREMENT_STATUS_OK) {
                        if ($reqinfo->availability == self::REQUIREMENT_AVAILABLE) {
                            $remoteinfo = $this->get_remote_plugin_info($reqname, $reqinfo->reqver, false);

                            if (empty($dependencies[$reqname])) {
                                $dependencies[$reqname] = $remoteinfo;
                            } else {
                                // If resolving requirements has led to two different versions of the same
                                // remote plugin, pick the higher version. This can happen in cases like one
                                // plugin requiring ANY_VERSION and another plugin requiring specific higher
                                // version with lower maturity of a remote plugin.
                                if ($remoteinfo->version->version > $dependencies[$reqname]->version->version) {
                                    $dependencies[$reqname] = $remoteinfo;
                                }
                            }

                        } else {
                            if (!isset($dependencies[$reqname])) {
                                // Unable to find a plugin fulfilling the requirements.
                                $dependencies[$reqname] = false;
                            }
                        }
                    }
                }
            }
        }

        if ($availableonly) {
            foreach ($dependencies as $component => $info) {
                if (empty($info) or empty($info->version)) {
                    unset($dependencies[$component]);
                }
            }
        }

        return $dependencies;
    }

    /**
     * Is it possible to uninstall the given plugin?
     *
     * False is returned if the plugininfo subclass declares the uninstall should
     * not be allowed via {@link \core\plugininfo\base::is_uninstall_allowed()} or if the
     * core vetoes it (e.g. becase the plugin or some of its subplugins is required
     * by some other installed plugin).
     *
     * @param string $component full frankenstyle name, e.g. mod_foobar
     * @return bool
     */
    public function can_uninstall_plugin($component) {

        $pluginfo = $this->get_plugin_info($component);

        if (is_null($pluginfo)) {
            return false;
        }

        if (!$this->common_uninstall_check($pluginfo)) {
            return false;
        }

        // Verify only if something else requires the subplugins, do not verify their common_uninstall_check()!
        $subplugins = $this->get_subplugins_of_plugin($pluginfo->component);
        foreach ($subplugins as $subpluginfo) {
            // Check if there are some other plugins requiring this subplugin
            // (but the parent and siblings).
            foreach ($this->other_plugins_that_require($subpluginfo->component) as $requiresme) {
                $ismyparent = ($pluginfo->component === $requiresme);
                $ismysibling = in_array($requiresme, array_keys($subplugins));
                if (!$ismyparent and !$ismysibling) {
                    return false;
                }
            }
        }

        // Check if there are some other plugins requiring this plugin
        // (but its subplugins).
        foreach ($this->other_plugins_that_require($pluginfo->component) as $requiresme) {
            $ismysubplugin = in_array($requiresme, array_keys($subplugins));
            if (!$ismysubplugin) {
                return false;
            }
        }

        return true;
    }

    /**
     * Perform the installation of plugins.
     *
     * If used for installation of remote plugins from the Moodle Plugins
     * directory, the $plugins must be list of {@link \core\update\remote_info}
     * object that represent installable remote plugins. The caller can use
     * {@link self::filter_installable()} to prepare the list.
     *
     * If used for installation of plugins from locally available ZIP files,
     * the $plugins should be list of objects with properties ->component and
     * ->zipfilepath.
     *
     * The method uses {@link mtrace()} to produce direct output and can be
     * used in both web and cli interfaces.
     *
     * @param array $plugins list of plugins
     * @param bool $confirmed should the files be really deployed into the dirroot?
     * @param bool $silent perform without output
     * @return bool true on success
     */
    public function install_plugins(array $plugins, $confirmed, $silent) {
        global $CFG, $OUTPUT;

        if (!empty($CFG->disableupdateautodeploy)) {
            return false;
        }

        if (empty($plugins)) {
            return false;
        }

        $ok = get_string('ok', 'core');

        // Let admins know they can expect more verbose output.
        $silent or $this->mtrace(get_string('packagesdebug', 'core_plugin'), PHP_EOL, DEBUG_NORMAL);

        // Download all ZIP packages if we do not have them yet.
        $zips = array();
        foreach ($plugins as $plugin) {
            if ($plugin instanceof \core\update\remote_info) {
                $zips[$plugin->component] = $this->get_remote_plugin_zip($plugin->version->downloadurl,
                    $plugin->version->downloadmd5);
                $silent or $this->mtrace(get_string('packagesdownloading', 'core_plugin', $plugin->component), ' ... ');
                $silent or $this->mtrace(PHP_EOL.' <- '.$plugin->version->downloadurl, '', DEBUG_DEVELOPER);
                $silent or $this->mtrace(PHP_EOL.' -> '.$zips[$plugin->component], ' ... ', DEBUG_DEVELOPER);
                if (!$zips[$plugin->component]) {
                    $silent or $this->mtrace(get_string('error'));
                    return false;
                }
                $silent or $this->mtrace($ok);
            } else {
                if (empty($plugin->zipfilepath)) {
                    throw new coding_exception('Unexpected data structure provided');
                }
                $zips[$plugin->component] = $plugin->zipfilepath;
                $silent or $this->mtrace('ZIP '.$plugin->zipfilepath, PHP_EOL, DEBUG_DEVELOPER);
            }
        }

        // Validate all downloaded packages.
        foreach ($plugins as $plugin) {
            $zipfile = $zips[$plugin->component];
            $silent or $this->mtrace(get_string('packagesvalidating', 'core_plugin', $plugin->component), ' ... ');
            list($plugintype, $pluginname) = core_component::normalize_component($plugin->component);
            $tmp = make_request_directory();
            $zipcontents = $this->unzip_plugin_file($zipfile, $tmp, $pluginname);
            if (empty($zipcontents)) {
                $silent or $this->mtrace(get_string('error'));
                $silent or $this->mtrace('Unable to unzip '.$zipfile, PHP_EOL, DEBUG_DEVELOPER);
                return false;
            }

            $validator = \core\update\validator::instance($tmp, $zipcontents);
            $validator->assert_plugin_type($plugintype);
            $validator->assert_moodle_version($CFG->version);
            // TODO Check for missing dependencies during validation.
            $result = $validator->execute();
            if (!$silent) {
                $result ? $this->mtrace($ok) : $this->mtrace(get_string('error'));
                foreach ($validator->get_messages() as $message) {
                    if ($message->level === $validator::INFO) {
                        // Display [OK] validation messages only if debugging mode is DEBUG_NORMAL.
                        $level = DEBUG_NORMAL;
                    } else if ($message->level === $validator::DEBUG) {
                        // Display [Debug] validation messages only if debugging mode is DEBUG_ALL.
                        $level = DEBUG_ALL;
                    } else {
                        // Display [Warning] and [Error] always.
                        $level = null;
                    }
                    if ($message->level === $validator::WARNING and !CLI_SCRIPT) {
                        $this->mtrace('  <strong>['.$validator->message_level_name($message->level).']</strong>', ' ', $level);
                    } else {
                        $this->mtrace('  ['.$validator->message_level_name($message->level).']', ' ', $level);
                    }
                    $this->mtrace($validator->message_code_name($message->msgcode), ' ', $level);
                    $info = $validator->message_code_info($message->msgcode, $message->addinfo);
                    if ($info) {
                        $this->mtrace('['.s($info).']', ' ', $level);
                    } else if (is_string($message->addinfo)) {
                        $this->mtrace('['.s($message->addinfo, true).']', ' ', $level);
                    } else {
                        $this->mtrace('['.s(json_encode($message->addinfo, true)).']', ' ', $level);
                    }
                    if ($icon = $validator->message_help_icon($message->msgcode)) {
                        if (CLI_SCRIPT) {
                            $this->mtrace(PHP_EOL.'  ^^^ '.get_string('help').': '.
                                get_string($icon->identifier.'_help', $icon->component), '', $level);
                        } else {
                            $this->mtrace($OUTPUT->render($icon), ' ', $level);
                        }
                    }
                    $this->mtrace(PHP_EOL, '', $level);
                }
            }
            if (!$result) {
                $silent or $this->mtrace(get_string('packagesvalidatingfailed', 'core_plugin'));
                return false;
            }
        }
        $silent or $this->mtrace(PHP_EOL.get_string('packagesvalidatingok', 'core_plugin'));

        if (!$confirmed) {
            return true;
        }

        // Extract all ZIP packs do the dirroot.
        foreach ($plugins as $plugin) {
            $silent or $this->mtrace(get_string('packagesextracting', 'core_plugin', $plugin->component), ' ... ');
            $zipfile = $zips[$plugin->component];
            list($plugintype, $pluginname) = core_component::normalize_component($plugin->component);
            $target = $this->get_plugintype_root($plugintype);
            if (file_exists($target.'/'.$pluginname)) {
                $this->remove_plugin_folder($this->get_plugin_info($plugin->component));
            }
            if (!$this->unzip_plugin_file($zipfile, $target, $pluginname)) {
                $silent or $this->mtrace(get_string('error'));
                $silent or $this->mtrace('Unable to unzip '.$zipfile, PHP_EOL, DEBUG_DEVELOPER);
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                }
                return false;
            }
            $silent or $this->mtrace($ok);
        }
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return true;
    }

    /**
     * Outputs the given message via {@link mtrace()}.
     *
     * If $debug is provided, then the message is displayed only at the given
     * debugging level (e.g. DEBUG_DEVELOPER to display the message only if the
     * site has developer debugging level selected).
     *
     * @param string $msg message
     * @param string $eol end of line
     * @param null|int $debug null to display always, int only on given debug level
     */
    protected function mtrace($msg, $eol=PHP_EOL, $debug=null) {
        global $CFG;

        if ($debug !== null and !debugging(null, $debug)) {
            return;
        }

        mtrace($msg, $eol);
    }

    /**
     * Returns uninstall URL if exists.
     *
     * @param string $component
     * @param string $return either 'overview' or 'manage'
     * @return moodle_url uninstall URL, null if uninstall not supported
     */
    public function get_uninstall_url($component, $return = 'overview') {
        if (!$this->can_uninstall_plugin($component)) {
            return null;
        }

        $pluginfo = $this->get_plugin_info($component);

        if (is_null($pluginfo)) {
            return null;
        }

        if (method_exists($pluginfo, 'get_uninstall_url')) {
            debugging('plugininfo method get_uninstall_url() is deprecated, all plugins should be uninstalled via standard URL only.');
            return $pluginfo->get_uninstall_url($return);
        }

        return $pluginfo->get_default_uninstall_url($return);
    }

    /**
     * Uninstall the given plugin.
     *
     * Automatically cleans-up all remaining configuration data, log records, events,
     * files from the file pool etc.
     *
     * In the future, the functionality of {@link uninstall_plugin()} function may be moved
     * into this method and all the code should be refactored to use it. At the moment, we
     * mimic this future behaviour by wrapping that function call.
     *
     * @param string $component
     * @param progress_trace $progress traces the process
     * @return bool true on success, false on errors/problems
     */
    public function uninstall_plugin($component, progress_trace $progress) {

        $pluginfo = $this->get_plugin_info($component);

        if (is_null($pluginfo)) {
            return false;
        }

        // Give the pluginfo class a chance to execute some steps.
        $result = $pluginfo->uninstall($progress);
        if (!$result) {
            return false;
        }

        // Call the legacy core function to uninstall the plugin.
        ob_start();
        uninstall_plugin($pluginfo->type, $pluginfo->name);
        $progress->output(ob_get_clean());

        return true;
    }

    /**
     * Checks if there are some plugins with a known available update
     *
     * @return bool true if there is at least one available update
     */
    public function some_plugins_updatable() {
        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                if ($plugin->available_updates()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns list of available updates for the given component.
     *
     * This method should be considered as internal API and is supposed to be
     * called by {@link \core\plugininfo\base::available_updates()} only
     * to lazy load the data once they are first requested.
     *
     * @param string $component frankenstyle name of the plugin
     * @return null|array array of \core\update\info objects or null
     */
    public function load_available_updates_for_plugin($component) {
        global $CFG;

        $provider = \core\update\checker::instance();

        if (!$provider->enabled() or during_initial_install()) {
            return null;
        }

        if (isset($CFG->updateminmaturity)) {
            $minmaturity = $CFG->updateminmaturity;
        } else {
            // This can happen during the very first upgrade to 2.3.
            $minmaturity = MATURITY_STABLE;
        }

        return $provider->get_update_info($component, array('minmaturity' => $minmaturity));
    }

    /**
     * Returns a list of all available updates to be installed.
     *
     * This is used when "update all plugins" action is performed at the
     * administration UI screen.
     *
     * Returns array of remote info objects indexed by the plugin
     * component. If there are multiple updates available (typically a mix of
     * stable and non-stable ones), we pick the most mature most recent one.
     *
     * Plugins without explicit maturity are considered more mature than
     * release candidates but less mature than explicit stable (this should be
     * pretty rare case).
     *
     * @return array (string)component => (\core\update\remote_info)remoteinfo
     */
    public function available_updates() {

        $updates = array();

        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                $availableupdates = $plugin->available_updates();
                if (empty($availableupdates)) {
                    continue;
                }
                foreach ($availableupdates as $update) {
                    if (empty($updates[$plugin->component])) {
                        $updates[$plugin->component] = $update;
                        continue;
                    }
                    $maturitycurrent = $updates[$plugin->component]->maturity;
                    if (empty($maturitycurrent)) {
                        $maturitycurrent = MATURITY_STABLE - 25;
                    }
                    $maturityremote = $update->maturity;
                    if (empty($maturityremote)) {
                        $maturityremote = MATURITY_STABLE - 25;
                    }
                    if ($maturityremote < $maturitycurrent) {
                        continue;
                    }
                    if ($maturityremote > $maturitycurrent) {
                        $updates[$plugin->component] = $update;
                        continue;
                    }
                    if ($update->version > $updates[$plugin->component]->version) {
                        $updates[$plugin->component] = $update;
                        continue;
                    }
                }
            }
        }

        foreach ($updates as $component => $update) {
            $remoteinfo = $this->get_remote_plugin_info($component, $update->version, true);
            if (empty($remoteinfo) or empty($remoteinfo->version)) {
                unset($updates[$component]);
            } else {
                $updates[$component] = $remoteinfo;
            }
        }

        return $updates;
    }

    /**
     * Check to see if the given plugin folder can be removed by the web server process.
     *
     * @param string $component full frankenstyle component
     * @return bool
     */
    public function is_plugin_folder_removable($component) {

        $pluginfo = $this->get_plugin_info($component);

        if (is_null($pluginfo)) {
            return false;
        }

        // To be able to remove the plugin folder, its parent must be writable, too.
        if (!is_writable(dirname($pluginfo->rootdir))) {
            return false;
        }

        // Check that the folder and all its content is writable (thence removable).
        return $this->is_directory_removable($pluginfo->rootdir);
    }

    /**
     * Is it possible to create a new plugin directory for the given plugin type?
     *
     * @throws coding_exception for invalid plugin types or non-existing plugin type locations
     * @param string $plugintype
     * @return boolean
     */
    public function is_plugintype_writable($plugintype) {

        $plugintypepath = $this->get_plugintype_root($plugintype);

        if (is_null($plugintypepath)) {
            throw new coding_exception('Unknown plugin type: '.$plugintype);
        }

        if ($plugintypepath === false) {
            throw new coding_exception('Plugin type location does not exist: '.$plugintype);
        }

        return is_writable($plugintypepath);
    }

    /**
     * Returns the full path of the root of the given plugin type
     *
     * Null is returned if the plugin type is not known. False is returned if
     * the plugin type root is expected but not found. Otherwise, string is
     * returned.
     *
     * @param string $plugintype
     * @return string|bool|null
     */
    public function get_plugintype_root($plugintype) {

        $plugintypepath = null;
        foreach (core_component::get_plugin_types() as $type => $fullpath) {
            if ($type === $plugintype) {
                $plugintypepath = $fullpath;
                break;
            }
        }
        if (is_null($plugintypepath)) {
            return null;
        }
        if (!is_dir($plugintypepath)) {
            return false;
        }

        return $plugintypepath;
    }

    /**
     * Defines a list of all plugins that were originally shipped in the standard Moodle distribution,
     * but are not anymore and are deleted during upgrades.
     *
     * The main purpose of this list is to hide missing plugins during upgrade.
     *
     * @param string $type plugin type
     * @param string $name plugin name
     * @return bool
     */
    public static function is_deleted_standard_plugin($type, $name) {
        // Do not include plugins that were removed during upgrades to versions that are
        // not supported as source versions for upgrade any more. For example, at MOODLE_23_STABLE
        // branch, listed should be no plugins that were removed at 1.9.x - 2.1.x versions as
        // Moodle 2.3 supports upgrades from 2.2.x only.
        $plugins = array(
            'qformat' => array('blackboard', 'learnwise'),
            'auth' => array('radius', 'fc', 'nntp', 'pam', 'pop3', 'imap'),
            'block' => array('course_overview', 'messages'),
            'cachestore' => array('memcache'),
            'enrol' => array('authorize'),
            'report' => array('search'),
            'repository' => array('alfresco'),
            'tinymce' => array('dragmath'),
            'tool' => array('bloglevelupgrade', 'qeupgradehelper', 'timezoneimport', 'assignmentupgrade'),
            'theme' => array('bootstrapbase', 'clean', 'more', 'afterburner', 'anomaly', 'arialist', 'base',
                'binarius', 'boxxie', 'brick', 'canvas', 'formal_white', 'formfactor', 'fusion', 'leatherbound',
                'magazine', 'mymobile', 'nimble', 'nonzero', 'overlay', 'serenity', 'sky_high', 'splash',
                'standard', 'standardold'),
            'webservice' => array('amf'),
        );

        if (!isset($plugins[$type])) {
            return false;
        }
        return in_array($name, $plugins[$type]);
    }

    /**
     * Defines a white list of all plugins shipped in the standard Moodle distribution
     *
     * @param string $type
     * @return false|array array of standard plugins or false if the type is unknown
     */
    public static function standard_plugins_list($type) {

        $standard_plugins = array(

            'antivirus' => array(
                'clamav'
            ),

            'atto' => array(
                'accessibilitychecker', 'accessibilityhelper', 'align',
                'backcolor', 'bold', 'charmap', 'clear', 'collapse', 'emoticon',
                'equation', 'fontcolor', 'html', 'image', 'indent', 'italic',
                'link', 'managefiles', 'media', 'noautolink', 'orderedlist',
                'recordrtc', 'rtl', 'strike', 'subscript', 'superscript', 'table',
                'title', 'underline', 'undo', 'unorderedlist'
            ),

            'assignment' => array(
                'offline', 'online', 'upload', 'uploadsingle'
            ),

            'assignsubmission' => array(
                'comments', 'file', 'onlinetext'
            ),

            'assignfeedback' => array(
                'comments', 'file', 'offline', 'editpdf'
            ),

            'auth' => array(
                'cas', 'db', 'email', 'ldap', 'lti', 'manual', 'mnet',
                'nologin', 'none', 'oauth2', 'shibboleth', 'webservice'
            ),

            'availability' => array(
                'completion', 'date', 'grade', 'group', 'grouping', 'profile'
            ),

            'block' => array(
                'activity_modules', 'activity_results', 'admin_bookmarks', 'badges',
                'blog_menu', 'blog_recent', 'blog_tags', 'calendar_month',
                'calendar_upcoming', 'comments', 'community',
                'completionstatus', 'course_list', 'course_summary',
                'feedback', 'globalsearch', 'glossary_random', 'html',
                'login', 'lp', 'mentees', 'mnet_hosts', 'myoverview', 'myprofile',
                'navigation', 'news_items', 'online_users', 'participants',
                'private_files', 'quiz_results', 'recent_activity', 'recentlyaccesseditems',
                'recentlyaccessedcourses', 'rss_client', 'search_forums', 'section_links',
                'selfcompletion', 'settings', 'site_main_menu',
                'social_activities', 'starredcourses', 'tag_flickr', 'tag_youtube', 'tags', 'timeline'
            ),

            'booktool' => array(
                'exportimscp', 'importhtml', 'print'
            ),

            'cachelock' => array(
                'file'
            ),

            'cachestore' => array(
                'file', 'memcached', 'mongodb', 'session', 'static', 'apcu', 'redis'
            ),

            'calendartype' => array(
                'gregorian'
            ),

            'customfield' => array(
                'checkbox', 'date', 'select', 'text', 'textarea'
            ),

            'coursereport' => array(
                // Deprecated!
            ),

            'datafield' => array(
                'checkbox', 'date', 'file', 'latlong', 'menu', 'multimenu',
                'number', 'picture', 'radiobutton', 'text', 'textarea', 'url'
            ),

            'dataformat' => array(
                'html', 'csv', 'json', 'excel', 'ods', 'pdf',
            ),

            'datapreset' => array(
                'imagegallery'
            ),

            'fileconverter' => array(
                'unoconv', 'googledrive'
            ),

            'editor' => array(
                'atto', 'textarea', 'tinymce'
            ),

            'enrol' => array(
                'category', 'cohort', 'database', 'flatfile',
                'guest', 'imsenterprise', 'ldap', 'lti', 'manual', 'meta', 'mnet',
                'paypal', 'self'
            ),

            'filter' => array(
                'activitynames', 'algebra', 'censor', 'emailprotect',
                'emoticon', 'mathjaxloader', 'mediaplugin', 'multilang', 'tex', 'tidy',
                'urltolink', 'data', 'glossary'
            ),

            'format' => array(
                'singleactivity', 'social', 'topics', 'weeks'
            ),

            'gradeexport' => array(
                'ods', 'txt', 'xls', 'xml'
            ),

            'gradeimport' => array(
                'csv', 'direct', 'xml'
            ),

            'gradereport' => array(
                'grader', 'history', 'outcomes', 'overview', 'user', 'singleview'
            ),

            'gradingform' => array(
                'rubric', 'guide'
            ),

            'local' => array(
            ),

            'logstore' => array(
                'database', 'legacy', 'standard',
            ),

            'ltiservice' => array(
                'gradebookservices', 'memberships', 'profile', 'toolproxy', 'toolsettings'
            ),

            'mlbackend' => array(
                'php', 'python'
            ),

            'media' => array(
                'html5audio', 'html5video', 'swf', 'videojs', 'vimeo', 'youtube'
            ),

            'message' => array(
                'airnotifier', 'email', 'jabber', 'popup'
            ),

            'mnetservice' => array(
                'enrol'
            ),

            'mod' => array(
                'assign', 'assignment', 'book', 'chat', 'choice', 'data', 'feedback', 'folder',
                'forum', 'glossary', 'imscp', 'label', 'lesson', 'lti', 'page',
                'quiz', 'resource', 'scorm', 'survey', 'url', 'wiki', 'workshop'
            ),

            'plagiarism' => array(
            ),

            'portfolio' => array(
                'boxnet', 'download', 'flickr', 'googledocs', 'mahara', 'picasa'
            ),

            'profilefield' => array(
                'checkbox', 'datetime', 'menu', 'text', 'textarea'
            ),

            'qbehaviour' => array(
                'adaptive', 'adaptivenopenalty', 'deferredcbm',
                'deferredfeedback', 'immediatecbm', 'immediatefeedback',
                'informationitem', 'interactive', 'interactivecountback',
                'manualgraded', 'missing'
            ),

            'qformat' => array(
                'aiken', 'blackboard_six', 'examview', 'gift',
                'missingword', 'multianswer', 'webct',
                'xhtml', 'xml'
            ),

            'qtype' => array(
                'calculated', 'calculatedmulti', 'calculatedsimple',
                'ddimageortext', 'ddmarker', 'ddwtos', 'description',
                'essay', 'gapselect', 'match', 'missingtype', 'multianswer',
                'multichoice', 'numerical', 'random', 'randomsamatch',
                'shortanswer', 'truefalse'
            ),

            'quiz' => array(
                'grading', 'overview', 'responses', 'statistics'
            ),

            'quizaccess' => array(
                'delaybetweenattempts', 'ipaddress', 'numattempts', 'offlineattempts', 'openclosedate',
                'password', 'safebrowser', 'securewindow', 'timelimit'
            ),

            'report' => array(
                'backups', 'competency', 'completion', 'configlog', 'courseoverview', 'eventlist',
                'insights', 'log', 'loglive', 'outline', 'participation', 'progress', 'questioninstances',
                'security', 'stats', 'performance', 'usersessions'
            ),

            'repository' => array(
                'areafiles', 'boxnet', 'coursefiles', 'dropbox', 'equella', 'filesystem',
                'flickr', 'flickr_public', 'googledocs', 'local', 'merlot', 'nextcloud',
                'onedrive', 'picasa', 'recent', 'skydrive', 's3', 'upload', 'url', 'user', 'webdav',
                'wikimedia', 'youtube'
            ),

            'search' => array(
                'simpledb', 'solr'
            ),

            'scormreport' => array(
                'basic',
                'interactions',
                'graphs',
                'objectives'
            ),

            'tinymce' => array(
                'ctrlhelp', 'managefiles', 'moodleemoticon', 'moodleimage',
                'moodlemedia', 'moodlenolink', 'pdw', 'spellchecker', 'wrap'
            ),

            'theme' => array(
                'boost', 'classic'
            ),

            'tool' => array(
                'analytics', 'availabilityconditions', 'behat', 'capability', 'cohortroles', 'customlang',
                'dataprivacy', 'dbtransfer', 'filetypes', 'generator', 'health', 'httpsreplace', 'innodb', 'installaddon',
                'langimport', 'log', 'lp', 'lpimportcsv', 'lpmigrate', 'messageinbound', 'mobile', 'multilangupgrade',
                'monitor', 'oauth2', 'phpunit', 'policy', 'profiling', 'recyclebin', 'replace', 'spamcleaner', 'task',
                'templatelibrary', 'uploadcourse', 'uploaduser', 'unsuproles', 'usertours', 'xmldb'
            ),

            'webservice' => array(
                'rest', 'soap', 'xmlrpc'
            ),

            'workshopallocation' => array(
                'manual', 'random', 'scheduled'
            ),

            'workshopeval' => array(
                'best'
            ),

            'workshopform' => array(
                'accumulative', 'comments', 'numerrors', 'rubric'
            )
        );

        if (isset($standard_plugins[$type])) {
            return $standard_plugins[$type];
        } else {
            return false;
        }
    }

    /**
     * Remove the current plugin code from the dirroot.
     *
     * If removing the currently installed version (which happens during
     * updates), we archive the code so that the upgrade can be cancelled.
     *
     * To prevent accidental data-loss, we also archive the existing plugin
     * code if cancelling installation of it, so that the developer does not
     * loose the only version of their work-in-progress.
     *
     * @param \core\plugininfo\base $plugin
     */
    public function remove_plugin_folder(\core\plugininfo\base $plugin) {

        if (!$this->is_plugin_folder_removable($plugin->component)) {
            throw new moodle_exception('err_removing_unremovable_folder', 'core_plugin', '',
                array('plugin' => $plugin->component, 'rootdir' => $plugin->rootdir),
                'plugin root folder is not removable as expected');
        }

        if ($plugin->get_status() === self::PLUGIN_STATUS_UPTODATE or $plugin->get_status() === self::PLUGIN_STATUS_NEW) {
            $this->archive_plugin_version($plugin);
        }

        remove_dir($plugin->rootdir);
        clearstatcache();
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Can the installation of the new plugin be cancelled?
     *
     * Subplugins can be cancelled only via their parent plugin, not separately
     * (they are considered as implicit requirements if distributed together
     * with the main package).
     *
     * @param \core\plugininfo\base $plugin
     * @return bool
     */
    public function can_cancel_plugin_installation(\core\plugininfo\base $plugin) {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            return false;
        }

        if (empty($plugin) or $plugin->is_standard() or $plugin->is_subplugin()
                or !$this->is_plugin_folder_removable($plugin->component)) {
            return false;
        }

        if ($plugin->get_status() === self::PLUGIN_STATUS_NEW) {
            return true;
        }

        return false;
    }

    /**
     * Can the upgrade of the existing plugin be cancelled?
     *
     * Subplugins can be cancelled only via their parent plugin, not separately
     * (they are considered as implicit requirements if distributed together
     * with the main package).
     *
     * @param \core\plugininfo\base $plugin
     * @return bool
     */
    public function can_cancel_plugin_upgrade(\core\plugininfo\base $plugin) {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            // Cancelling the plugin upgrade is actually installation of the
            // previously archived version.
            return false;
        }

        if (empty($plugin) or $plugin->is_standard() or $plugin->is_subplugin()
                or !$this->is_plugin_folder_removable($plugin->component)) {
            return false;
        }

        if ($plugin->get_status() === self::PLUGIN_STATUS_UPGRADE) {
            if ($this->get_code_manager()->get_archived_plugin_version($plugin->component, $plugin->versiondb)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the plugin code directory if it is not installed yet.
     *
     * This is intended for the plugins check screen to give the admin a chance
     * to cancel the installation of just unzipped plugin before the database
     * upgrade happens.
     *
     * @param string $component
     */
    public function cancel_plugin_installation($component) {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            return false;
        }

        $plugin = $this->get_plugin_info($component);

        if ($this->can_cancel_plugin_installation($plugin)) {
            $this->remove_plugin_folder($plugin);
        }

        return false;
    }

    /**
     * Returns plugins, the installation of which can be cancelled.
     *
     * @return array [(string)component] => (\core\plugininfo\base)plugin
     */
    public function list_cancellable_installations() {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            return array();
        }

        $cancellable = array();
        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                if ($this->can_cancel_plugin_installation($plugin)) {
                    $cancellable[$plugin->component] = $plugin;
                }
            }
        }

        return $cancellable;
    }

    /**
     * Archive the current on-disk plugin code.
     *
     * @param \core\plugiinfo\base $plugin
     * @return bool
     */
    public function archive_plugin_version(\core\plugininfo\base $plugin) {
        return $this->get_code_manager()->archive_plugin_version($plugin->rootdir, $plugin->component, $plugin->versiondisk);
    }

    /**
     * Returns list of all archives that can be installed to cancel the plugin upgrade.
     *
     * @return array [(string)component] => {(string)->component, (string)->zipfilepath}
     */
    public function list_restorable_archives() {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            return false;
        }

        $codeman = $this->get_code_manager();
        $restorable = array();
        foreach ($this->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                if ($this->can_cancel_plugin_upgrade($plugin)) {
                    $restorable[$plugin->component] = (object)array(
                        'component' => $plugin->component,
                        'zipfilepath' => $codeman->get_archived_plugin_version($plugin->component, $plugin->versiondb)
                    );
                }
            }
        }

        return $restorable;
    }

    /**
     * Reorders plugin types into a sequence to be displayed
     *
     * For technical reasons, plugin types returned by {@link core_component::get_plugin_types()} are
     * in a certain order that does not need to fit the expected order for the display.
     * Particularly, activity modules should be displayed first as they represent the
     * real heart of Moodle. They should be followed by other plugin types that are
     * used to build the courses (as that is what one expects from LMS). After that,
     * other supportive plugin types follow.
     *
     * @param array $types associative array
     * @return array same array with altered order of items
     */
    protected function reorder_plugin_types(array $types) {
        $fix = array('mod' => $types['mod']);
        foreach (core_component::get_plugin_list('mod') as $plugin => $fulldir) {
            if (!$subtypes = core_component::get_subplugins('mod_'.$plugin)) {
                continue;
            }
            foreach ($subtypes as $subtype => $ignored) {
                $fix[$subtype] = $types[$subtype];
            }
        }

        $fix['mod']        = $types['mod'];
        $fix['block']      = $types['block'];
        $fix['qtype']      = $types['qtype'];
        $fix['qbehaviour'] = $types['qbehaviour'];
        $fix['qformat']    = $types['qformat'];
        $fix['filter']     = $types['filter'];

        $fix['editor']     = $types['editor'];
        foreach (core_component::get_plugin_list('editor') as $plugin => $fulldir) {
            if (!$subtypes = core_component::get_subplugins('editor_'.$plugin)) {
                continue;
            }
            foreach ($subtypes as $subtype => $ignored) {
                $fix[$subtype] = $types[$subtype];
            }
        }

        $fix['enrol'] = $types['enrol'];
        $fix['auth']  = $types['auth'];
        $fix['tool']  = $types['tool'];
        foreach (core_component::get_plugin_list('tool') as $plugin => $fulldir) {
            if (!$subtypes = core_component::get_subplugins('tool_'.$plugin)) {
                continue;
            }
            foreach ($subtypes as $subtype => $ignored) {
                $fix[$subtype] = $types[$subtype];
            }
        }

        foreach ($types as $type => $path) {
            if (!isset($fix[$type])) {
                $fix[$type] = $path;
            }
        }
        return $fix;
    }

    /**
     * Check if the given directory can be removed by the web server process.
     *
     * This recursively checks that the given directory and all its contents
     * it writable.
     *
     * @param string $fullpath
     * @return boolean
     */
    public function is_directory_removable($fullpath) {

        if (!is_writable($fullpath)) {
            return false;
        }

        if (is_dir($fullpath)) {
            $handle = opendir($fullpath);
        } else {
            return false;
        }

        $result = true;

        while ($filename = readdir($handle)) {

            if ($filename === '.' or $filename === '..') {
                continue;
            }

            $subfilepath = $fullpath.'/'.$filename;

            if (is_dir($subfilepath)) {
                $result = $result && $this->is_directory_removable($subfilepath);

            } else {
                $result = $result && is_writable($subfilepath);
            }
        }

        closedir($handle);

        return $result;
    }

    /**
     * Helper method that implements common uninstall prerequisites
     *
     * @param \core\plugininfo\base $pluginfo
     * @return bool
     */
    protected function common_uninstall_check(\core\plugininfo\base $pluginfo) {

        if (!$pluginfo->is_uninstall_allowed()) {
            // The plugin's plugininfo class declares it should not be uninstalled.
            return false;
        }

        if ($pluginfo->get_status() === static::PLUGIN_STATUS_NEW) {
            // The plugin is not installed. It should be either installed or removed from the disk.
            // Relying on this temporary state may be tricky.
            return false;
        }

        if (method_exists($pluginfo, 'get_uninstall_url') and is_null($pluginfo->get_uninstall_url())) {
            // Backwards compatibility.
            debugging('\core\plugininfo\base subclasses should use is_uninstall_allowed() instead of returning null in get_uninstall_url()',
                DEBUG_DEVELOPER);
            return false;
        }

        return true;
    }

    /**
     * Returns a code_manager instance to be used for the plugins code operations.
     *
     * @return \core\update\code_manager
     */
    protected function get_code_manager() {

        if ($this->codemanager === null) {
            $this->codemanager = new \core\update\code_manager();
        }

        return $this->codemanager;
    }

    /**
     * Returns a client for https://download.moodle.org/api/
     *
     * @return \core\update\api
     */
    protected function get_update_api_client() {

        if ($this->updateapiclient === null) {
            $this->updateapiclient = \core\update\api::client();
        }

        return $this->updateapiclient;
    }
}
