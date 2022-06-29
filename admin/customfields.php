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
 * Allows the admin to enable, disable and uninstall custom fields
 *
 * @package    core_admin
 * @copyright  2018 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$customfieldname = required_param('field', PARAM_PLUGIN);

$syscontext = context_system::instance();
$PAGE->set_url('/admin/customfields.php');
$PAGE->set_context($syscontext);

require_admin();
require_sesskey();

$return = new moodle_url('/admin/settings.php', array('section' => 'managecustomfields'));

$customfieldplugins = core_plugin_manager::instance()->get_plugins_of_type('customfield');
$sortorder = array_flip(array_keys($customfieldplugins));

if (!isset($customfieldplugins[$customfieldname])) {
    print_error('customfieldnotfound', 'error', $return, $customfieldname);
}

switch ($action) {
    case 'disable':
        if ($customfieldplugins[$customfieldname]->is_enabled()) {
            $class = \core_plugin_manager::resolve_plugininfo_class('customfield');
            $class::enable_plugin($customfieldname, false);
        }
        break;
    case 'enable':
        if (!$customfieldplugins[$customfieldname]->is_enabled()) {
            $class = \core_plugin_manager::resolve_plugininfo_class('customfield');
            $class::enable_plugin($customfieldname, true);
        }
        break;
}
redirect($return);
