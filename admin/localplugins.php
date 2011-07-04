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
 * Provides an overview of installed local plugins
 *
 * Displays the list of found local plugins, their version (if found) and
 * a link to delete the local plugin.
 *
 * @see       http://docs.moodle.org/dev/Local_customisation
 * @package   admin
 * @copyright 2010 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

admin_externalpage_setup('managelocalplugins');

$delete  = optional_param('delete', '', PARAM_SAFEDIR);
$confirm = optional_param('confirm', '', PARAM_BOOL);

/// If data submitted, then process and store.

if (!empty($delete) and confirm_sesskey()) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('localplugins'));

    if (!$confirm) {
        if (get_string_manager()->string_exists('pluginname', 'local_' . $delete)) {
            $strpluginname = get_string('pluginname', 'local_' . $delete);
        } else {
            $strpluginname = $delete;
        }
        echo $OUTPUT->confirm(get_string('localplugindeleteconfirm', '', $strpluginname),
                                new moodle_url($PAGE->url, array('delete' => $delete, 'confirm' => 1)),
                                $PAGE->url);
        echo $OUTPUT->footer();
        die();

    } else {
        uninstall_plugin('local', $delete);
        $a = new stdclass();
        $a->name = $delete;
        $pluginlocation = get_plugin_types();
        $a->directory = $pluginlocation['local'] . '/' . $delete;
        echo $OUTPUT->notification(get_string('plugindeletefiles', '', $a), 'notifysuccess');
        echo $OUTPUT->continue_button($PAGE->url);
        echo $OUTPUT->footer();
        die();
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('localplugins'));

/// Print the table of all installed local plugins

$table = new flexible_table('localplugins_administration_table');
$table->define_columns(array('name', 'version', 'delete'));
$table->define_headers(array(get_string('plugin'), get_string('version'), get_string('delete')));
$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'localplugins');
$table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
$table->setup();

$plugins = array();
foreach (get_plugin_list('local') as $plugin => $plugindir) {
    if (get_string_manager()->string_exists('pluginname', 'local_' . $plugin)) {
        $strpluginname = get_string('pluginname', 'local_' . $plugin);
    } else {
        $strpluginname = $plugin;
    }
    $plugins[$plugin] = $strpluginname;
}
textlib_get_instance()->asort($plugins);

foreach ($plugins as $plugin => $name) {
    $delete = new moodle_url($PAGE->url, array('delete' => $plugin, 'sesskey' => sesskey()));
    $delete = html_writer::link($delete, get_string('delete'));

    $version = get_config('local_' . $plugin);
    if (!empty($version->version)) {
        $version = $version->version;
    } else {
        $version = '?';
    }

    $table->add_data(array($name, $version, $delete));
}

$table->print_html();

echo $OUTPUT->footer();
