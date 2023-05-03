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
 * @param object  $linkarray contains all relevant information for the plugin to generate a link
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
 * returns array of plagiarism details about specified file
 *
 * @deprecated Since Moodle 4.0. - this function was a placeholder and not used in core.
 * @todo MDL-71326 This is to be moved from here to deprecatedlib.php in Moodle 4.4
 * @param int $cmid
 * @param int $userid
 * @param object $file moodle file object
 * @return array - sets of details about specified file, one array of details per plagiarism plugin
 *  - each set contains at least 'analyzed', 'score', 'reporturl'
 */
function plagiarism_get_file_results($cmid, $userid, $file) {
    global $CFG;
    $text = 'plagiarism_get_file_results is deprecated, please use plagiarism_get_links() or plugin specific functions.';
    debugging($text, DEBUG_DEVELOPER);
    $allresults = array();
    if (empty($CFG->enableplagiarism)) {
        return $allresults;
    }
    $plagiarismplugins = plagiarism_load_available_plugins();
    foreach ($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $allresults[] = $plagiarismplugin->get_file_results($cmid, $userid, $file);
    }
    return $allresults;
}

/**
 * Allows a plagiarism plugin to print a button/link at the top of activity overview report pages.
 *
 * @deprecated Since Moodle 4.0 - Please use {plugin name}_before_standard_top_of_body_html instead.
 * @todo MDL-71326 Remove this method.
 * @param object $course - full Course object
 * @param object $cm - full cm object
 * @return string
 */
function plagiarism_update_status($course, $cm) {
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

        $reflectionmethod = new ReflectionMethod($plagiarismplugin, 'update_status');
        if ($reflectionmethod->getDeclaringClass()->getName() == get_class($plagiarismplugin)) {
            $text = 'plagiarism_plugin::update_status() is deprecated.';
            $text .= ' Use plagiarism_' . $plugin . '_before_standard_top_of_body_html() instead';
            debugging($text, DEBUG_DEVELOPER);
        }
        $output .= $plagiarismplugin->update_status($course, $cm);
    }
    return $output;
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
