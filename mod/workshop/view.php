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
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id     = optional_param('id', 0, PARAM_INT); // course_module ID, or
$w      = optional_param('w', 0, PARAM_INT);  // workshop instance ID
$edit   = optional_param('edit', null, PARAM_BOOL);

if ($id) {
    $cm         = get_coursemodule_from_id('workshop', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $workshop   = $DB->get_record('workshop', array('id' => $w), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $workshop->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('workshop', $workshop->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);
require_capability('mod/workshop:view', $PAGE->context);
add_to_log($course->id, 'workshop', 'view', 'view.php?id=' . $cm->id, $workshop->name, $cm->id);

$workshop = new workshop($workshop, $cm, $course);

if (!is_null($edit) && $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

$PAGE->set_url($workshop->view_url());
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);

// todo 
$buttons = array();
if ($PAGE->user_allowed_editing()) {
    $editblocks                 = new html_form();
    $editblocks->method         = 'get';
    $editblocks->button->text   = get_string($PAGE->user_is_editing() ? 'blockseditoff' : 'blocksediton');
    $editblocks->url            = new moodle_url($PAGE->url, array('edit' => $PAGE->user_is_editing() ? 'off' : 'on'));
    $buttons[] = $OUTPUT->button($editblocks);
}
$buttons[] = $OUTPUT->update_module_button($cm->id, 'workshop');
$PAGE->set_button(implode('', $buttons));

$wsoutput = $PAGE->theme->get_renderer('mod_workshop', $PAGE);

/// Output starts here

echo $OUTPUT->header();
include(dirname(__FILE__) . '/tabs.php');
echo $OUTPUT->heading(format_string($workshop->name), 2);
echo $wsoutput->user_plan($workshop->prepare_user_plan($USER->id, $PAGE->context));

switch ($workshop->phase) {
case workshop::PHASE_SETUP:
    if (trim(strip_tags($workshop->intro))) {
        echo $OUTPUT->box(format_module_intro('workshop', $workshop, $workshop->cm->id), 'generalbox', 'intro');
    }
    break;
case workshop::PHASE_SUBMISSION:
    if (trim(strip_tags($workshop->instructauthors))) {
        $instructions = file_rewrite_pluginfile_urls($workshop->instructauthors, 'pluginfile.php', $PAGE->context->id,
            'workshop_instructauthors', 0, workshop::instruction_editors_options($PAGE->context));
        echo $OUTPUT->box(format_text($instructions, $workshop->instructauthorsformat), array('generalbox', 'instructions'));
    }
    if (has_capability('mod/workshop:submit', $PAGE->context)) {
        if ($submission = $workshop->get_submission_by_author($USER->id)) {
            echo $OUTPUT->box_start('generalbox mysubmission');
            echo $wsoutput->submission_summary($submission, true);
            if ($workshop->submitting_allowed()) {
                $editbutton                 = new html_form();
                $editbutton->method         = 'get';
                $editbutton->button->text   = get_string('editsubmission', 'workshop');
                $editbutton->url            = new moodle_url($workshop->submission_url(), array('edit' => 'on', 'id' => $submission->id));
                echo $OUTPUT->button($editbutton);
            }
            echo $OUTPUT->box_end();
        }
    }
    if (has_capability('mod/workshop:viewallsubmissions', $PAGE->context)) {
        $shownames = has_capability('mod/workshop:viewauthornames', $PAGE->context);
        echo $OUTPUT->box_start('generalbox allsubmissions');
        if (! $submissions = $workshop->get_submissions('all')) {
            echo $OUTPUT->container(get_string('nosubmissions', 'workshop'), 'nosubmissions');
        }
        foreach ($submissions as $submission) {
            echo $wsoutput->submission_summary($submission, $shownames);
        }
        echo $OUTPUT->box_end();
    }
    break;
case workshop::PHASE_ASSESSMENT:
    if (trim(strip_tags($workshop->instructreviewers))) {
        $instructions = file_rewrite_pluginfile_urls($workshop->instructreviewers, 'pluginfile.php', $PAGE->context->id,
            'workshop_instructreviewers', 0, workshop::instruction_editors_options($PAGE->context));
        echo $OUTPUT->box(format_text($instructions, $workshop->instructreviewersformat), array('generalbox', 'instructions'));
    }
    if (! $assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
        echo $OUTPUT->heading(get_string('assignedassessmentsnone', 'workshop'), 3);
    } else {
        echo $OUTPUT->heading(get_string('assignedassessments', 'workshop'), 3);
        $shownames = has_capability('mod/workshop:viewauthornames', $PAGE->context);
        foreach ($assessments as $assessment) {
            $submission = clone($assessment);
            $submission->id = $submission->submissionid;
            $submission->title = $submission->submissiontitle;
            $submission->timecreated = $submission->submissioncreated;
            $submission->timemodified = $submission->submissionmodified;
            if (is_null($assessment->grade)) {
                $class      = ' notgraded';
                $givengrade = get_string('nogradeyet', 'workshop');
                $buttontext = get_string('assess', 'workshop');
            } else {
                $class      = ' graded';
                // todo format grade
                $givengrade = $assessment->grade;
                $buttontext = get_string('reassess', 'workshop');
            }
            echo $OUTPUT->box_start('generalbox assessment-summary' . $class);
            echo $wsoutput->submission_summary($submission, $shownames);
            echo get_string('givengrade', 'workshop', $givengrade);
            $button = new html_form();
            $button->method         = 'get';
            $button->button->text   = $buttontext;
            $button->url            = $workshop->assess_url($assessment->id);
            echo $OUTPUT->button($button);
            echo $OUTPUT->box_end();
        }
    }
    break;
case workshop::PHASE_EVALUATION:
    $pagingvar  = 'page';
    $page       = optional_param($pagingvar, 0, PARAM_INT);
    $perpage    = 10;           // todo let the user modify this
    $groups     = '';           // todo let the user choose the group
    $sortby     = 'totalgrade';   // todo let the user choose the column to sort by
    $sorthow    = 'DESC';        // todo detto

    $data = $workshop->prepare_grading_report($USER->id, $groups, $page, $perpage, $sortby, $sorthow);
    if ($data) {
        $showauthornames    = has_capability('mod/workshop:viewauthornames', $PAGE->context);
        $showreviewernames  = has_capability('mod/workshop:viewreviewernames', $PAGE->context);

        if (has_capability('mod/workshop:overridegrades', $PAGE->context)) {
            $form = new html_form();
            $form->url = $workshop->aggregate_url();
            $form->button = new html_button();
            $form->button->text = get_string('aggregategrades', 'workshop');
            $form->method = 'post';

            $helpicon = new moodle_help_icon();
            $helpicon->page = 'aggregate';
            $helpicon->text = get_string('aggregategrades', 'workshop');
            $helpicon->module = 'workshop';

            echo $OUTPUT->box_start('buttonsbar');
            echo $OUTPUT->box($OUTPUT->button($form) . $OUTPUT->help_icon($helpicon), 'buttonwithhelp');
            echo $OUTPUT->box_end();
        }

        // prepare paging bar
        $pagingbar              = new moodle_paging_bar();
        $pagingbar->totalcount  = $data->totalcount;
        $pagingbar->page        = $page;
        $pagingbar->perpage     = $perpage;
        $pagingbar->baseurl     = $PAGE->url;
        $pagingbar->pagevar     = $pagingvar;

        echo $OUTPUT->paging_bar($pagingbar);
        echo $wsoutput->grading_report($data, $showauthornames, $showreviewernames);
        echo $OUTPUT->paging_bar($pagingbar);
    }
    break;
case workshop::PHASE_CLOSED:
default:
}

echo $OUTPUT->footer();
