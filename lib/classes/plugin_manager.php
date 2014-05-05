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

    /** @var core_plugin_manager holds the singleton instance */
    protected static $singletoninstance;
    /** @var array of raw plugins information */
    protected $pluginsinfo = null;
    /** @var array of raw subplugins information */
    protected $subpluginsinfo = null;
    /** @var array list of installed plugins $name=>$version */
    protected $installedplugins = null;
    /** @var array list of all enabled plugins $name=>$name */
    protected $enabledplugins = null;
    /** @var array list of all enabled plugins $name=>$diskversion */
    protected $presentplugins = null;
    /** @var array reordered list of plugin types */
    protected $plugintypes = null;

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
        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    /**
     * Reset all caches.
     * @param bool $phpunitreset
     */
    public static function reset_caches($phpunitreset = false) {
        if ($phpunitreset) {
            self::$singletoninstance = null;
        } else {
            if (self::$singletoninstance) {
                self::$singletoninstance->pluginsinfo = null;
                self::$singletoninstance->subpluginsinfo = null;
                self::$singletoninstance->installedplugins = null;
                self::$singletoninstance->enabledplugins = null;
                self::$singletoninstance->presentplugins = null;
                self::$singletoninstance->plugintypes = null;
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
                throw coding_exception('core_plugin_manager->get_plugin_types() does not support relative paths.');
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

        // TODO: Delete this block once Moodle 2.6 or later becomes minimum required version to upgrade.
        if ($CFG->version < 2013092001.02) {
            // We did not upgrade the database yet.
            $modules = $DB->get_records('modules', array(), 'name ASC', 'id, name, version');
            foreach ($modules as $module) {
                $this->installedplugins['mod'][$module->name] = $module->version;
            }
            $blocks = $DB->get_records('block', array(), 'name ASC', 'id, name, version');
            foreach ($blocks as $block) {
                $this->installedplugins['block'][$block->name] = $block->version;
            }
        }

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
            $plugininfoclass = self::resolve_plugininfo_class($plugintype);
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
                $plugin = new stdClass();
                $plugin->version = null;
                $module = $plugin;
                @include($fullplug.'/version.php');
                $this->presentplugins[$type][$plug] = $plugin;
            }
        }

        $cache->set('present', $this->presentplugins);
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
            $plugintypeclass = self::resolve_plugininfo_class($type);
            $this->pluginsinfo[$type] = $plugintypeclass::get_plugins($type, null, $plugintypeclass);
            return $this->pluginsinfo[$type];
        }

        /** @var \core\plugininfo\base $plugintypeclass */
        $plugintypeclass = self::resolve_plugininfo_class($type);
        $plugins = $plugintypeclass::get_plugins($type, $types[$type], $plugintypeclass);
        $this->pluginsinfo[$type] = $plugins;

        if (empty($CFG->disableupdatenotifications) and !during_initial_install()) {
            // Append the information about available updates provided by {@link \core\update\checker()}.
            $provider = \core\update\checker::instance();
            foreach ($plugins as $plugininfoholder) {
                $plugininfoholder->check_available_updates($provider);
            }
        }

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
     * @see \core\update\deployer::plugin_external_source()
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
            'qformat' => array('blackboard'),
            'enrol' => array('authorize'),
            'tool' => array('bloglevelupgrade', 'qeupgradehelper'),
            'theme' => array('mymobile', 'afterburner', 'anomaly', 'arialist', 'binarius', 'boxxie', 'brick', 'formal_white',
                'formfactor', 'fusion', 'leatherbound', 'magazine', 'nimble', 'nonzero', 'overlay', 'serenity', 'sky_high',
                'splash', 'standard', 'standardold'),
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

            'atto' => array(
                'accessibilitychecker', 'accessibilityhelper', 'align',
                'backcolor', 'bold', 'charmap', 'clear', 'collapse', 'emoticon',
                'equation', 'fontcolor', 'html', 'image', 'indent', 'italic',
                'link', 'managefiles', 'media', 'noautolink', 'orderedlist',
                'rtl', 'strike', 'subscript', 'superscript', 'table', 'title',
                'underline', 'undo', 'unorderedlist'
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
                'cas', 'db', 'email', 'fc', 'imap', 'ldap', 'manual', 'mnet',
                'nntp', 'nologin', 'none', 'pam', 'pop3', 'radius',
                'shibboleth', 'webservice'
            ),

            'availability' => array(
                'completion', 'date', 'grade', 'group', 'grouping', 'profile'
            ),

            'block' => array(
                'activity_modules', 'admin_bookmarks', 'badges', 'blog_menu',
                'blog_recent', 'blog_tags', 'calendar_month',
                'calendar_upcoming', 'comments', 'community',
                'completionstatus', 'course_list', 'course_overview',
                'course_summary', 'feedback', 'glossary_random', 'html',
                'login', 'mentees', 'messages', 'mnet_hosts', 'myprofile',
                'navigation', 'news_items', 'online_users', 'participants',
                'private_files', 'quiz_results', 'recent_activity',
                'rss_client', 'search_forums', 'section_links',
                'selfcompletion', 'settings', 'site_main_menu',
                'social_activities', 'tag_flickr', 'tag_youtube', 'tags'
            ),

            'booktool' => array(
                'exportimscp', 'importhtml', 'print'
            ),

            'cachelock' => array(
                'file'
            ),

            'cachestore' => array(
                'file', 'memcache', 'memcached', 'mongodb', 'session', 'static'
            ),

            'calendartype' => array(
                'gregorian'
            ),

            'coursereport' => array(
                // Deprecated!
            ),

            'datafield' => array(
                'checkbox', 'date', 'file', 'latlong', 'menu', 'multimenu',
                'number', 'picture', 'radiobutton', 'text', 'textarea', 'url'
            ),

            'datapreset' => array(
                'imagegallery'
            ),

            'editor' => array(
                'atto', 'textarea', 'tinymce'
            ),

            'enrol' => array(
                'category', 'cohort', 'database', 'flatfile',
                'guest', 'imsenterprise', 'ldap', 'manual', 'meta', 'mnet',
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
                'csv', 'xml'
            ),

            'gradereport' => array(
                'grader', 'outcomes', 'overview', 'user'
            ),

            'gradingform' => array(
                'rubric', 'guide'
            ),

            'local' => array(
            ),

            'logstore' => array(
                'database', 'legacy', 'standard',
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
                'learnwise', 'missingword', 'multianswer', 'webct',
                'xhtml', 'xml'
            ),

            'qtype' => array(
                'calculated', 'calculatedmulti', 'calculatedsimple',
                'description', 'essay', 'match', 'missingtype', 'multianswer',
                'multichoice', 'numerical', 'random', 'randomsamatch',
                'shortanswer', 'truefalse'
            ),

            'quiz' => array(
                'grading', 'overview', 'responses', 'statistics'
            ),

            'quizaccess' => array(
                'delaybetweenattempts', 'ipaddress', 'numattempts', 'openclosedate',
                'password', 'safebrowser', 'securewindow', 'timelimit'
            ),

            'report' => array(
                'backups', 'completion', 'configlog', 'courseoverview', 'eventlist',
                'log', 'loglive', 'outline', 'participation', 'progress', 'questioninstances', 'security', 'stats', 'performance'
            ),

            'repository' => array(
                'alfresco', 'areafiles', 'boxnet', 'coursefiles', 'dropbox', 'equella', 'filesystem',
                'flickr', 'flickr_public', 'googledocs', 'local', 'merlot',
                'picasa', 'recent', 'skydrive', 's3', 'upload', 'url', 'user', 'webdav',
                'wikimedia', 'youtube'
            ),

            'scormreport' => array(
                'basic',
                'interactions',
                'graphs',
                'objectives'
            ),

            'tinymce' => array(
                'ctrlhelp', 'dragmath', 'managefiles', 'moodleemoticon', 'moodleimage',
                'moodlemedia', 'moodlenolink', 'pdw', 'spellchecker', 'wrap'
            ),

            'theme' => array(
                'base', 'bootstrapbase', 'canvas', 'clean', 'more'
            ),

            'tool' => array(
                'assignmentupgrade', 'availabilityconditions', 'behat', 'capability', 'customlang',
                'dbtransfer', 'generator', 'health', 'innodb', 'installaddon',
                'langimport', 'log', 'multilangupgrade', 'phpunit', 'profiling',
                'replace', 'spamcleaner', 'task', 'timezoneimport',
                'unittest', 'uploadcourse', 'uploaduser', 'unsuproles', 'xmldb'
            ),

            'webservice' => array(
                'amf', 'rest', 'soap', 'xmlrpc'
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
    protected function is_directory_removable($fullpath) {

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

        if ($pluginfo->get_status() === self::PLUGIN_STATUS_NEW) {
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
}
