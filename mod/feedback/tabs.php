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
 * prints the tabbed bar
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */
defined('MOODLE_INTERNAL') OR die('not allowed');

$tabs = array();
$row  = array();
$inactive = array();
$activated = array();

//some pages deliver the cmid instead the id
if (isset($cmid) AND intval($cmid) AND $cmid > 0) {
    $usedid = $cmid;
} else {
    $usedid = $id;
}

$context = context_module::instance($usedid);

$courseid = optional_param('courseid', false, PARAM_INT);
// $current_tab = $SESSION->feedback->current_tab;
if (!isset($current_tab)) {
    $current_tab = '';
}

$viewurl = new moodle_url('/mod/feedback/view.php', array('id' => $usedid));
$row[] = new tabobject('view', $viewurl->out(), get_string('overview', 'feedback'));

if (has_capability('mod/feedback:edititems', $context)) {
    $editurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$usedid, 'do_show'=>'edit'));
    $row[] = new tabobject('edit', $editurl->out(), get_string('edit_items', 'feedback'));

    $templateurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$usedid, 'do_show'=>'templates'));
    $row[] = new tabobject('templates', $templateurl->out(), get_string('templates', 'feedback'));
}

if ($feedback->course == SITEID && has_capability('mod/feedback:mapcourse', $context)) {
    $mapurl = new moodle_url('/mod/feedback/mapcourse.php', array('id' => $usedid));
    $row[] = new tabobject('mapcourse', $mapurl->out(), get_string('mappedcourses', 'feedback'));
}

if (has_capability('mod/feedback:viewreports', $context)) {
    if ($feedback->course == SITEID) {
        $url_params = array('id' => $usedid, 'courseid' => $courseid);
        $analysisurl = new moodle_url('/mod/feedback/analysis_course.php', $url_params);
        $row[] = new tabobject('analysis',
                                $analysisurl->out(),
                                get_string('analysis', 'feedback'));

    } else {
        $url_params = array('id' => $usedid);
        $analysisurl = new moodle_url('/mod/feedback/analysis.php', $url_params);
        $row[] = new tabobject('analysis',
                                $analysisurl->out(),
                                get_string('analysis', 'feedback'));
    }

    $url_params = array('id' => $usedid);
    $reporturl = new moodle_url('/mod/feedback/show_entries.php', $url_params);
    $row[] = new tabobject('showentries',
                            $reporturl->out(),
                            get_string('show_entries', 'feedback'));

    if ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO AND $feedback->course != SITEID) {
        $nonrespondenturl = new moodle_url('/mod/feedback/show_nonrespondents.php', array('id'=>$usedid));
        $row[] = new tabobject('nonrespondents',
                                $nonrespondenturl->out(),
                                get_string('show_nonrespondents', 'feedback'));
    }
}

if (count($row) > 1) {
    $tabs[] = $row;

    print_tabs($tabs, $current_tab, $inactive, $activated);
}

