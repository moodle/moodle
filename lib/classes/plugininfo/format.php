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

use moodle_url, part_of_admin_tree, admin_settingpage, core_plugin_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for course formats
 */
class format extends base {
    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $DB;

        $plugins = core_plugin_manager::instance()->get_installed_plugins('format');
        if (!$plugins) {
            return array();
        }
        $installed = array();
        foreach ($plugins as $plugin => $version) {
            $installed[] = 'format_'.$plugin;
        }

        list($installed, $params) = $DB->get_in_or_equal($installed, SQL_PARAMS_NAMED);
        $disabled = $DB->get_records_select('config_plugins', "plugin $installed AND name = 'disabled'", $params, 'plugin ASC');
        foreach ($disabled as $conf) {
            if (empty($conf->value)) {
                continue;
            }
            list($type, $name) = explode('_', $conf->plugin, 2);
            unset($plugins[$name]);
        }

        $enabled = array();
        foreach ($plugins as $plugin => $version) {
            $enabled[$plugin] = $plugin;
        }

        return $enabled;
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;

        $plugin = 'format_' . $pluginname;
        $oldvalue = get_config($plugin, 'disabled');
        $disabled = !$enabled;
        // Only set value if there is no config setting or if the value is different from the previous one.
        if ($oldvalue == false && $disabled) {
            if (get_config('moodlecourse', 'format') === $pluginname) {
                // The default course format can't be disabled.
                throw new \moodle_exception('cannotdisableformat', 'error');
            }
            set_config('disabled', $disabled, $plugin);
            $haschanged = true;
        } else if ($oldvalue != false && !$disabled) {
            unset_config('disabled', $plugin);
            $haschanged = true;
        }

        if ($haschanged) {
            add_to_config_log('disabled', $oldvalue, $disabled, $plugin);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    /**
     * Gathers and returns the information about all plugins of the given type
     *
     * @param string $type the name of the plugintype, eg. mod, auth or workshopform
     * @param string $typerootdir full path to the location of the plugin dir
     * @param string $typeclass the name of the actually called class
     * @param core_plugin_manager $pluginman the plugin manager calling this method
     * @return array of plugintype classes, indexed by the plugin name
     */
    public static function get_plugins($type, $typerootdir, $typeclass, $pluginman) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        $formats = parent::get_plugins($type, $typerootdir, $typeclass, $pluginman);
        $order = get_sorted_course_formats();
        $sortedformats = array();
        foreach ($order as $formatname) {
            $sortedformats[$formatname] = $formats[$formatname];
        }
        return $sortedformats;
    }

    public function get_settings_section_name() {
        return 'formatsetting' . $this->name;
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = new admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
        include($this->full_path('settings.php')); // This may also set $settings to null.

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    public function is_uninstall_allowed() {
        if ($this->name !== get_config('moodlecourse', 'format') && $this->name !== 'site') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/settings.php', array('section'=>'manageformats'));
    }

    public function get_uninstall_extra_warning() {
        global $DB;

        $coursecount = $DB->count_records('course', array('format' => $this->name));

        if (!$coursecount) {
            return '';
        }

        $defaultformat = $this->pluginman->plugin_name('format_'.get_config('moodlecourse', 'format'));
        $message = get_string(
            'formatuninstallwithcourses', 'core_admin',
            (object)array('count' => $coursecount, 'format' => $this->displayname,
                'defaultformat' => $defaultformat));

        return $message;
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
        global $DB;

        if (($defaultformat = get_config('moodlecourse', 'format')) && $defaultformat !== $this->name) {
            $courses = $DB->get_records('course', array('format' => $this->name), 'id');
            $data = (object)array('id' => null, 'format' => $defaultformat);
            foreach ($courses as $record) {
                $data->id = $record->id;
                update_course($data);
            }
        }

        $DB->delete_records('course_format_options', array('format' => $this->name));

        parent::uninstall_cleanup();
    }
}
