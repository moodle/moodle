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
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for media plugins
 *
 * @package    core
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media extends base {

    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * Get the name for the settings section.
     *
     * @return string
     */
    public function get_settings_section_name() {
        return 'mediasetting' . $this->name;
    }

    /**
     * Load the global settings for a particular availability plugin (if there are any)
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php
        $availability = $this; // Also to be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = null;
        if (file_exists($this->full_path('settings.php'))) {
            $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return \moodle_url
     */
    public static function get_manage_url() {
        return new \moodle_url('/admin/settings.php', array('section' => 'managemediaplayers'));
    }

    public static function get_enabled_plugins() {
        global $CFG;

        $order = (!empty($CFG->media_plugins_sortorder)) ? explode(',', $CFG->media_plugins_sortorder) : [];
        if ($order) {
            $plugins = \core_plugin_manager::instance()->get_installed_plugins('media');
            $order = array_intersect($order, array_keys($plugins));
        }
        return array_combine($order, $order);
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $CFG;

        $haschanged = false;
        $plugins = [];
        if (!empty($CFG->media_plugins_sortorder)) {
            $plugins = explode(',', $CFG->media_plugins_sortorder);
        }
        // Only set visibility if it's different from the current value.
        if ($enabled && !in_array($pluginname, $plugins)) {
            // Enable media plugin.

            $pluginsbytype = \core_plugin_manager::instance()->get_plugins_of_type('media');
            if (!array_key_exists($pluginname, $pluginsbytype)) {
                // Can not be enabled.
                return false;
            }

            $rank = $pluginsbytype[$pluginname]->get_rank();
            $position = 0;
            // Insert before the first enabled plugin which default rank is smaller than the default rank of this one.
            foreach ($plugins as $playername) {
                if (($player = $pluginsbytype[$playername]) && ($rank > $player->get_rank())) {
                    break;
                }
                $position++;
            }
            array_splice($plugins, $position, 0, [$pluginname]);
            $haschanged = true;
        } else if (!$enabled && in_array($pluginname, $plugins)) {
            // Disable media plugin.
            $key = array_search($pluginname, $plugins);
            unset($plugins[$key]);
            $haschanged = true;
        }

        if ($haschanged) {
            add_to_config_log('media_plugins_sortorder', !$enabled, $enabled, $pluginname);
            self::set_enabled_plugins($plugins);
        }

        return $haschanged;
    }

    /**
     * Sets the current plugin as enabled or disabled
     * When enabling tries to guess the sortorder based on default rank returned by the plugin.
     * @param bool $newstate
     */
    public function set_enabled($newstate = true) {
        self::enable_plugin($this->name, $newstate);
    }

    /**
     * Set the list of enabled media players in the specified sort order
     * To be used when changing settings or in unit tests
     * @param string|array $list list of plugin names without frankenstyle prefix - comma-separated string or an array
     */
    public static function set_enabled_plugins($list) {
        if (empty($list)) {
            $list = [];
        } else if (!is_array($list)) {
            $list = explode(',', $list);
        }
        if ($list) {
            $plugins = \core_plugin_manager::instance()->get_installed_plugins('media');
            $list = array_intersect($list, array_keys($plugins));
        }
        set_config('media_plugins_sortorder', join(',', $list));
        \core_plugin_manager::reset_caches();
        \core_media_manager::reset_caches();
    }

    /**
     * Returns the default rank of this plugin for default sort order
     * @return int
     */
    public function get_rank() {
        $classname = '\media_'.$this->name.'_plugin';
        if (class_exists($classname)) {
            $object = new $classname();
            return $object->get_rank();
        }
        return 0;
    }

    /**
     * Returns human-readable string of supported file/link types for the "Manage media players" page
     * @param array $extensions
     * @return string
     */
    public function supports(&$extensions) {
        $classname = '\media_'.$this->name.'_plugin';
        if (class_exists($classname)) {
            $object = new $classname();
            $result = $object->supports($extensions);
            foreach ($object->get_supported_extensions() as $ext) {
                $extensions[$ext] = $ext;
            }
            return $result;
        }
        return '';
    }
}
