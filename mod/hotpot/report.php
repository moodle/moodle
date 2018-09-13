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
 * Display a list of HotPot activities with links to HotPot reports.
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id    = optional_param('id', 0, PARAM_INT); // course_module ID, or
$hp    = optional_param('hp', 0, PARAM_INT); // hotpot instance ID
$mode  = optional_param('mode', 'overview', PARAM_ALPHA); // type of report

if ($id) {
    $cm      = get_coursemodule_from_id('hotpot', $id, 0, false, MUST_EXIST);
    $course  = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $hotpot  = $DB->get_record('hotpot', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $hotpot  = $DB->get_record('hotpot', array('id' => $hp), '*', MUST_EXIST);
    $course  = $DB->get_record('course', array('id' => $hotpot->course), '*', MUST_EXIST);
    $cm      = get_coursemodule_from_instance('hotpot', $hotpot->id, $course->id, false, MUST_EXIST);
}

// check login
require_login($course, true, $cm);
if (! has_capability('mod/hotpot:reviewallattempts', $PAGE->context)) {
    require_capability('mod/hotpot:reviewmyattempts', $PAGE->context);
}

hotpot_add_to_log($course->id, 'hotpot', 'report', 'report.php?id='.$cm->id, $hotpot->id, $cm->id);

// Create an object to represent the current HotPot activity
$hotpot = hotpot::create($hotpot, $cm, $course, $PAGE->context);

// delete attempts, if requested
$action    = optional_param('action', '', PARAM_ALPHA);
$confirmed = optional_param('confirmed', 0, PARAM_INT);
if (function_exists('optional_param_array')) {
    $selected  = optional_param_array('selected', 0, PARAM_INT);
} else {
    $selected  = optional_param('selected', 0, PARAM_INT);
}

if ($action=='deleteselected') {
    require_sesskey();
    if ($confirmed) {
        $hotpot->delete_attempts($selected, false);
    } else {
        // show a confirm button ?
    }
}

$PAGE->set_url('/mod/hotpot/report.php', array('id' => $course->id, 'mode' => $mode));
$PAGE->set_title($hotpot->name);
$PAGE->set_heading($course->shortname);
$PAGE->navbar->add(get_string('report', 'quiz'));
if ($mode) {
    $PAGE->navbar->add(get_string($mode.'report', 'mod_hotpot'));
}

// get renderer subtype (e.g. report_overview)
// and load the appropriate renderer class for this attempt
$subtype = $hotpot->get_report_renderer_subtype($mode);
$subdir = str_replace('_', '/', $subtype);
require_once($CFG->dirroot.'/mod/hotpot/'.$subdir.'/renderer.php');

// create the renderer for this attempt
$output = $PAGE->get_renderer('mod_hotpot', $subtype);

////////////////////////////////////////////////////////////////////////////////
// Output starts here                                                         //
////////////////////////////////////////////////////////////////////////////////

echo $output->render_report($hotpot);
