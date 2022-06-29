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
 * Defines classes used for plugin info.
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

use core_component, core_plugin_manager, moodle_url, coding_exception;

defined('MOODLE_INTERNAL') || die();


/**
 * Base class providing access to the information about a plugin
 *
 * @property-read string component the component name, type_name
 */
abstract class base {

    /** @var string the plugintype name, eg. mod, auth or workshopform */
    public $type;
    /** @var string full path to the location of all the plugins of this type */
    public $typerootdir;
    /** @var string the plugin name, eg. assignment, ldap */
    public $name;
    /** @var string the localized plugin name */
    public $displayname;
    /** @var string the plugin source, one of core_plugin_manager::PLUGIN_SOURCE_xxx constants */
    public $source;
    /** @var string fullpath to the location of this plugin */
    public $rootdir;
    /** @var int|string the version of the plugin's source code */
    public $versiondisk;
    /** @var int|string the version of the installed plugin */
    public $versiondb;
    /** @var int|float|string required version of Moodle core  */
    public $versionrequires;
    /** @var array explicitly supported branches of Moodle core  */
    public $pluginsupported;
    /** @var int first incompatible branch of Moodle core  */
    public $pluginincompatible;
    /** @var mixed human-readable release information */
    public $release;
    /** @var array other plugins that this one depends on, lazy-loaded by {@link get_other_required_plugins()} */
    public $dependencies;
    /** @var int number of instances of the plugin - not supported yet */
    public $instances;
    /** @var int order of the plugin among other plugins of the same type - not supported yet */
    public $sortorder;
    /** @var core_plugin_manager the plugin manager this plugin info is part of */
    public $pluginman;

