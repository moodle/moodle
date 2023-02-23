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
 * Prints a particular instance of workshop
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

$id         = optional_param('id', 0, PARAM_INT); // course_module ID, or
$w          = optional_param('w', 0, PARAM_INT);  // workshop instance ID
$editmode   = optional_param('editmode', null, PARAM_BOOL);
$page       = optional_param('page', 0, PARAM_INT);
$perpage    = optional_param('perpage', null, PARAM_INT);
$sortby     = optional_param('sortby', 'lastname', PARAM_ALPHA);
$sorthow    = optional_param('sorthow', 'ASC', PARAM_ALPHA);
$eval       = optional_param('eval', null, PARAM_PLUGIN);

if ($id) {
    $cm             = get_coursemodule_from_id('workshop', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $workshoprecord = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $workshoprecord = $DB->get_record('workshop', array('id' => $w), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $workshoprecord->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('workshop', $workshoprecord->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);
require_capability('mod/workshop:view', $PAGE->context);

$workshop = new workshop($workshoprecord, $cm, $course);

$PAGE->set_url($workshop->view_url());

// Mark viewed.
$workshop->set_module_viewed();

// If the phase is to be switched, do it asap. This just has to happen after triggering
// the event so that the scheduled allocator had a chance to allocate submissions.
if ($workshop->phase == workshop::PHASE_SUBMISSION and $workshop->phaseswitchassessment
        and $workshop->submissionend > 0 and $workshop->submissionend < time()) {
    $workshop->switch_phase(workshop::PHASE_ASSESSMENT);
    // Disable the automatic switching now so that it is not executed again by accident
    // if the teacher changes the phase back to the submission one.
    $DB->set_field('workshop', 'phaseswitchassessment', 0, array('id' => $workshop->id));
    $workshop->phaseswitchassessment = 0;
}

if (!is_null($editmode) && $PAGE->user_allowed_editing()) {
    $USER->editing = $editmode;
}
$workshop->init_initial_bar();
$userplan = new workshop_user_plan($workshop, $USER->id);

foreach ($userplan->phases as $phase) {
    if ($phase->active) {
        $currentphasetitle = $phase->title;
    }
}

$PAGE->set_title($workshop->name . " (" . $currentphasetitle . ")");
$PAGE->set_heading($course->fullname);

if ($perpage and $perpage > 0 and $perpage <= 1000) {
    require_sesskey();
    set_user_preference('workshop_perpage', $perpage);
    redirect($PAGE->url);
}

if ($eval) {
    require_sesskey();
    require_capability('mod/workshop:overridegrades', $workshop->context);
    $workshop->set_grading_evaluation_method($eval);
    redirect($PAGE->url);
}

$heading = $OUTPUT->heading_with_help(format_string($workshop->name), 'userplan', 'workshop');
$heading = preg_replace('/<h2[^>]*>([.\s\S]*)<\/h2>/', '$1', $heading);
$PAGE->activityheader->set_attrs([
    'title' => $PAGE->activityheader->is_title_allowed() ? $heading : "",
    'description' => ''
]);

$output = $PAGE->get_renderer('mod_workshop');

// Output starts here.

echo $output->header();

echo $output->view_page($workshop, $userplan, $currentphasetitle, $page, $sortby, $sorthow);

$PAGE->requires->js_call_amd('mod_workshop/workshopview', 'init');
echo $output->footer();
