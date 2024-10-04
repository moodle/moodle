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
 * TinyMCE Premium plugins configuration page.
 *
 * @package    tiny_premium
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action = required_param('action', PARAM_ALPHANUMEXT);
$plugin = required_param('plugin', PARAM_ALPHANUMEXT);

$syscontext = context_system::instance();
$PAGE->set_url('/lib/editor/tiny/plugins/premium/pluginsettings.php');
$PAGE->set_context($syscontext);

require_admin();
require_sesskey();

$return = new moodle_url('/admin/settings.php', ['section' => 'tiny_premium_settings']);

// Get all Tiny Premium plugins.
$premiumplugins = \tiny_premium\manager::get_plugins();
if (!in_array($plugin, $premiumplugins)) {
    throw new moodle_exception('pluginnotfound', 'tiny_premium', $return, $plugin);
}

// Get enabled Tiny Premium plugins.
$enabledplugins = \tiny_premium\manager::get_enabled_plugins();
$pluginname = get_string('premiumplugin:' . $plugin, 'tiny_premium');

switch ($action) {
    case 'disable':
        if (in_array($plugin, $enabledplugins)) {
            \tiny_premium\manager::set_plugin_config(['enabled' => 0], $plugin);

            \core\notification::add(
                get_string('plugin_disabled', 'core_admin', $pluginname),
                \core\notification::SUCCESS
            );
        }
        break;

    case 'enable':
        if (!in_array($plugin, $enabledplugins)) {
            \tiny_premium\manager::set_plugin_config(['enabled' => 1], $plugin);

            \core\notification::add(
                get_string('plugin_enabled', 'core_admin', $pluginname),
                \core\notification::SUCCESS
            );

            // Special notification for the Accessibility Checker plugin.
            if ($plugin === 'a11ychecker') {
                \core\notification::add(
                    get_string('accessibilitycheckerinfo', 'tiny_premium'),
                    \core\notification::INFO
                );
            }
        }
        break;
}

redirect($return);
