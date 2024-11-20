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
 * A page to manage editor plugins.
 *
 * @package   core_admin
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

$action = required_param('action', PARAM_ALPHANUMEXT);
$plugin = required_param('plugin', PARAM_PLUGIN);

$PAGE->set_url('/admin/editors.php', ['action' => $action, 'editor' => $plugin]);
$PAGE->set_context(context_system::instance());

require_admin();
require_sesskey();

$returnurl = new moodle_url('/admin/settings.php', ['section' => 'manageeditors']);

// Get currently installed and enabled auth plugins.
$availableeditors = editors_get_available();
if (!empty($plugin) && empty($availableeditors[$plugin])) {
    redirect($returnurl);
}

$activeeditors = explode(',', $CFG->texteditors);
foreach ($activeeditors as $key => $active) {
    if (empty($availableeditors[$active])) {
        unset($activeeditors[$key]);
    }
}

switch ($action) {
    case 'disable':
        // Remove from enabled list.
        $class = \core_plugin_manager::resolve_plugininfo_class('editor');
        $class::enable_plugin($plugin, false);
        break;

    case 'enable':
        // Add to enabled list.
        if (!in_array($plugin, $activeeditors)) {
            $class = \core_plugin_manager::resolve_plugininfo_class('editor');
            $class::enable_plugin($plugin, true);
        }
        break;

    case 'down':
        $key = array_search($plugin, $activeeditors);
        if ($key !== false) {
            // Move down the list.
            if ($key < (count($activeeditors) - 1)) {
                $fsave = $activeeditors[$key];
                $activeeditors[$key] = $activeeditors[$key + 1];
                $activeeditors[$key + 1] = $fsave;
                add_to_config_log('editor_position', $key, $key + 1, $plugin);
                set_config('texteditors', implode(',', $activeeditors));
                core_plugin_manager::reset_caches();
            }
        }
        break;

    case 'up':
        $key = array_search($plugin, $activeeditors);
        if ($key !== false) {
            // Move up the list.
            if ($key >= 1) {
                $fsave = $activeeditors[$key];
                $activeeditors[$key] = $activeeditors[$key - 1];
                $activeeditors[$key - 1] = $fsave;
                add_to_config_log('editor_position', $key, $key - 1, $plugin);
                set_config('texteditors', implode(',', $activeeditors));
                core_plugin_manager::reset_caches();
            }
        }
        break;

    default:
        break;
}

redirect($returnurl);
