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
 * Prints the intro page particular instance of a hotpot
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id       = optional_param('id', 0, PARAM_INT); // hotpot_attempts id
$attempt  = $DB->get_record('hotpot_attempts', array('id' => $id), '*', MUST_EXIST);
$hotpot   = $DB->get_record('hotpot', array('id' => $attempt->hotpotid), '*', MUST_EXIST);
$course   = $DB->get_record('course', array('id' => $hotpot->course), '*', MUST_EXIST);
$cm       = get_coursemodule_from_instance('hotpot', $hotpot->id, $course->id, false, MUST_EXIST);

// Check login
require_login($course, true, $cm);
if (! has_capability('mod/hotpot:reviewallattempts', $PAGE->context)) {
    require_capability('mod/hotpot:reviewmyattempts', $PAGE->context);
}

// Create an object to represent this attempt at the current HotPot activity
$hotpot = hotpot::create($hotpot, $cm, $course, $PAGE->context, $attempt);

// Log this request
hotpot_add_to_log($course->id, 'hotpot', 'review', 'view.php?id='.$cm->id, $hotpot->id, $cm->id);

// Set editing mode
if ($PAGE->user_allowed_editing()) {
    hotpot::set_user_editing();
}

// initialize $PAGE (and compute blocks)
$PAGE->set_url($hotpot->review_url());
$PAGE->set_title($hotpot->name);
$PAGE->set_heading($course->fullname);

// get renderer subtype (e.g. attempt_hp_6_jcloze_xml)
// and load the appropriate storage class for this attempt
$subtype = $hotpot->get_attempt_renderer_subtype();
$subdir = str_replace('_', '/', $subtype);
require_once($CFG->dirroot.'/mod/hotpot/'.$subdir.'/review.php');

// create the renderer for this attempt
$output = $PAGE->get_renderer('mod_hotpot');

////////////////////////////////////////////////////////////////////////////////
// Output starts here                                                         //
////////////////////////////////////////////////////////////////////////////////

echo $output->header();

echo $output->heading($hotpot);

echo $output->box_start('generalbox boxaligncenter boxwidthwide');

// show the attempt review page
// use call_user_func() to prevent syntax error in PHP 5.2.x
$class = 'mod_hotpot_'.$subtype.'_review';
echo call_user_func(array($class, 'review'), $hotpot, $class);

echo $output->box_end();

echo $output->continue_button($hotpot->report_url());

echo $output->footer();
