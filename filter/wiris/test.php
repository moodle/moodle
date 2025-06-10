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
 * MathType filter test page.
 *
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function create_atto_compatibility_row($currenteditordata = null, $solutionlink = null) {
    // Get Wiris plugin instance.
    $wirisplugin = filter_wiris_pluginwrapper::get_wiris_plugin();

    // Prepare test name.
    $testname = get_string('wirispluginfilterfor', 'filter_wiris') . '&nbsp;' . $currenteditordata['plugin_name'] . ' versions';

    // Get plugin version.
    $pluginversion = '';
    $versionfile = (strtolower($currenteditordata['plugin_name']) == 'tinymce' || strtolower($currenteditordata['plugin_name']) == 'tiny')
        ? $currenteditordata['plugin_path'] . '/../version.php'
        : $currenteditordata['plugin_path'] . '/version.php';

    if (file_exists($versionfile)) {
        include($versionfile);
        if (isset($plugin->version)) {
            $pluginversion = $plugin->version;
        }
    }

    // Get filter version.
    $filterversion = isset($plugin->version) ? $plugin->version : '';

    // Check compatibility.
    if ($filterversion == $pluginversion) {
        $reporttext = get_string('wirispluginfilterfor', 'filter_wiris') . '&nbsp;' . $currenteditordata['plugin_name'] . '&nbsp;' .
            get_string('havesameversion', 'filter_wiris');
        $condition = true;
    } else {
        $reporttext = get_string('wirispluginfilterfor', 'filter_wiris') . '&nbsp;' . $currenteditordata['plugin_name'] . '&nbsp;' .
            get_string('versionsdontmatch', 'filter_wiris');
        $reporttext .= "<br>" . get_string('wirisfilterversion', 'filter_wiris') . '&nbsp;' . $filterversion;
        $reporttext .= "<br>" . get_string('wirispluginfor', 'filter_wiris') . '&nbsp;' .  $currenteditordata['plugin_name'] .
            '&nbsp;' . get_string('version', 'filter_wiris') . ' = ' . $pluginversion;
        $condition = false;
    }

    // Return the HTML output as a string.
    return html_writer::tag('tr', wrs_createtablerow($testname, $reporttext, $solutionlink, $condition), ['class' => 'wrs_plugin wrs_filter']);
}
