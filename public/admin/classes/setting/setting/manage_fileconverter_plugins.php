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
 * Generic class for managing plugins in a table that allows re-ordering and enable/disable of each plugin.
 * Requires a get_rank method on the plugininfo class for sorting.
 *
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class manage_fileconverter_plugins extends \core_admin\setting\setting\manage_plugins {
    public function get_section_title() {
        return get_string('type_fileconverter_plural', 'plugin');
    }

    public function get_plugin_type() {
        return 'fileconverter';
    }

    public function get_info_column_name() {
        return get_string('supportedconversions', 'plugin');
    }

    public function get_info_column($plugininfo) {
        return $plugininfo->get_supported_conversions();
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manage_fileconverter_plugins::class, \admin_setting_manage_fileconverter_plugins::class);
