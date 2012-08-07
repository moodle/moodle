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
 * Provides an overview of installed plagiarism plugins
 *
 * Displays the list of found plagiarism plugins, their version (if found) and
 * a link to delete the plagiarism plugin.
 *
 * @see       http://docs.moodle.org/dev/Plagiarism_API
 * @package   admin
 * @copyright 2012 Dan Marsden <dan@danmarsden.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

admin_externalpage_setup('manageplagiarismplugins');

$delete  = optional_param('delete', '', PARAM_PLUGIN);
$confirm = optional_param('confirm', false, PARAM_BOOL);

if (!empty($delete) and confirm_sesskey()) { // If data submitted, then process and store.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('manageplagiarism', 'plagiarism'));

    if (!$confirm) {
        if (get_string_manager()->string_exists('pluginname', 'plagiarism_' . $delete)) {
            $strpluginname = get_string('pluginname', 'plagiarism_' . $delete);
        } else {
            $strpluginname = $delete;
        }
        echo $OUTPUT->confirm(get_string('plagiarismplugindeleteconfirm', 'plagiarism', $strpluginname),
            new moodle_url($PAGE->url, array('delete' => $delete, 'confirm' => 1)),
            $PAGE->url);
        echo $OUTPUT->footer();
        die();

    } else {
        uninstall_plugin('plagiarism', $delete);
        $a = new stdclass();
        $a->name = $delete;
        $pluginlocation = get_plugin_types();
        $a->directory = $pluginlocation['plagiarism'] . '/' . $delete;
        echo $OUTPUT->notification(get_string('plugindeletefiles', '', $a), 'notifysuccess');
        echo $OUTPUT->continue_button($PAGE->url);
        echo $OUTPUT->footer();
        die();
    }
}

echo $OUTPUT->header();

// Print the table of all installed plagiarism plugins.

$txt = get_strings(array('settings', 'name', 'version', 'delete'));

$plagiarismplugins = get_plugin_list('plagiarism');
if (empty($plagiarismplugins)) {
    echo $OUTPUT->notification(get_string('nopluginsinstalled', 'plagiarism'));
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->heading(get_string('availableplugins', 'plagiarism'), 3, 'main');
echo $OUTPUT->box_start('generalbox authsui');

$table = new html_table();
$table->head  = array($txt->name, $txt->version, $txt->delete, $txt->settings);
$table->colclasses = array('mdl-left', 'mdl-align', 'mdl-align', 'mdl-align');
$table->data  = array();
$table->attributes['class'] = 'manageplagiarismtable generaltable';

// Iterate through auth plugins and add to the display table.
$authcount = count($plagiarismplugins);
foreach ($plagiarismplugins as $plugin => $dir) {
    if (file_exists($dir.'/settings.php')) {
        $displayname = "<span>".get_string($plugin, 'plagiarism_'.$plugin)."</span>";
        // Settings link.
        $url = new moodle_url("/plagiarism/$plugin/settings.php");
        $settings = html_writer::link($url, $txt->settings);
        // Get version.
        $version = get_config('plagiarism_' . $plugin);
        if (!empty($version->version)) {
            $version = $version->version;
        } else {
            $version = '?';
        }
        // Delete link.
        $delete = new moodle_url($PAGE->url, array('delete' => $plugin, 'sesskey' => sesskey()));
        $delete = html_writer::link($delete, get_string('delete'));
        $table->data[] = array($displayname, $version, $delete, $settings);
    }
}
echo html_writer::table($table);
echo get_string('configplagiarismplugins', 'plagiarism');
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