    /** @var array|null array of {@link \core\update\info} for this plugin */
    protected $availableupdates;

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        return null;
    }

    /**
     * Enable or disable a plugin.
     * When possible, the change will be stored into the config_log table, to let admins check when/who has modified it.
     *
     * @param string $pluginname The plugin name to enable/disable.
     * @param int $enabled Whether the pluginname should be enabled (1) or not (0). This is an integer because some plugins, such
     * as filters or repositories, might support more statuses than just enabled/disabled.
     *
     * @return bool Whether $pluginname has been updated or not.
     */
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        return false;
    }

    /**
     * Returns current status for a pluginname.
     *
     * @param string $pluginname The plugin name to check.
     * @return int The current status (enabled, disabled...) of $pluginname.
     */
    public static function get_enabled_plugin(string $pluginname): int {
        $enabledplugins = static::get_enabled_plugins();
        $value = $enabledplugins && array_key_exists($pluginname, $enabledplugins);
        return (int) $value;
    }

    /**
     * Gathers and returns the information about all plugins of the given type,
     * either on disk or previously installed.
     *
     * This is supposed to be used exclusively by the plugin manager when it is
     * populating its tree of plugins.
     *
     * @param string $type the name of the plugintype, eg. mod, auth or workshopform
     * @param string $typerootdir full path to the location of the plugin dir
     * @param string $typeclass the name of the actually called class
     * @param core_plugin_manager $pluginman the plugin manager calling this method
     * @return array of plugintype classes, indexed by the plugin name
     */
    public static function get_plugins($type, $typerootdir, $typeclass, $pluginman) {
        // Get the information about plugins at the disk.
        $plugins = core_component::get_plugin_list($type);
        $return = array();
        foreach ($plugins as $pluginname => $pluginrootdir) {
            $return[$pluginname] = self::make_plugin_instance($type, $typerootdir,
                $pluginname, $pluginrootdir, $typeclass, $pluginman);
        }

        // Fetch missing incorrectly uninstalled plugins.
        $plugins = $pluginman->get_installed_plugins($type);

        foreach ($plugins as $name => $version) {
            if (isset($return[$name])) {
                continue;
            }
            $plugin              = new $typeclass();
            $plugin->type        = $type;
            $plugin->typerootdir = $typerootdir;
            $plugin->name        = $name;
            $plugin->rootdir     = null;
            $plugin->displayname = $name;
            $plugin->versiondb   = $version;
            $plugin->pluginman   = $pluginman;
            $plugin->init_is_standard();

            $return[$name] = $plugin;
        }

        return $return;
    }

    /**
     * Makes a new instance of the plugininfo class
     *
     * @param string $type the plugin type, eg. 'mod'
     * @param string $typerootdir full path to the location of all the plugins of this type
     * @param string $name the plugin name, eg. 'workshop'
     * @param string $namerootdir full path to the location of the plugin
     * @param string $typeclass the name of class that holds the info about the plugin
     * @param core_plugin_manager $pluginman the plugin manager of the new instance
     * @return base the instance of $typeclass
     */
    protected static function make_plugin_instance($type, $typerootdir, $name, $namerootdir, $typeclass, $pluginman) {
        $plugin              = new $typeclass();
        $plugin->type        = $type;
        $plugin->typerootdir = $typerootdir;
        $plugin->name        = $name;
        $plugin->rootdir     = $namerootdir;
        $plugin->pluginman   = $pluginman;

        $plugin->init_display_name();
        $plugin->load_disk_version();
        $plugin->load_db_version();
        $plugin->init_is_standard();

        return $plugin;
    }

    /**
     * Is this plugin already installed and updated?
     * @return bool true if plugin installed and upgraded.
     */
    public function is_installed_and_upgraded() {
        if (!$this->rootdir) {
            return false;
        }
        if ($this->versiondb === null and $this->versiondisk === null) {
            // There is no version.php or version info inside it.
            return false;
        }

        return ((float)$this->versiondb === (float)$this->versiondisk);
    }

    /**
     * Sets {@link $displayname} property to a localized name of the plugin
     */
    public function init_display_name() {
        if (!get_string_manager()->string_exists('pluginname', $this->component)) {
            $this->displayname = '[pluginname,' . $this->component . ']';
        } else {
            $this->displayname = get_string('pluginname', $this->component);
        }
    }

    /**
     * Magic method getter, redirects to read only values.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        switch ($name) {
            case 'component': return $this->type . '_' . $this->name;

            default:
                debugging('Invalid plugin property accessed! '.$name);
                return null;
        }
    }

    /**
     * Return the full path name of a file within the plugin.
     *
     * No check is made to see if the file exists.
     *
     * @param string $relativepath e.g. 'version.php'.
     * @return string e.g. $CFG->dirroot . '/mod/quiz/version.php'.
     */
    public function full_path($relativepath) {
        if (empty($this->rootdir)) {
            return '';
        }
        return $this->rootdir . '/' . $relativepath;
    }

    /**
     * Sets {@link $versiondisk} property to a numerical value representing the
     * version of the plugin's source code.
     *
     * If the value is null after calling this method, either the plugin
     * does not use versioning (typically does not have any database
     * data) or is missing from disk.
     */
    public function load_disk_version() {
        $versions = $this->pluginman->get_present_plugins($this->type);

        $this->versiondisk = null;
        $this->versionrequires = null;
        $this->pluginsupported = null;
        $this->pluginincompatible = null;
        $this->dependencies = array();

        if (!isset($versions[$this->name])) {
            return;
        }

        $plugin = $versions[$this->name];

        if (isset($plugin->version)) {
            $this->versiondisk = $plugin->version;
        }
        if (isset($plugin->requires)) {
            $this->versionrequires = $plugin->requires;
        }
        if (isset($plugin->release)) {
            $this->release = $plugin->release;
        }
        if (isset($plugin->dependencies)) {
            $this->dependencies = $plugin->dependencies;
        }

        // Check that supports and incompatible are wellformed, exception otherwise.
        if (isset($plugin->supported)) {
            // Checks for structure of supported.
            $isint = (is_int($plugin->supported[0]) && is_int($plugin->supported[1]));
            $isrange = ($plugin->supported[0] <= $plugin->supported[1] && count($plugin->supported) == 2);

            if (is_array($plugin->supported) && $isint && $isrange) {
                $this->pluginsupported = $plugin->supported;
            } else {
                throw new coding_exception('Incorrect syntax in plugin supported declaration in '."$this->name");
            }
        }

        if (isset($plugin->incompatible) && $plugin->incompatible !== null) {
            if ((ctype_digit($plugin->incompatible) || is_int($plugin->incompatible)) && (int) $plugin->incompatible > 0) {
                $this->pluginincompatible = intval($plugin->incompatible);
            } else {
                throw new coding_exception('Incorrect syntax in plugin incompatible declaration in '."$this->name");
            }
        }

    }

    /**
     * Get the list of other plugins that this plugin requires to be installed.
     *
     * @return array with keys the frankenstyle plugin name, and values either
     *      a version string (like '2011101700') or the constant ANY_VERSION.
     */
    public function get_other_required_plugins() {
        if (is_null($this->dependencies)) {
            $this->load_disk_version();
        }
        return $this->dependencies;
    }

    /**
     * Is this is a subplugin?
     *
     * @return boolean
     */
    public function is_subplugin() {
        return ($this->get_parent_plugin() !== false);
    }

    /**
     * If I am a subplugin, return the name of my parent plugin.
     *
     * @return string|bool false if not a subplugin, name of the parent otherwise
     */
    public function get_parent_plugin() {
        return $this->pluginman->get_parent_of_subplugin($this->type);
    }

    /**
     * Sets {@link $versiondb} property to a numerical value representing the
     * currently installed version of the plugin.
     *
     * If the value is null after calling this method, either the plugin
     * does not use versioning (typically does not have any database
     * data) or has not been installed yet.
     */
    public function load_db_version() {
        $versions = $this->pluginman->get_installed_plugins($this->type);

        if (isset($versions[$this->name])) {
            $this->versiondb = $versions[$this->name];
        } else {
            $this->versiondb = null;
        }
    }

    /**
     * Sets {@link $source} property to one of core_plugin_manager::PLUGIN_SOURCE_xxx
     * constants.
     *
     * If the property's value is null after calling this method, then
     * the type of the plugin has not been recognized and you should throw
     * an exception.
     */
    public function init_is_standard() {

        $pluginman = $this->pluginman;
        $standard = $pluginman::standard_plugins_list($this->type);

        if ($standard !== false) {
            $standard = array_flip($standard);
            if (isset($standard[$this->name])) {
                $this->source = core_plugin_manager::PLUGIN_SOURCE_STANDARD;
            } else if (!is_null($this->versiondb) and is_null($this->versiondisk)
                and $pluginman::is_deleted_standard_plugin($this->type, $this->name)) {
                $this->source = core_plugin_manager::PLUGIN_SOURCE_STANDARD; // To be deleted.
            } else {
                $this->source = core_plugin_manager::PLUGIN_SOURCE_EXTENSION;
            }
        }
    }

    /**
     * Returns true if the plugin is shipped with the official distribution
     * of the current Moodle version, false otherwise.
     *
     * @return bool
     */
    public function is_standard() {
        return $this->source === core_plugin_manager::PLUGIN_SOURCE_STANDARD;
    }

    /**
     * Returns true if the the given Moodle version is enough to run this plugin
     *
     * @param string|int|double $moodleversion
     * @return bool
     */
    public function is_core_dependency_satisfied($moodleversion) {

        if (empty($this->versionrequires)) {
            return true;

        } else {
            return (double)$this->versionrequires <= (double)$moodleversion;
        }
    }

    /**
     * Returns true if the the given moodle branch is not stated incompatible with the plugin
     *
     * @param int $branch the moodle branch number
     * @return bool true if not incompatible with moodle branch
     */
    public function is_core_compatible_satisfied(int $branch) : bool {
        if (!empty($this->pluginincompatible) && ($branch >= $this->pluginincompatible)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the status of the plugin
     *
     * @return string one of core_plugin_manager::PLUGIN_STATUS_xxx constants
     */
    public function get_status() {

        $pluginman = $this->pluginman;

        if (is_null($this->versiondb) and is_null($this->versiondisk)) {
            return core_plugin_manager::PLUGIN_STATUS_NODB;

        } else if (is_null($this->versiondb) and !is_null($this->versiondisk)) {
            return core_plugin_manager::PLUGIN_STATUS_NEW;

        } else if (!is_null($this->versiondb) and is_null($this->versiondisk)) {
            if ($pluginman::is_deleted_standard_plugin($this->type, $this->name)) {
                return core_plugin_manager::PLUGIN_STATUS_DELETE;
            } else {
                return core_plugin_manager::PLUGIN_STATUS_MISSING;
            }

        } else if ((float)$this->versiondb === (float)$this->versiondisk) {
            // Note: the float comparison should work fine here
            //       because there are no arithmetic operations with the numbers.
            return core_plugin_manager::PLUGIN_STATUS_UPTODATE;

        } else if ($this->versiondb < $this->versiondisk) {
            return core_plugin_manager::PLUGIN_STATUS_UPGRADE;

        } else if ($this->versiondb > $this->versiondisk) {
            return core_plugin_manager::PLUGIN_STATUS_DOWNGRADE;

        } else {
            // $version = pi(); and similar funny jokes - hopefully Donald E. Knuth will never contribute to Moodle ;-)
            throw new coding_exception('Unable to determine plugin state, check the plugin versions');
        }
    }

    /**
     * Returns the information about plugin availability
     *
     * True means that the plugin is enabled. False means that the plugin is
     * disabled. Null means that the information is not available, or the
     * plugin does not support configurable availability or the availability
     * can not be changed.
     *
     * @return null|bool
     */
    public function is_enabled() {
        if (!$this->rootdir) {
            // Plugin missing.
            return false;
        }

        $enabled = $this->pluginman->get_enabled_plugins($this->type);

        if (!is_array($enabled)) {
            return null;
        }

        return isset($enabled[$this->name]);
    }

    /**
     * If there are updates for this plugin available, returns them.
     *
     * Returns array of {@link \core\update\info} objects, if some update
     * is available. Returns null if there is no update available or if the update
     * availability is unknown.
     *
     * Populates the property {@link $availableupdates} on first call (lazy
     * loading).
     *
     * @return array|null
     */
    public function available_updates() {

        if ($this->availableupdates === null) {
            // Lazy load the information about available updates.
            $this->availableupdates = $this->pluginman->load_available_updates_for_plugin($this->component);
        }

        if (empty($this->availableupdates) or !is_array($this->availableupdates)) {
            $this->availableupdates = array();
            return null;
        }

        $updates = array();

        foreach ($this->availableupdates as $availableupdate) {
            if ($availableupdate->version > $this->versiondisk) {
                $updates[] = $availableupdate;
            }
        }

        if (empty($updates)) {
            return null;
        }

        return $updates;
    }

    /**
     * Returns the node name used in admin settings menu for this plugin settings (if applicable)
     *
     * @return null|string node name or null if plugin does not create settings node (default)
     */
    public function get_settings_section_name() {
        return null;
    }

    /**
     * Returns the URL of the plugin settings screen
     *
     * Null value means that the plugin either does not have the settings screen
     * or its location is not available via this library.
     *
     * @return null|moodle_url
     */
    public function get_settings_url(): ?moodle_url {
        $section = $this->get_settings_section_name();
        if ($section === null) {
            return null;
        }

        $settings = admin_get_root()->locate($section);
        if ($settings && $settings instanceof \core_admin\local\settings\linkable_settings_page) {
            return $settings->get_settings_page_url();
        }

        return null;
    }

    /**
     * Loads plugin settings to the settings tree
     *
     * This function usually includes settings.php file in plugins folder.
     * Alternatively it can create a link to some settings page (instance of admin_externalpage)
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
    }

    /**
     * Should there be a way to uninstall the plugin via the administration UI.
     *
     * By default uninstallation is not allowed, plugin developers must enable it explicitly!
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        return false;
    }

    /**
     * Optional extra warning before uninstallation, for example number of uses in courses.
     *
     * @return string
     */
    public function get_uninstall_extra_warning() {
        return '';
    }

    /**
     * Pre-uninstall hook.
     *
     * This is intended for disabling of plugin, some DB table purging, etc.
     *
     * NOTE: to be called from uninstall_plugin() only.
     * @private
     */
    public function uninstall_cleanup() {
        // Override when extending class,
        // do not forget to call parent::pre_uninstall_cleanup() at the end.
    }

    /**
     * Returns relative directory of the plugin with heading '/'
     *
     * @return string
     */
    public function get_dir() {
        global $CFG;

        return substr($this->rootdir, strlen($CFG->dirroot));
    }

    /**
     * Hook method to implement certain steps when uninstalling the plugin.
     *
     * This hook is called by {@link core_plugin_manager::uninstall_plugin()} so
     * it is basically usable only for those plugin types that use the default
     * uninstall tool provided by {@link self::get_default_uninstall_url()}.
     *
     * @param \progress_trace $progress traces the process
     * @return bool true on success, false on failure
     */
    public function uninstall(\progress_trace $progress) {
        return true;
    }

    /**
     * Where should we return after plugin of this type is uninstalled?
     * @param string $return
     * @return moodle_url
     */
    public function get_return_url_after_uninstall($return) {
        if ($return === 'manage') {
            if ($url = $this->get_manage_url()) {
                return $url;
            }
        }
        return new moodle_url('/admin/plugins.php#plugin_type_cell_'.$this->type);
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return null;
    }

    /**
     * Returns URL to a script that handles common plugin uninstall procedure.
     *
     * This URL is intended for all plugin uninstallations.
     *
     * @param string $return either 'overview' or 'manage'
     * @return moodle_url
     */
    public final function get_default_uninstall_url($return = 'overview') {
        return new moodle_url('/admin/plugins.php', array(
            'uninstall' => $this->component,
            'confirm' => 0,
            'return' => $return,
        ));
    }
}
