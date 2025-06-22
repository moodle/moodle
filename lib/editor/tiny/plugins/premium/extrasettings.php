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
 * TinyMCE Premium plugins settings page.
 *
 * @package    tiny_premium
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// This is an admin page.
admin_externalpage_setup('tiny_premium_plugin_settings');
$return = new moodle_url('/admin/settings.php', ['section' => 'tiny_premium_settings']);
$plugin = optional_param('plugin', '', PARAM_ALPHA);

// Get form class.
$form = \tiny_premium\manager::get_settings_form(
    plugin: $plugin,
    return: $return,
);
if ($form->is_cancelled()) {
    redirect($return);
} else if ($data = $form->get_data()) {
    if (isset($data->service_url) && $data->service_url !== '') {
        \tiny_premium\manager::set_plugin_config(
            data: ['service_url' => $data->service_url],
            plugin: $data->plugin,
        );
    } else {
        \tiny_premium\manager::unset_plugin_config(
            names: ['service_url'],
            plugin: $data->plugin,
        );
    }
    redirect($return, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Get all Tiny Premium plugins.
$premiumplugins = \tiny_premium\manager::get_plugins();
if (!in_array($plugin, $premiumplugins)) {
    throw new moodle_exception('pluginnotfound', 'tiny_premium', $return, $plugin);
}
$pluginname = get_string('premiumplugin:' . $plugin, 'tiny_premium');

// Display the page.
echo $OUTPUT->header();
echo $OUTPUT->heading($pluginname);
$form->display();
echo $OUTPUT->footer();
