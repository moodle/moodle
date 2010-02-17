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
        if ($workshop->grading_strategy_instance()->form_ready()) {
            if (! $examples = $workshop->get_examples_for_manager()) {
                echo $OUTPUT->container(get_string('noexamples', 'workshop'), 'noexamples');
            }
            foreach ($examples as $example) {
                $summary = $workshop->prepare_example_summary($example);
                echo $wsoutput->example_summary($summary);
            }
            $aurl = new moodle_url($workshop->exsubmission_url(0), array('edit' => 'on'));
            echo $OUTPUT->single_button($aurl, get_string('exampleadd', 'workshop'), 'get');
        } else {
            echo $OUTPUT->container(get_string('noexamplesformready', 'workshop'));
        }
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

    $examplesdone = true;
    if ($workshop->assessing_examples_allowed()
            and has_capability('mod/workshop:submit', $workshop->context)
                    and ! has_capability('mod/workshop:manageexamples', $workshop->context)) {
        $examples = $workshop->get_examples_for_reviewer($USER->id);
        $total = count($examples);
        $done = 0;
        $todo = 0;
        // make sure the current user has all examples allocated
        foreach ($examples as $exampleid => $example) {
            if (is_null($example->assessmentid)) {
                $examples[$exampleid]->assessmentid = $workshop->add_allocation($example, $USER->id, false, 0);
            }
            if (is_null($example->grade)) {
                $todo++;
            } else {
                $done++;
            }
        }
        if ($todo > 0 and $workshop->examplesmode != workshop::EXAMPLES_VOLUNTARY) {
            $examplesdone = false;
        }
        print_collapsible_region_start('', 'workshop-viewlet-examples', get_string('exampleassessments', 'workshop'));
        echo $OUTPUT->box_start('generalbox exampleassessments');
        if ($total == 0) {
            echo $OUTPUT->heading(get_string('noexamples', 'workshop'), 3);
        } else {
            foreach ($examples as $example) {
                $summary = $workshop->prepare_example_summary($example);
                echo $wsoutput->example_summary($summary);
            }
        }
        echo $OUTPUT->box_end();
        print_collapsible_region_end();
    }

    if ($workshop->submitting_allowed()
                and has_capability('mod/workshop:submit', $PAGE->context)
                        and $examplesdone) {
        print_collapsible_region_start('', 'workshop-viewlet-ownsubmission', get_string('yoursubmission', 'workshop'));
        echo $OUTPUT->box_start('generalbox ownsubmission');
        if ($submission = $workshop->get_submission_by_author($USER->id)) {
            echo $wsoutput->submission_summary($submission, true);
        } else {
            echo $OUTPUT->container(get_string('noyoursubmission', 'workshop'));
        }
        if ($workshop->submitting_allowed()) {
            $aurl = new moodle_url($workshop->submission_url(), array('edit' => 'on'));
            echo $OUTPUT->single_button($aurl, get_string('editsubmission', 'workshop'), 'get');
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
        $PAGE->set_url($PAGE->url, compact('sortby', 'sorthow', 'page')); // TODO: this is suspicious
        $data = $workshop->prepare_grading_report($USER->id, $groups, $page, $perpage, $sortby, $sorthow);
        if ($data) {
            $showauthornames    = has_capability('mod/workshop:viewauthornames', $workshop->context);
            $showreviewernames  = has_capability('mod/workshop:viewreviewernames', $workshop->context);

            // prepare paging bar
            $pagingbar              = new paging_bar($data->totalcount, $page, $perpage, $PAGE->url, 'page');

            // grading report display options
            $reportopts                         = new stdclass();
            $reportopts->showauthornames        = $showauthornames;
            $reportopts->showreviewernames      = $showreviewernames;
            $reportopts->sortby                 = $sortby;
            $reportopts->sorthow                = $sorthow;
            $reportopts->showsubmissiongrade    = false;
            $reportopts->showgradinggrade       = false;

            echo $OUTPUT->render($pagingbar);
            echo $wsoutput->grading_report($data, $reportopts);
            echo $OUTPUT->render($pagingbar);
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
            $submission                     = new stdclass();
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
            $aurl = $workshop->assess_url($assessment->id);
            echo $OUTPUT->single_button($aurl, $buttontext, 'get');
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
        $PAGE->set_url($PAGE->url, compact('sortby', 'sorthow', 'page')); // TODO: this is suspicious
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
            $pagingbar              = new paging_bar($data->totalcount, $page, $perpage, $PAGE->url, 'page');

            // grading report display options
            $reportopts                         = new stdclass();
            $reportopts->showauthornames        = $showauthornames;
            $reportopts->showreviewernames      = $showreviewernames;
            $reportopts->sortby                 = $sortby;
            $reportopts->sorthow                = $sorthow;
            $reportopts->showsubmissiongrade    = true;
            $reportopts->showgradinggrade       = true;

            echo $OUTPUT->render($pagingbar);
            echo $wsoutput->grading_report($data, $reportopts);
            echo $OUTPUT->render($pagingbar);
        }
    }
    break;
case workshop::PHASE_CLOSED:
    break;
default:
}

echo $OUTPUT->footer();
