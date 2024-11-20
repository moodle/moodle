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
 * Tiny noautolink plugin install code.
 *
 * @package    tiny_noautolink
 * @copyright  2023 Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Install the Tiny noautolink plugin in a disabled state.
 */
function xmldb_tiny_noautolink_install() {
    $tinymanager = \core_plugin_manager::resolve_plugininfo_class('tiny');
    // Disabled the plugin.
    $tinymanager::enable_plugin('noautolink', 0);
}
