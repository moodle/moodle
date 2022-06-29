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

use moodle_url, part_of_admin_tree, admin_settingpage;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for messaging processors
 */
class message extends base {
    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $DB;
        return $DB->get_records_menu('message_processors', array('enabled'=>1), 'name ASC', 'name, name AS val');
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        global $DB;

        if (!$plugin = $DB->get_record('message_processors', ['name' => $pluginname])) {
            throw new \moodle_exception('invalidplugin', 'message', '', $pluginname);
        }

        $haschanged = false;

        // Only set visibility if it's different from the current value.
        if ($plugin->enabled != $enabled) {
            $haschanged = true;
            $processor = \core_message\api::get_processed_processor_object($plugin);

            // Include this information into config changes table.
            add_to_config_log($processor->name, $processor->enabled, $enabled, 'core');

            // Save processor enabled/disabled status.
            \core_message\api::update_processor_status($processor, $enabled);
        }

        return $haschanged;
    }

    public function get_settings_section_name() {
        return 'messagesetting' . $this->name;
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }
        $section = $this->get_settings_section_name();

        $settings = null;
        $processors = get_message_processors();
        if (isset($processors[$this->name])) {
            $processor = $processors[$this->name];
            if ($processor->available && $processor->hassettings) {
                $settings = new admin_settingpage($section, $this->displayname,
                    'moodle/site:config', $this->is_enabled() === false);
                include($this->full_path('settings.php')); // This may also set $settings to null.
            }
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/message.php');
    }

    public function is_uninstall_allowed() {
        return true;
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
        global $CFG;

        require_once($CFG->libdir.'/messagelib.php');
        message_processor_uninstall($this->name);

        parent::uninstall_cleanup();
    }
}
