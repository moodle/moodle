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
 * @since 2.0
 * @package    moodlecore
 * @subpackage plagiarism
 * @copyright  2010 Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

///// GENERIC PLAGIARISM FUNCTIONS ////////////////////////////////////////////////////

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
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
    foreach($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $output .= $plagiarismplugin->get_links($linkarray);
    }
    return $output;
}

/**
 * returns array of plagiarism details about specified file
 *
 * @param int $cmid
 * @param int $userid
 * @param object $file moodle file object
 * @return array - sets of details about specified file, one array of details per plagiarism plugin
 *  - each set contains at least 'analyzed', 'score', 'reporturl'
 */
function plagiarism_get_file_results($cmid, $userid, $file) {
    global $CFG;
    $allresults = array();
    if (empty($CFG->enableplagiarism)) {
        return $allresults;
    }
    $plagiarismplugins = plagiarism_load_available_plugins();
    foreach($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $allresults[] = $plagiarismplugin->get_file_results($cmid, $userid, $file);
    }
    return $allresults;
}

/**
 * saves/updates plagiarism settings from a modules config page - called by course/modedit.php
 *
 * @param object $data - form data
 */
function plagiarism_save_form_elements($data) {
    global $CFG;
    if (empty($CFG->enableplagiarism)) {
        return '';
    }
    $plagiarismplugins = plagiarism_load_available_plugins();
    foreach($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $plagiarismplugin->save_form_elements($data);
    }
}

/**
 * adds the list of plagiarism settings to a form - called inside modules that have enabled plagiarism
 *
 * @param object $mform - Moodle form object
 * @param object $context - context object
 * @param string $modulename - Name of the module
 */
function plagiarism_get_form_elements_module($mform, $context, $modulename = "") {
    global $CFG;
    if (empty($CFG->enableplagiarism)) {
        return '';
    }
    $plagiarismplugins = plagiarism_load_available_plugins();
    foreach($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $plagiarismplugin->get_form_elements_module($mform, $context, $modulename);
    }
}
/**
 * updates the status of all files within a module
 *
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
    foreach($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
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
    foreach($plagiarismplugins as $plugin => $dir) {
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $output .= $plagiarismplugin->print_disclosure($cmid);
    }
    return $output;
}
/**
 * used by admin/cron.php to get similarity scores from submitted files.
 *
 */
function plagiarism_cron() {
    global $CFG;
    if (empty($CFG->enableplagiarism)) {
        return '';
    }
    $plagiarismplugins = plagiarism_load_available_plugins();
    foreach($plagiarismplugins as $plugin => $dir) {
        mtrace('Processing cron function for plagiarism_plugin_' . $plugin . '...', '');
        cron_trace_time_and_memory();
        require_once($dir.'/lib.php');
        $plagiarismclass = "plagiarism_plugin_$plugin";
        $plagiarismplugin = new $plagiarismclass;
        $plagiarismplugin->cron();
    }
}
/**
 * helper function - also loads lib file of plagiarism plugin
 * @return array of available plugins
 */
function plagiarism_load_available_plugins() {
    global $CFG;
    if (empty($CFG->enableplagiarism)) {
        return array();
    }
    $plagiarismplugins = get_plugin_list('plagiarism');
    $availableplugins = array();
    foreach($plagiarismplugins as $plugin => $dir) {
        //check this plugin is enabled and a lib file exists.
        if (get_config('plagiarism', $plugin."_use") && file_exists($dir."/lib.php")) {
            require_once($dir.'/lib.php');
            $plagiarismclass = "plagiarism_plugin_$plugin";
            if (class_exists($plagiarismclass)) {
                $availableplugins[$plugin] = $dir;
            }
        }
    }
    return $availableplugins;
}
