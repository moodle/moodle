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
 * a link to uninstall the plagiarism plugin.
 *
 * @see       https://moodledev.io/docs/apis/subsystems/plagiarism
 * @package   admin
 * @copyright 2012 Dan Marsden <dan@danmarsden.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');


admin_externalpage_setup('manageplagiarismplugins');

echo $OUTPUT->header();

// Print the table of all installed plagiarism plugins.

$txt = get_strings(array('settings', 'name', 'version'));
$txt->uninstall = get_string('uninstallplugin', 'core_admin');

$plagiarismplugins = core_component::get_plugin_list('plagiarism');
if (empty($plagiarismplugins)) {
    echo $OUTPUT->notification(get_string('nopluginsinstalled', 'plagiarism'));
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->heading(get_string('availableplugins', 'plagiarism'), 3, 'main');
echo $OUTPUT->box_start('generalbox authsui');

$table = new html_table();
$table->head  = array($txt->name, $txt->version, $txt->uninstall, $txt->settings);
$table->colclasses = array('mdl-left', 'mdl-align', 'mdl-align', 'mdl-align');
$table->data  = array();
$table->attributes['class'] = 'manageplagiarismtable table generaltable table-hover';

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
        // uninstall link.
        $uninstall = '';
        if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('plagiarism_'.$plugin, 'manage')) {
            $uninstall = html_writer::link($uninstallurl, $txt->uninstall);
        }
        $table->data[] = array($displayname, $version, $uninstall, $settings);
    }
}
echo html_writer::table($table);
echo get_string('configplagiarismplugins', 'plagiarism');
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
