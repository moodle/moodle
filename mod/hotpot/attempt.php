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
 * Display an attempt page of a hotpot activity
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id  = optional_param('id', 0, PARAM_INT); // course_module ID, or
$hp  = optional_param('hp', 0, PARAM_INT); // hotpot instance ID

if ($id) {
    $cm      = get_coursemodule_from_id('hotpot', $id, 0, false, MUST_EXIST);
    $course  = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $hotpot  = $DB->get_record('hotpot', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $hotpot  = $DB->get_record('hotpot', array('id' => $hp), '*', MUST_EXIST);
    $course  = $DB->get_record('course', array('id' => $hotpot->course), '*', MUST_EXIST);
    $cm      = get_coursemodule_from_instance('hotpot', $hotpot->id, $course->id, false, MUST_EXIST);
}

// Check login
require_login($course, true, $cm);
require_capability('mod/hotpot:attempt', $PAGE->context);
hotpot_add_to_log($course->id, 'hotpot', 'attempt', 'view.php?id='.$cm->id, $hotpot->id, $cm->id);

// Set editing mode
if ($PAGE->user_allowed_editing()) {
    hotpot::set_user_editing();
}

// Create an object to represent the current HotPot activity
$hotpot = hotpot::create($hotpot, $cm, $course, $PAGE->context);

// initialize $PAGE (and compute blocks)
$PAGE->set_url($hotpot->attempt_url());
$PAGE->set_title($hotpot->name);
$PAGE->set_heading($course->fullname);

// allow the HotPot activity to set its preferred page layout
$hotpot->set_preferred_pagelayout($PAGE);

/// Create an object to manage all the other (non-roles) access rules.
//$timenow = time();
//$canignoretimelimits = has_capability('mod/hotpot:ignoretimelimits', $PAGE->context, null, false);
//$accessmanager = new hotpot_access_manager($hotpot, $timenow, $canignoretimelimits);
//$messages = $accessmanager->describe_rules();


// get renderer subtype (e.g. attempt_hp_6_jcloze_xml)
// and load the appropriate renderer class for this attempt
if (! $subtype = $hotpot->get_attempt_renderer_subtype()) {
    echo $OUTPUT->header();
    echo get_string('unrecognizedsourcefile', 'mod_hotpot', $hotpot->sourcefile);
    echo $OUTPUT->footer();
    exit;
}

$subdir = str_replace('_', '/', $subtype);
require_once($CFG->dirroot.'/mod/hotpot/'.$subdir.'/renderer.php');

// create the renderer for this attempt
$output = $PAGE->get_renderer('mod_hotpot', $subtype);

// print access warnings, if required
if ($warnings = $output->entrywarnings($hotpot)) {
    echo $output->header();
    echo $warnings;
    echo $output->footer();
    exit;
}

////////////////////////////////////////////////////////////////////////////////
// Output starts here                                                         //
////////////////////////////////////////////////////////////////////////////////

echo $output->render_attempt($hotpot);
