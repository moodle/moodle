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
 * Payment gateway admin setting.
 *
 * @package    core_admin
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_admin\local\settings;

/**
 * Generic class for managing plugins in a table that allows re-ordering and enable/disable of each plugin.
 */
class manage_payment_gateway_plugins extends \admin_setting_manage_plugins {
    /**
     * Get the admin settings section title (use get_string).
     *
     * @return string
     */
    public function get_section_title() {
        return get_string('type_pg_plural', 'plugin');
    }

    /**
     * Get the type of plugin to manage.
     *
     * @return string
     */
    public function get_plugin_type() {
        return 'pg';
    }

    /**
     * Get the name of the second column.
     *
     * @return string
     */
    public function get_info_column_name() {
        return get_string('supportedcurrencies', 'core_payment');
    }

    /**
     * Get the type of plugin to manage.
     *
     * @param plugininfo The plugin info class.
     * @return string
     */
    public function get_info_column($plugininfo) {
        $codes = $plugininfo->get_supported_currencies();

        $currencies = [];
        foreach ($codes as $c) {
            $currencies[$c] = new \lang_string($c, 'core_currencies');
        }

        return implode(get_string('listsep', 'langconfig') . ' ', $currencies);
    }
}
