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
 * Provides an overview of installed reports
 *
 * Displays the list of found reports, their version (if found) and
 * a link to delete the report.
 *
 * The code is based on admin/localplugins.php by David Mudrak.
 *
 * @package   admin
 * @copyright 2011 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

admin_externalpage_setup('managereports');

$delete  = optional_param('delete', '', PARAM_PLUGIN);
$confirm = optional_param('confirm', '', PARAM_BOOL);

/// If data submitted, then process and store.

if (!empty($delete) and confirm_sesskey()) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('reports'));

    if (!$confirm) {
        if (get_string_manager()->string_exists('pluginname', 'report_' . $delete)) {
            $strpluginname = get_string('pluginname', 'report_' . $delete);
        } else {
            $strpluginname = $delete;
        }
        echo $OUTPUT->confirm(get_string('reportsdeleteconfirm', 'admin', $strpluginname),
                                new moodle_url($PAGE->url, array('delete' => $delete, 'confirm' => 1)),
                                $PAGE->url);
        echo $OUTPUT->footer();
        die();

    } else {
        uninstall_plugin('report', $delete);
        $a = new stdclass();
        $a->name = $delete;
        $pluginlocation = get_plugin_types();
        $a->directory = $pluginlocation['report'] . '/' . $delete;
        echo $OUTPUT->notification(get_string('plugindeletefiles', '', $a), 'notifysuccess');
        echo $OUTPUT->continue_button($PAGE->url);
        echo $OUTPUT->footer();
        die();
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reports'));

/// Print the table of all installed report plugins

$table = new flexible_table('reportplugins_administration_table');
$table->define_columns(array('name', 'version', 'delete'));
$table->define_headers(array(get_string('plugin'), get_string('version'), get_string('delete')));
$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'reportplugins');
$table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
$table->setup();

$plugins = array();
foreach (get_plugin_list('report') as $plugin => $plugindir) {
    if (get_string_manager()->string_exists('pluginname', 'report_' . $plugin)) {
        $strpluginname = get_string('pluginname', 'report_' . $plugin);
    } else {
        $strpluginname = $plugin;
    }
    $plugins[$plugin] = $strpluginname;
}
collatorlib::asort($plugins);

$like = $DB->sql_like('plugin', '?', true, true, false, '|');
$params = array('report|_%');
$installed = $DB->get_records_select('config_plugins', "$like AND name = 'version'", $params);
$versions = array();
foreach ($installed as $config) {
    $name = preg_replace('/^report_/', '', $config->plugin);
    $versions[$name] = $config->value;
    if (!isset($plugins[$name])) {
        $plugins[$name] = $name;
    }
}

foreach ($plugins as $plugin => $name) {
    $delete = new moodle_url($PAGE->url, array('delete' => $plugin, 'sesskey' => sesskey()));
    $delete = html_writer::link($delete, get_string('delete'));

    if (!isset($versions[$plugin])) {
        if (file_exists("$CFG->dirroot/report/$plugin/version.php")) {
            // not installed yet
            $version = '?';
        } else {
            // no version info available
            $version = '-';
        }
    } else {
        $version = $versions[$plugin];
        if (file_exists("$CFG->dirroot/report/$plugin")) {
            $version = $versions[$plugin];
        } else {
            // somebody removed plugin without uninstall
            $name = '<span class="notifyproblem">'.$name.' ('.get_string('missingfromdisk').')</span>';
            $version = $versions[$plugin];
        }
    }

    $table->add_data(array($name, $version, $delete));
}

$table->print_html();

echo $OUTPUT->footer();
