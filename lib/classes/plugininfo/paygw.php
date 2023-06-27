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
 * Contains subplugin info class for payment gateways.
 *
 * @package   core_payment
 * @copyright 2019 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\plugininfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Payment gateway subplugin info class.
 *
 * @copyright 2019 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class paygw extends base {
    public function is_uninstall_allowed() {
        return true;
    }

    public function get_settings_section_name() {
        return 'paymentgateway' . $this->name;
    }

    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
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
        if (file_exists($this->full_path('settings.php'))) {
            $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    public static function get_manage_url() {
        return new \moodle_url('/admin/settings.php', array('section' => 'managepaymentgateways'));
    }

    public static function get_enabled_plugins() {
        global $CFG;

        $order = (!empty($CFG->paygw_plugins_sortorder)) ? explode(',', $CFG->paygw_plugins_sortorder) : [];
        if ($order) {
            $plugins = \core_plugin_manager::instance()->get_installed_plugins('paygw');
            $order = array_intersect($order, array_keys($plugins));
        }

        return array_combine($order, $order);
    }

    /**
     * Sets the current plugin as enabled or disabled
     * When enabling tries to guess the sortorder based on default rank returned by the plugin.
     *
     * @param bool $newstate
     */
    public function set_enabled(bool $newstate = true) {
        $enabled = self::get_enabled_plugins();
        if (array_key_exists($this->name, $enabled) == $newstate) {
            // Nothing to do.
            return;
        }
        if ($newstate) {
            // Enable gateway plugin.
            $plugins = \core_plugin_manager::instance()->get_plugins_of_type('paygw');
            if (!array_key_exists($this->name, $plugins)) {
                // Can not be enabled.
                return;
            }
            $enabled[$this->name] = $this->name;
            self::set_enabled_plugins($enabled);
        } else {
            // Disable gateway plugin.
            unset($enabled[$this->name]);
            self::set_enabled_plugins($enabled);
        }
    }

    /**
     * Set the list of enabled payment gateways in the specified sort order
     * To be used when changing settings or in unit tests.
     *
     * @param string|array $list list of plugin names without frankenstyle prefix - comma-separated string or an array
     */
    public static function set_enabled_plugins($list) {
        if (empty($list)) {
            $list = [];
        } else if (!is_array($list)) {
            $list = explode(',', $list);
        }
        if ($list) {
            $plugins = \core_plugin_manager::instance()->get_installed_plugins('paygw');
            $list = array_intersect($list, array_keys($plugins));
        }
        set_config('paygw_plugins_sortorder', join(',', $list));
        \core_plugin_manager::reset_caches();
    }

    /**
     * Returns the list of currencies that the payment gateway supports.
     *
     * @return string[] An array of the currency codes in the three-character ISO-4217 format
     */
    public function get_supported_currencies(): array {
        $classname = '\paygw_'.$this->name.'\gateway';

        return $classname::get_supported_currencies();
    }
}
