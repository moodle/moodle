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
 * Generic plugin config manipulation script.
 *
 * @package    admin
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$plugin  = required_param('plugin', PARAM_PLUGIN);
$type    = required_param('type', PARAM_PLUGIN);

$PAGE->set_url('/admin/updatesetting.php');
$PAGE->set_context(context_system::instance());

require_admin();
require_sesskey();

$plugintypeclass = "\\core\\plugininfo\\{$type}";

$plugins = \core_plugin_manager::instance()->get_plugins_of_type($type);
$sortorder = array_values($plugintypeclass::get_enabled_plugins());

$return = $plugintypeclass::get_manage_url();

if (!array_key_exists($plugin, $plugins)) {
    redirect($return);
}

switch ($action) {
    case 'disable':
        $class = \core_plugin_manager::resolve_plugininfo_class($type);
        $class::enable_plugin($plugin, false);
        break;

    case 'enable':
        $class = \core_plugin_manager::resolve_plugininfo_class($type);
        $class::enable_plugin($plugin, true);
        break;

    case 'up':
        if (($pos = array_search($plugin, $sortorder)) > 0) {
            $tmp = $sortorder[$pos - 1];
            $sortorder[$pos - 1] = $sortorder[$pos];
            $sortorder[$pos] = $tmp;
            $plugintypeclass::set_enabled_plugins($sortorder);
        }
        break;

    case 'down':
        if ((($pos = array_search($plugin, $sortorder)) !== false) && ($pos < count($sortorder) - 1)) {
            $tmp = $sortorder[$pos + 1];
            $sortorder[$pos + 1] = $sortorder[$pos];
            $sortorder[$pos] = $tmp;
            $plugintypeclass::set_enabled_plugins($sortorder);
        }
        break;
}

redirect($return);
