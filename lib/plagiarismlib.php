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
 * plagiarismlib.php - Contains core Plagiarism related functions.
 *
 * @since Moodle 2.0
 * @package    core
 * @subpackage plagiarism
 * @copyright  2010 Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

/**
 * displays the similarity score and provides a link to the full report if allowed.
 *
 * @param array  $linkarray contains all relevant information for the plugin to generate a link
 * @return string - url to allow login/viewing of a similarity report
 */
function plagiarism_get_links($linkarray) {
    global $CFG;
    if (empty($CFG->enableplagiarism)) {
        return '';
    }
    $plagiarismplugins = plagiarism_load_available_plugins();
    $output = '';
    foreach ($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $output .= $plagiarismplugin->get_links($linkarray);
    }
    if (!empty($output)) {
        return html_writer::span($output, 'core_plagiarism_links');
    }
    return '';
}

/**
 * Function that prints the student disclosure notifying that the files will be checked for plagiarism
 * @param integer $cmid - the cmid of this module
 * @return string
 */
function plagiarism_print_disclosure($cmid) {
    global $CFG;
    if (empty($CFG->enableplagiarism)) {
        return '';
    }
    $plagiarismplugins = plagiarism_load_available_plugins();
    $output = '';
    foreach ($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $output .= $plagiarismplugin->print_disclosure($cmid);
    }
    return $output;
}

/**
 * Helper function - also loads lib file of plagiarism plugin
 *
 * @return array of available plugins
 */
function plagiarism_load_available_plugins() {
    global $CFG;

    if (empty($CFG->enableplagiarism)) {
        return array();
    }
    $plagiarismplugins = core_component::get_plugin_list('plagiarism');
    $availableplugins = array();
    foreach ($plagiarismplugins as $plugin => $dir) {
        // Check this plugin is enabled and a lib file exists.
        $pluginenabled = get_config('plagiarism_'.$plugin, 'enabled');
        if ($pluginenabled && file_exists($dir."/lib.php")) {
            require_once($dir.'/lib.php');
            $plagiarismclass = "plagiarism_plugin_$plugin";
            if (class_exists($plagiarismclass)) {
                $availableplugins[$plugin] = $dir;
            }
        }
    }
    return $availableplugins;
}
