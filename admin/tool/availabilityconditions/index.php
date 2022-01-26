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
 * Provides an overview of installed availability conditions.
 *
 * You can also enable/disable them from this screen.
 *
 * @package tool_availabilityconditions
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

admin_externalpage_setup('manageavailability');

// Get sorted list of all availability condition plugins.
$plugins = array();
foreach (core_component::get_plugin_list('availability') as $plugin => $plugindir) {
    if (get_string_manager()->string_exists('pluginname', 'availability_' . $plugin)) {
        $strpluginname = get_string('pluginname', 'availability_' . $plugin);
    } else {
        $strpluginname = $plugin;
    }
    $plugins[$plugin] = $strpluginname;
}
core_collator::asort($plugins);

// Do plugin actions.
$pageurl = new moodle_url('/' . $CFG->admin . '/tool/availabilityconditions/');
if (($plugin = optional_param('plugin', '', PARAM_PLUGIN))) {
    require_sesskey();
    if (!array_key_exists($plugin, $plugins)) {
        print_error('invalidcomponent', 'error', $pageurl);
    }
    $action = required_param('action', PARAM_ALPHA);
    switch ($action) {
        case 'hide' :
            $class = \core_plugin_manager::resolve_plugininfo_class('availability');
            $class::enable_plugin($plugin, false);
            break;
        case 'show' :
            $class = \core_plugin_manager::resolve_plugininfo_class('availability');
            $class::enable_plugin($plugin, true);
            break;
    }

    // Always redirect back after an action.
    redirect($pageurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageplugins', 'availability'));

// Show a table of installed availability conditions.
$table = new flexible_table('availabilityconditions_administration_table');
$table->define_columns(array('name', 'version', 'enable'));
$table->define_headers(array(get_string('plugin'),
        get_string('version'), get_string('hide') . '/' . get_string('show')));
$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'availabilityconditions');
$table->set_attribute('class', 'admintable generaltable');
$table->setup();

$enabledlist = core\plugininfo\availability::get_enabled_plugins();
foreach ($plugins as $plugin => $name) {

    // Get version or ? if unknown.
    $version = get_config('availability_' . $plugin);
    if (!empty($version->version)) {
        $version = $version->version;
    } else {
        $version = '?';
    }

    // Get enabled status and use to grey out name if necessary.
    $enabled = in_array($plugin, $enabledlist);
    if ($enabled) {
        $enabledaction = 'hide';
        $enabledstr = get_string('hide');
        $class = '';
    } else {
        $enabledaction = 'show';
        $enabledstr = get_string('show');
        $class = 'dimmed_text';
    }

    // Make enable control. This is a POST request (using a form control rather
    // than just a link) because it makes a database change.
    $params = array('sesskey' => sesskey(), 'plugin' => $plugin, 'action' => $enabledaction);
    $url = new moodle_url('/' . $CFG->admin . '/tool/availabilityconditions/', $params);
    $enablecontrol = html_writer::link($url, $OUTPUT->pix_icon('t/' . $enabledaction, $enabledstr));

    $table->add_data([$name, $version, $enablecontrol], $class);
}

$table->print_html();

echo $OUTPUT->footer();
