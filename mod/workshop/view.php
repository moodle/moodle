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

$id         = optional_param('id', 0, PARAM_INT); // course_module ID, or
$w          = optional_param('w', 0, PARAM_INT);  // workshop instance ID
$editmode   = optional_param('editmode', null, PARAM_BOOL);

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

if (!is_null($editmode) && $PAGE->user_allowed_editing()) {
    $USER->editing = $editmode;
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
    $editblocks->url            = new moodle_url($PAGE->url, array('editmode' => $PAGE->user_is_editing() ? 'off' : 'on'));
    $buttons[] = $OUTPUT->button($editblocks);
}
$buttons[] = $OUTPUT->update_module_button($cm->id, 'workshop');
$PAGE->set_button(implode('', $buttons));

$wsoutput = $PAGE->get_renderer('mod_workshop');

/// Output starts here

echo $OUTPUT->header();
include(dirname(__FILE__) . '/tabs.php');
echo $OUTPUT->heading(format_string($workshop->name), 2);
echo $wsoutput->user_plan($workshop->prepare_user_plan($USER->id, $PAGE->context));

switch ($workshop->phase) {
case workshop::PHASE_SETUP:
    if (trim(strip_tags($workshop->intro))) {
        print_collapsible_region_start('', 'workshop-viewlet-intro', get_string('introduction', 'workshop'));
        echo $OUTPUT->box(format_module_intro('workshop', $workshop, $workshop->cm->id), 'generalbox');
        print_collapsible_region_end();
    }
    if ($workshop->useexamples and has_capability('mod/workshop:manageexamples', $PAGE->context)) {
        print_collapsible_region_start('', 'workshop-viewlet-allexamples', get_string('examplesubmissions', 'workshop'));
        echo $OUTPUT->box_start('generalbox examples');
        echo $OUTPUT->heading(get_string('examplesubmissions', 'workshop'), 3);
        if (! $examples = $workshop->get_examples()) {
            echo $OUTPUT->container(get_string('noexamples', 'workshop'), 'noexamples');
        }
        foreach ($examples as $example) {
            echo $wsoutput->example_summary($example);
        }
        $editbutton                 = new html_form();
        $editbutton->method         = 'get';
        $editbutton->button->text   = get_string('exampleadd', 'workshop');
        $editbutton->url            = new moodle_url($workshop->exsubmission_url(0), array('edit' => 'on'));
        echo $OUTPUT->button($editbutton);
        echo $OUTPUT->box_end();
    }
    break;
case workshop::PHASE_SUBMISSION:
    if (trim(strip_tags($workshop->instructauthors))) {
        $instructions = file_rewrite_pluginfile_urls($workshop->instructauthors, 'pluginfile.php', $PAGE->context->id,
            'workshop_instructauthors', 0, workshop::instruction_editors_options($PAGE->context));
        print_collapsible_region_start('', 'workshop-viewlet-instructauthors', get_string('instructauthors', 'workshop'));
        echo $OUTPUT->box(format_text($instructions, $workshop->instructauthorsformat), array('generalbox', 'instructions'));
        print_collapsible_region_end();
    }
    //print_collapsible_region_start('', 'workshop-viewlet-examples', get_string('hideshow', 'workshop'));
    //echo 'Hello';
    //print_collapsible_region_end();
    /* todo pseudocode follows
    if ($workshop->useexamples) {
        if (examples are voluntary) {
            submitting is allowed
            assessing is allowed
            display the example assessment tool - just offer the posibility to train assessment
        }
        if (examples must be done before submission) {
            if (student assessed all example submissions) {
                submitting is allowed
                assessing is allowed
                display - let the student to reassess to train
            } else {
                submitting is not allowed
                assessing is not allowed
                display - force student to assess the examples
            }
        }


        // the following goes into the next PHASE
        if (examples must be done before assessment) {
            if (student assessed all example submissions) {
                assessing is allowed
                let the student to optionally reassess to train
            } else {
                assessing is not allowed
                force student to assess the examples
            }
        }
    }

    */
    if ($workshop->useexamples and $workshop->examplesmode == workshop::EXAMPLES_BEFORE_SUBMISSION) {
        if (has_capability('mod/workshop:manageexamples', $workshop->context)) {
            // todo what is teacher expected to see here? some stats probably...
        }
        if (has_capability('mod/workshop:peerassess', $workshop->context)) {

        }
    }
    if (has_capability('mod/workshop:submit', $PAGE->context)) {
        print_collapsible_region_start('', 'workshop-viewlet-ownsubmission', get_string('yoursubmission', 'workshop'));
        echo $OUTPUT->box_start('generalbox ownsubmission');
        if ($submission = $workshop->get_submission_by_author($USER->id)) {
            echo $wsoutput->submission_summary($submission, true);
        } else {
            echo $OUTPUT->container(get_string('noyoursubmission', 'workshop'));
        }
        if ($workshop->submitting_allowed()) {
            $editbutton                 = new html_form();
            $editbutton->method         = 'get';
            $editbutton->button->text   = get_string('editsubmission', 'workshop');
            $editbutton->url            = new moodle_url($workshop->submission_url(), array('edit' => 'on'));
            echo $OUTPUT->button($editbutton);
        }
        echo $OUTPUT->box_end();
        print_collapsible_region_end();
    }
    if (has_capability('mod/workshop:viewallsubmissions', $PAGE->context)) {
        $shownames = has_capability('mod/workshop:viewauthornames', $PAGE->context);
        print_collapsible_region_start('', 'workshop-viewlet-allsubmissions', get_string('allsubmissions', 'workshop'));
        echo $OUTPUT->box_start('generalbox allsubmissions');
        if (! $submissions = $workshop->get_submissions('all')) {
            echo $OUTPUT->container(get_string('nosubmissions', 'workshop'), 'nosubmissions');
        }
        foreach ($submissions as $submission) {
            echo $wsoutput->submission_summary($submission, $shownames);
        }
        echo $OUTPUT->box_end();
        print_collapsible_region_end();
    }
    break;
case workshop::PHASE_ASSESSMENT:
    if (has_capability('mod/workshop:viewallassessments', $PAGE->context)) {
        $page       = optional_param('page', 0, PARAM_INT);
        $sortby     = optional_param('sortby', 'lastname', PARAM_ALPHA);
        $sorthow    = optional_param('sorthow', 'ASC', PARAM_ALPHA);
        $perpage    = 10;           // todo let the user modify this
        $groups     = '';           // todo let the user choose the group
        $PAGE->set_url(new moodle_url($PAGE->url, compact('sortby', 'sorthow', 'page')));
        $data = $workshop->prepare_grading_report($USER->id, $groups, $page, $perpage, $sortby, $sorthow);
        if ($data) {
            $showauthornames    = has_capability('mod/workshop:viewauthornames', $workshop->context);
            $showreviewernames  = has_capability('mod/workshop:viewreviewernames', $workshop->context);

            // prepare paging bar
            $pagingbar              = new moodle_paging_bar();
            $pagingbar->totalcount  = $data->totalcount;
            $pagingbar->page        = $page;
            $pagingbar->perpage     = $perpage;
            $pagingbar->baseurl     = $PAGE->url;
            $pagingbar->pagevar     = 'page';

            // grading report display options
            $reportopts                         = new stdClass();
            $reportopts->showauthornames        = $showauthornames;
            $reportopts->showreviewernames      = $showreviewernames;
            $reportopts->sortby                 = $sortby;
            $reportopts->sorthow                = $sorthow;
            $reportopts->showsubmissiongrade    = false;
            $reportopts->showgradinggrade       = false;

            echo $OUTPUT->paging_bar($pagingbar);
            echo $wsoutput->grading_report($data, $reportopts);
            echo $OUTPUT->paging_bar($pagingbar);
        }
    }
    if (trim(strip_tags($workshop->instructreviewers))) {
        $instructions = file_rewrite_pluginfile_urls($workshop->instructreviewers, 'pluginfile.php', $PAGE->context->id,
            'workshop_instructreviewers', 0, workshop::instruction_editors_options($PAGE->context));
        print_collapsible_region_start('', 'workshop-viewlet-instructreviewers', get_string('instructreviewers', 'workshop'));
        echo $OUTPUT->box(format_text($instructions, $workshop->instructreviewersformat), array('generalbox', 'instructions'));
        print_collapsible_region_end();
    }
    print_collapsible_region_start('', 'workshop-viewlet-assignedassessments', get_string('assignedassessments', 'workshop'));
    if (! $assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
        echo $OUTPUT->box_start('generalbox assessment-none');
        echo $OUTPUT->heading(get_string('assignedassessmentsnone', 'workshop'), 3);
        echo $OUTPUT->box_end();
    } else {
        $shownames = has_capability('mod/workshop:viewauthornames', $PAGE->context);
        foreach ($assessments as $assessment) {
            $submission                     = new stdClass();
            $submission->id                 = $assessment->submissionid;
            $submission->title              = $assessment->submissiontitle;
            $submission->timecreated        = $assessment->submissioncreated;
            $submission->timemodified       = $assessment->submissionmodified;
            $submission->authorid           = $assessment->authorid;
            $submission->authorfirstname    = $assessment->authorfirstname;
            $submission->authorlastname     = $assessment->authorlastname;
            $submission->authorpicture      = $assessment->authorpicture;
            $submission->authorimagealt     = $assessment->authorimagealt;

            if (is_null($assessment->grade)) {
                $class      = ' notgraded';
                $status     = get_string('nogradeyet', 'workshop');
                $buttontext = get_string('assess', 'workshop');
            } else {
                $class      = ' graded';
                $status     = get_string('alreadygraded', 'workshop');
                $buttontext = get_string('reassess', 'workshop');
            }
            echo $OUTPUT->box_start('generalbox assessment-summary' . $class);
            echo $wsoutput->submission_summary($submission, $shownames);
            echo get_string('givengradestatus', 'workshop', $status);
            $button = new html_form();
            $button->method         = 'get';
            $button->button->text   = $buttontext;
            $button->url            = $workshop->assess_url($assessment->id);
            echo $OUTPUT->button($button);
            echo $OUTPUT->box_end();
        }
    }
    print_collapsible_region_end();
    break;
case workshop::PHASE_EVALUATION:
    if (has_capability('mod/workshop:viewallassessments', $PAGE->context)) {
        $page       = optional_param('page', 0, PARAM_INT);
        $sortby     = optional_param('sortby', 'lastname', PARAM_ALPHA);
        $sorthow    = optional_param('sorthow', 'ASC', PARAM_ALPHA);
        $perpage    = 10;           // todo let the user modify this
        $groups     = '';           // todo let the user choose the group
        $PAGE->set_url(new moodle_url($PAGE->url, compact('sortby', 'sorthow', 'page')));
        $data = $workshop->prepare_grading_report($USER->id, $groups, $page, $perpage, $sortby, $sorthow);
        if ($data) {
            $showauthornames    = has_capability('mod/workshop:viewauthornames', $workshop->context);
            $showreviewernames  = has_capability('mod/workshop:viewreviewernames', $workshop->context);

            if (has_capability('mod/workshop:overridegrades', $PAGE->context)) {
                // load the grading evaluator
                $evaluator = $workshop->grading_evaluation_instance();
                $form = $evaluator->get_settings_form(new moodle_url($workshop->aggregate_url(),
                        compact('sortby', 'sorthow', 'page')));
                $form->display();
            }

            // prepare paging bar
            $pagingbar              = new moodle_paging_bar();
            $pagingbar->totalcount  = $data->totalcount;
            $pagingbar->page        = $page;
            $pagingbar->perpage     = $perpage;
            $pagingbar->baseurl     = $PAGE->url;
            $pagingbar->pagevar     = 'page';

            // grading report display options
            $reportopts                         = new stdClass();
            $reportopts->showauthornames        = $showauthornames;
            $reportopts->showreviewernames      = $showreviewernames;
            $reportopts->sortby                 = $sortby;
            $reportopts->sorthow                = $sorthow;
            $reportopts->showsubmissiongrade    = true;
            $reportopts->showgradinggrade       = true;

            echo $OUTPUT->paging_bar($pagingbar);
            echo $wsoutput->grading_report($data, $reportopts);
            echo $OUTPUT->paging_bar($pagingbar);
        }
    }
    break;
case workshop::PHASE_CLOSED:
    break;
default:
}

echo $OUTPUT->footer();
