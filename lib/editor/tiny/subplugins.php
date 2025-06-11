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
 * Tiny subplugin management.
 *
 * @package   editor_tinymce
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once("{$CFG->libdir}/adminlib.php");

$action = optional_param('action', '', PARAM_ALPHA);
$plugin = optional_param('plugin', '', PARAM_PLUGIN);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/editor/tinymce/subplugins.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

$tinymanager = \core_plugin_manager::resolve_plugininfo_class('tiny');
$pluginname = get_string('pluginname', "tiny_{$plugin}");

switch ($action) {
    case 'disable':
        if ($tinymanager::enable_plugin($plugin, 0)) {
            \core\notification::add(
                get_string('plugin_disabled', 'editor_tiny', $pluginname),
                \core\notification::SUCCESS
            );
        }
        break;
    case 'enable':
        if ($tinymanager::enable_plugin($plugin, 1)) {
            \core\notification::add(
                get_string('plugin_enabled', 'editor_tiny', $pluginname),
                \core\notification::SUCCESS
            );
        }
        break;
    default:
}

redirect(new moodle_url('/admin/settings.php', [
    'section' => 'editorsettingstiny',
]));
