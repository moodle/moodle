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
 * Provides an overview of installed admin tools
 *
 * Displays the list of found admin tools, their version (if found) and
 * a link to uninstall the admin tool.
 *
 * The code is based on admin/localplugins.php by David Mudrak.
 *
 * @package   admin
 * @copyright 2011 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

admin_externalpage_setup('managetools');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('tools', 'admin'));

/// Print the table of all installed tool plugins

$struninstall = get_string('uninstallplugin', 'core_admin');

$table = new flexible_table('toolplugins_administration_table');
$table->define_columns(array('name', 'version', 'uninstall'));
$table->define_headers(array(get_string('plugin'), get_string('version'), $struninstall));
$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'toolplugins');
$table->set_attribute('class', 'admintable generaltable');
$table->setup();

$plugins = array();
foreach (core_component::get_plugin_list('tool') as $plugin => $plugindir) {
    if (get_string_manager()->string_exists('pluginname', 'tool_' . $plugin)) {
        $strpluginname = get_string('pluginname', 'tool_' . $plugin);
    } else {
        $strpluginname = $plugin;
    }
    $plugins[$plugin] = $strpluginname;
}
core_collator::asort($plugins);

$like = $DB->sql_like('plugin', '?', true, true, false, '|');
$params = array('tool|_%');
$installed = $DB->get_records_select('config_plugins', "$like AND name = 'version'", $params);
$versions = array();
foreach ($installed as $config) {
    $name = preg_replace('/^tool_/', '', $config->plugin);
    $versions[$name] = $config->value;
    if (!isset($plugins[$name])) {
        $plugins[$name] = $name;
    }
}

foreach ($plugins as $plugin => $name) {
    $uninstall = '';
    if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('tool_'.$plugin, 'manage')) {
        $uninstall = html_writer::link($uninstallurl, $struninstall);
    }

    if (!isset($versions[$plugin])) {
        if (file_exists("$CFG->dirroot/$CFG->admin/tool/$plugin/version.php")) {
            // not installed yet
            $version = '?';
        } else {
            // no version info available
            $version = '-';
        }
    } else {
        $version = $versions[$plugin];
        if (file_exists("$CFG->dirroot/$CFG->admin/tool/$plugin")) {
            $version = $versions[$plugin];
        } else {
            // somebody removed plugin without uninstall
            $name = '<span class="notifyproblem">'.$name.' ('.get_string('missingfromdisk').')</span>';
            $version = $versions[$plugin];
        }
    }

    $table->add_data(array($name, $version, $uninstall));
}

$table->print_html();

echo $OUTPUT->footer();
