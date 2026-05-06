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

namespace core_admin\setting\setting;

use core_admin\admin_search;

/**
 * Generic class for managing plugins in a table that allows re-ordering and enable/disable of each plugin.
 * Requires a get_rank method on the plugininfo class for sorting.
 *
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class manage_plugins extends \admin_setting {

    /**
     * Get the admin settings section name (just a unique string)
     *
     * @return string
     */
    public function get_section_name() {
        return 'manage' . $this->get_plugin_type() . 'plugins';
    }

    /**
     * Get the admin settings section title (use get_string).
     *
     * @return string
     */
    abstract public function get_section_title();

    /**
     * Get the type of plugin to manage.
     *
     * @return string
     */
    abstract public function get_plugin_type();

    /**
     * Get the name of the second column.
     *
     * @return string
     */
    public function get_info_column_name() {
        return '';
    }

    /**
     * Get the type of plugin to manage.
     *
     * @param \core\plugininfo\base $plugininfo The plugin info class.
     * @return string
     */
    abstract public function get_info_column($plugininfo);

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct($this->get_section_name(), $this->get_section_title(), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param mixed $data
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available plugins of this type
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $query = \core_text::strtolower($query);
        $plugins = \core_plugin_manager::instance()->get_plugins_of_type($this->get_plugin_type());
        foreach ($plugins as $name => $plugin) {
            $localised = $plugin->displayname;
            if (strpos(\core_text::strtolower($name), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            if (strpos(\core_text::strtolower($localised), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    /**
     * The URL for the management page for this plugintype.
     *
     * @return moodle_url
     */
    protected function get_manage_url() {
        return new \moodle_url('/admin/updatesetting.php');
    }

    /**
     * Builds the HTML to display the control.
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT, $DB, $PAGE;

        $context = (object) [
            'manageurl' => new \moodle_url($this->get_manage_url(), [
                    'type' => $this->get_plugin_type(),
                    'sesskey' => sesskey(),
                ]),
            'infocolumnname' => $this->get_info_column_name(),
            'plugins' => [],
        ];

        $pluginmanager = \core_plugin_manager::instance();
        $allplugins = $pluginmanager->get_plugins_of_type($this->get_plugin_type());
        $enabled = $pluginmanager->get_enabled_plugins($this->get_plugin_type());
        $plugins = array_merge($enabled, $allplugins);
        foreach ($plugins as $key => $plugin) {
            $pluginlink = new \moodle_url($context->manageurl, ['plugin' => $key]);

            $pluginkey = (object) [
                'plugin' => $plugin->displayname,
                'enabled' => $plugin->is_enabled(),
                'togglelink' => '',
                'moveuplink' => '',
                'movedownlink' => '',
                'settingslink' => $plugin->get_settings_url(),
                'uninstalllink' => '',
                'info' => '',
            ];

            // Enable/Disable link.
            $togglelink = new \moodle_url($pluginlink);
            if ($plugin->is_enabled()) {
                $toggletarget = false;
                $togglelink->param('action', 'disable');

                if (count($context->plugins)) {
                    // This is not the first plugin.
                    $pluginkey->moveuplink = new \moodle_url($pluginlink, ['action' => 'up']);
                }

                if (count($enabled) > count($context->plugins) + 1) {
                    // This is not the last plugin.
                    $pluginkey->movedownlink = new \moodle_url($pluginlink, ['action' => 'down']);
                }

                $pluginkey->info = $this->get_info_column($plugin);
            } else {
                $toggletarget = true;
                $togglelink->param('action', 'enable');
            }

            $pluginkey->toggletarget = $toggletarget;
            $pluginkey->togglelink = $togglelink;

            $frankenstyle = $plugin->type . '_' . $plugin->name;
            if ($uninstalllink = \core_plugin_manager::instance()->get_uninstall_url($frankenstyle, 'manage')) {
                // This plugin supports uninstallation.
                $pluginkey->uninstalllink = $uninstalllink;
            }

            if (!empty($this->get_info_column_name())) {
                // This plugintype has an info column.
                $pluginkey->info = $this->get_info_column($plugin);
            }

            $context->plugins[] = $pluginkey;
        }

        $str = $OUTPUT->render_from_template('core_admin/setting_manage_plugins', $context);
        return highlight($query, $str);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manage_plugins::class, \admin_setting_manage_plugins::class);
