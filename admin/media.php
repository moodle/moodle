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
 * Enrol config manipulation script.
 *
 * @package    core
 * @subpackage media
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once("{$CFG->libdir}/adminlib.php");

$action = required_param('action', PARAM_ALPHANUMEXT);
$plugin = required_param('plugin', PARAM_PLUGIN);

$PAGE->set_url('/admin/media.php');
$PAGE->set_context(context_system::instance());

require_admin();
require_sesskey();

$return = new moodle_url('/admin/settings.php', [
    'section' => 'managemediaplayers',
]);

$displayname = get_string('pluginname', "media_{$plugin}");
switch ($action) {
    case 'disable':
        $class = \core_plugin_manager::resolve_plugininfo_class('media');
        if ($class::enable_plugin($plugin, false)) {
            \core\notification::add(
                get_string('plugin_disabled', 'core_admin', $displayname),
                \core\notification::SUCCESS
            );
        }
        break;

    case 'enable':
        $class = \core_plugin_manager::resolve_plugininfo_class('media');
        if ($class::enable_plugin($plugin, true)) {
            \core\notification::add(
                get_string('plugin_enabled', 'core_admin', $displayname),
                \core\notification::SUCCESS
            );
        }
        break;

    case 'up':
        $class::change_plugin_order($plugin, $class::MOVE_UP);
        break;

    case 'down':
        $class::change_plugin_order($plugin, $class::MOVE_DOWN);
        break;
}

redirect($return);
