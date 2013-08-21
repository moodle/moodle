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
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id         = optional_param('id', 0, PARAM_INT); // course_module ID, or
$w          = optional_param('w', 0, PARAM_INT);  // workshop instance ID
$editmode   = optional_param('editmode', null, PARAM_BOOL);
$page       = optional_param('page', 0, PARAM_INT);
$perpage    = optional_param('perpage', null, PARAM_INT);
$sortby     = optional_param('sortby', 'lastname', PARAM_ALPHA);
$sorthow    = optional_param('sorthow', 'ASC', PARAM_ALPHA);
$eval       = optional_param('eval', null, PARAM_PLUGIN);

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

$workshop = new workshop($workshop, $cm, $course);
$workshop->log('view');

// Mark viewed
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Fire the event
events_trigger('workshop_viewed', (object)array(
    'workshop' => $workshop,
    'user' => $USER,
));

// If the phase is to be switched, do it asap. This just has to happen after triggering
// the event so that the scheduled allocator had a chance to allocate submissions.
if ($workshop->phase == workshop::PHASE_SUBMISSION and $workshop->phaseswitchassessment
        and $workshop->submissionend > 0 and $workshop->submissionend < time()) {
    $workshop->switch_phase(workshop::PHASE_ASSESSMENT);
    $workshop->log('update switch phase', $workshop->view_url(), $workshop->phase);
    // Disable the automatic switching now so that it is not executed again by accident
    // if the teacher changes the phase back to the submission one.
    $DB->set_field('workshop', 'phaseswitchassessment', 0, array('id' => $workshop->id));
    $workshop->phaseswitchassessment = 0;
}

if (!is_null($editmode) && $PAGE->user_allowed_editing()) {
    $USER->editing = $editmode;
}

$PAGE->set_url($workshop->view_url());
$PAGE->set_title($workshop->name);
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

$output = $PAGE->get_renderer('mod_workshop');
$userplan = new workshop_user_plan($workshop, $USER->id);

/// Output starts here

echo $output->header();
echo $output->heading_with_help(format_string($workshop->name), 'userplan', 'workshop');
echo $output->render($userplan);

switch ($workshop->phase) {
case workshop::PHASE_SETUP:
    if (trim($workshop->intro)) {
        print_collapsible_region_start('', 'workshop-viewlet-intro', get_string('introduction', 'workshop'));
        echo $output->box(format_module_intro('workshop', $workshop, $workshop->cm->id), 'generalbox');
        print_collapsible_region_end();
    }
    if ($workshop->useexamples and has_capability('mod/workshop:manageexamples', $PAGE->context)) {
        print_collapsible_region_start('', 'workshop-viewlet-allexamples', get_string('examplesubmissions', 'workshop'));
        echo $output->box_start('generalbox examples');
        if ($workshop->grading_strategy_instance()->form_ready()) {
            if (! $examples = $workshop->get_examples_for_manager()) {
                echo $output->container(get_string('noexamples', 'workshop'), 'noexamples');
            }
            foreach ($examples as $example) {
                $summary = $workshop->prepare_example_summary($example);
                $summary->editable = true;
                echo $output->render($summary);
            }
            $aurl = new moodle_url($workshop->exsubmission_url(0), array('edit' => 'on'));
            echo $output->single_button($aurl, get_string('exampleadd', 'workshop'), 'get');
        } else {
            echo $output->container(get_string('noexamplesformready', 'workshop'));
        }
        echo $output->box_end();
        print_collapsible_region_end();
    }
    break;
case workshop::PHASE_SUBMISSION:
    if (trim($workshop->instructauthors)) {
        $instructions = file_rewrite_pluginfile_urls($workshop->instructauthors, 'pluginfile.php', $PAGE->context->id,
            'mod_workshop', 'instructauthors', 0, workshop::instruction_editors_options($PAGE->context));
        print_collapsible_region_start('', 'workshop-viewlet-instructauthors', get_string('instructauthors', 'workshop'));
        echo $output->box(format_text($instructions, $workshop->instructauthorsformat, array('overflowdiv'=>true)), array('generalbox', 'instructions'));
        print_collapsible_region_end();
    }

    // does the user have to assess examples before submitting their own work?
    $examplesmust = ($workshop->useexamples and $workshop->examplesmode == workshop::EXAMPLES_BEFORE_SUBMISSION);

    // is the assessment of example submissions considered finished?
    $examplesdone = has_capability('mod/workshop:manageexamples', $workshop->context);
    if ($workshop->assessing_examples_allowed()
            and has_capability('mod/workshop:submit', $workshop->context)
                    and ! has_capability('mod/workshop:manageexamples', $workshop->context)) {
        $examples = $userplan->get_examples();
        $total = count($examples);
        $left = 0;
        // make sure the current user has all examples allocated
        foreach ($examples as $exampleid => $example) {
            if (is_null($example->assessmentid)) {
                $examples[$exampleid]->assessmentid = $workshop->add_allocation($example, $USER->id, 0);
            }
            if (is_null($example->grade)) {
                $left++;
            }
        }
        if ($left > 0 and $workshop->examplesmode != workshop::EXAMPLES_VOLUNTARY) {
            $examplesdone = false;
        } else {
            $examplesdone = true;
        }
        print_collapsible_region_start('', 'workshop-viewlet-examples', get_string('exampleassessments', 'workshop'), false, $examplesdone);
        echo $output->box_start('generalbox exampleassessments');
        if ($total == 0) {
            echo $output->heading(get_string('noexamples', 'workshop'), 3);
        } else {
            foreach ($examples as $example) {
                $summary = $workshop->prepare_example_summary($example);
                echo $output->render($summary);
            }
        }
        echo $output->box_end();
        print_collapsible_region_end();
    }

    if (has_capability('mod/workshop:submit', $PAGE->context) and (!$examplesmust or $examplesdone)) {
        print_collapsible_region_start('', 'workshop-viewlet-ownsubmission', get_string('yoursubmission', 'workshop'));
        echo $output->box_start('generalbox ownsubmission');
        if ($submission = $workshop->get_submission_by_author($USER->id)) {
            echo $output->render($workshop->prepare_submission_summary($submission, true));
            if ($workshop->modifying_submission_allowed($USER->id)) {
                $btnurl = new moodle_url($workshop->submission_url(), array('edit' => 'on'));
                $btntxt = get_string('editsubmission', 'workshop');
            }
        } else {
            echo $output->container(get_string('noyoursubmission', 'workshop'));
            if ($workshop->creating_submission_allowed($USER->id)) {
                $btnurl = new moodle_url($workshop->submission_url(), array('edit' => 'on'));
                $btntxt = get_string('createsubmission', 'workshop');
            }
        }
        if (!empty($btnurl)) {
            echo $output->single_button($btnurl, $btntxt, 'get');
        }
        echo $output->box_end();
        print_collapsible_region_end();
    }

    if (has_capability('mod/workshop:viewallsubmissions', $PAGE->context)) {
        $groupmode = groups_get_activity_groupmode($workshop->cm);
        $groupid = groups_get_activity_group($workshop->cm, true);

        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $workshop->context)) {
            $allowedgroups = groups_get_activity_allowed_groups($workshop->cm);
            if (empty($allowedgroups)) {
                echo $output->container(get_string('groupnoallowed', 'mod_workshop'), 'groupwidget error');
                break;
            }
            if (! in_array($groupid, array_keys($allowedgroups))) {
                echo $output->container(get_string('groupnotamember', 'core_group'), 'groupwidget error');
                break;
            }
        }

        $countsubmissions = $workshop->count_submissions('all', $groupid);
        $perpage = get_user_preferences('workshop_perpage', 10);
        $pagingbar = new paging_bar($countsubmissions, $page, $perpage, $PAGE->url, 'page');

        print_collapsible_region_start('', 'workshop-viewlet-allsubmissions', get_string('allsubmissions', 'workshop', $countsubmissions));
        echo $output->box_start('generalbox allsubmissions');
        echo $output->container(groups_print_activity_menu($workshop->cm, $PAGE->url, true), 'groupwidget');

        if ($countsubmissions == 0) {
            echo $output->container(get_string('nosubmissions', 'workshop'), 'nosubmissions');

        } else {
            $submissions = $workshop->get_submissions('all', $groupid, $page * $perpage, $perpage);
            $shownames = has_capability('mod/workshop:viewauthornames', $workshop->context);
            echo $output->render($pagingbar);
            foreach ($submissions as $submission) {
                echo $output->render($workshop->prepare_submission_summary($submission, $shownames));
            }
            echo $output->render($pagingbar);
            echo $output->perpage_selector($perpage);
        }

        echo $output->box_end();
        print_collapsible_region_end();
    }

    break;

case workshop::PHASE_ASSESSMENT:

    $ownsubmissionexists = null;
    if (has_capability('mod/workshop:submit', $PAGE->context)) {
        if ($ownsubmission = $workshop->get_submission_by_author($USER->id)) {
            print_collapsible_region_start('', 'workshop-viewlet-ownsubmission', get_string('yoursubmission', 'workshop'), false, true);
            echo $output->box_start('generalbox ownsubmission');
            echo $output->render($workshop->prepare_submission_summary($ownsubmission, true));
            $ownsubmissionexists = true;
        } else {
            print_collapsible_region_start('', 'workshop-viewlet-ownsubmission', get_string('yoursubmission', 'workshop'));
            echo $output->box_start('generalbox ownsubmission');
            echo $output->container(get_string('noyoursubmission', 'workshop'));
            $ownsubmissionexists = false;
            if ($workshop->creating_submission_allowed($USER->id)) {
                $btnurl = new moodle_url($workshop->submission_url(), array('edit' => 'on'));
                $btntxt = get_string('createsubmission', 'workshop');
            }
        }
        if (!empty($btnurl)) {
            echo $output->single_button($btnurl, $btntxt, 'get');
        }
        echo $output->box_end();
        print_collapsible_region_end();
    }

    if (has_capability('mod/workshop:viewallassessments', $PAGE->context)) {
        $perpage = get_user_preferences('workshop_perpage', 10);
        $groupid = groups_get_activity_group($workshop->cm, true);
        $data = $workshop->prepare_grading_report_data($USER->id, $groupid, $page, $perpage, $sortby, $sorthow);
        if ($data) {
            $showauthornames    = has_capability('mod/workshop:viewauthornames', $workshop->context);
            $showreviewernames  = has_capability('mod/workshop:viewreviewernames', $workshop->context);

            // prepare paging bar
            $baseurl = new moodle_url($PAGE->url, array('sortby' => $sortby, 'sorthow' => $sorthow));
            $pagingbar = new paging_bar($data->totalcount, $page, $perpage, $baseurl, 'page');

            // grading report display options
            $reportopts                         = new stdclass();
            $reportopts->showauthornames        = $showauthornames;
            $reportopts->showreviewernames      = $showreviewernames;
            $reportopts->sortby                 = $sortby;
            $reportopts->sorthow                = $sorthow;
            $reportopts->showsubmissiongrade    = false;
            $reportopts->showgradinggrade       = false;

            print_collapsible_region_start('', 'workshop-viewlet-gradereport', get_string('gradesreport', 'workshop'));
            echo $output->box_start('generalbox gradesreport');
            echo $output->container(groups_print_activity_menu($workshop->cm, $PAGE->url, true), 'groupwidget');
            echo $output->render($pagingbar);
            echo $output->render(new workshop_grading_report($data, $reportopts));
            echo $output->render($pagingbar);
            echo $output->perpage_selector($perpage);
            echo $output->box_end();
            print_collapsible_region_end();
        }
    }
    if (trim($workshop->instructreviewers)) {
        $instructions = file_rewrite_pluginfile_urls($workshop->instructreviewers, 'pluginfile.php', $PAGE->context->id,
            'mod_workshop', 'instructreviewers', 0, workshop::instruction_editors_options($PAGE->context));
        print_collapsible_region_start('', 'workshop-viewlet-instructreviewers', get_string('instructreviewers', 'workshop'));
        echo $output->box(format_text($instructions, $workshop->instructreviewersformat, array('overflowdiv'=>true)), array('generalbox', 'instructions'));
        print_collapsible_region_end();
    }

    // does the user have to assess examples before assessing other's work?
    $examplesmust = ($workshop->useexamples and $workshop->examplesmode == workshop::EXAMPLES_BEFORE_ASSESSMENT);

    // is the assessment of example submissions considered finished?
    $examplesdone = has_capability('mod/workshop:manageexamples', $workshop->context);

    // can the examples be assessed?
    $examplesavailable = true;

    if (!$examplesdone and $examplesmust and ($ownsubmissionexists === false)) {
        print_collapsible_region_start('', 'workshop-viewlet-examplesfail', get_string('exampleassessments', 'workshop'));
        echo $output->box(get_string('exampleneedsubmission', 'workshop'));
        print_collapsible_region_end();
        $examplesavailable = false;
    }

    if ($workshop->assessing_examples_allowed()
            and has_capability('mod/workshop:submit', $workshop->context)
                and ! has_capability('mod/workshop:manageexamples', $workshop->context)
                    and $examplesavailable) {
        $examples = $userplan->get_examples();
        $total = count($examples);
        $left = 0;
        // make sure the current user has all examples allocated
        foreach ($examples as $exampleid => $example) {
            if (is_null($example->assessmentid)) {
                $examples[$exampleid]->assessmentid = $workshop->add_allocation($example, $USER->id, 0);
            }
            if (is_null($example->grade)) {
                $left++;
            }
        }
        if ($left > 0 and $workshop->examplesmode != workshop::EXAMPLES_VOLUNTARY) {
            $examplesdone = false;
        } else {
            $examplesdone = true;
        }
        print_collapsible_region_start('', 'workshop-viewlet-examples', get_string('exampleassessments', 'workshop'), false, $examplesdone);
        echo $output->box_start('generalbox exampleassessments');
        if ($total == 0) {
            echo $output->heading(get_string('noexamples', 'workshop'), 3);
        } else {
            foreach ($examples as $example) {
                $summary = $workshop->prepare_example_summary($example);
                echo $output->render($summary);
            }
        }
        echo $output->box_end();
        print_collapsible_region_end();
    }
    if (!$examplesmust or $examplesdone) {
        print_collapsible_region_start('', 'workshop-viewlet-assignedassessments', get_string('assignedassessments', 'workshop'));
        if (! $assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
            echo $output->box_start('generalbox assessment-none');
            echo $output->heading(get_string('assignedassessmentsnone', 'workshop'), 3);
            echo $output->box_end();
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
                $submission->authoremail        = $assessment->authoremail;

                // transform the submission object into renderable component
                $submission = $workshop->prepare_submission_summary($submission, $shownames);

                if (is_null($assessment->grade)) {
                    $submission->status = 'notgraded';
                    $class = ' notgraded';
                    $buttontext = get_string('assess', 'workshop');
                } else {
                    $submission->status = 'graded';
                    $class = ' graded';
                    $buttontext = get_string('reassess', 'workshop');
                }

                echo $output->box_start('generalbox assessment-summary' . $class);
                echo $output->render($submission);
                $aurl = $workshop->assess_url($assessment->id);
                echo $output->single_button($aurl, $buttontext, 'get');
                echo $output->box_end();
            }
        }
        print_collapsible_region_end();
    }
    break;
case workshop::PHASE_EVALUATION:
    if (has_capability('mod/workshop:viewallassessments', $PAGE->context)) {
        $perpage = get_user_preferences('workshop_perpage', 10);
        $groupid = groups_get_activity_group($workshop->cm, true);
        $data = $workshop->prepare_grading_report_data($USER->id, $groupid, $page, $perpage, $sortby, $sorthow);
        if ($data) {
            $showauthornames    = has_capability('mod/workshop:viewauthornames', $workshop->context);
            $showreviewernames  = has_capability('mod/workshop:viewreviewernames', $workshop->context);

            if (has_capability('mod/workshop:overridegrades', $PAGE->context)) {
                // Print a drop-down selector to change the current evaluation method.
                $selector = new single_select($PAGE->url, 'eval', workshop::available_evaluators_list(),
                    $workshop->evaluation, false, 'evaluationmethodchooser');
                $selector->set_label(get_string('evaluationmethod', 'mod_workshop'));
                $selector->set_help_icon('evaluationmethod', 'mod_workshop');
                $selector->method = 'post';
                echo $output->render($selector);
                // load the grading evaluator
                $evaluator = $workshop->grading_evaluation_instance();
                $form = $evaluator->get_settings_form(new moodle_url($workshop->aggregate_url(),
                        compact('sortby', 'sorthow', 'page')));
                $form->display();
            }

            // prepare paging bar
            $baseurl = new moodle_url($PAGE->url, array('sortby' => $sortby, 'sorthow' => $sorthow));
            $pagingbar = new paging_bar($data->totalcount, $page, $perpage, $baseurl, 'page');

            // grading report display options
            $reportopts                         = new stdclass();
            $reportopts->showauthornames        = $showauthornames;
            $reportopts->showreviewernames      = $showreviewernames;
            $reportopts->sortby                 = $sortby;
            $reportopts->sorthow                = $sorthow;
            $reportopts->showsubmissiongrade    = true;
            $reportopts->showgradinggrade       = true;

            print_collapsible_region_start('', 'workshop-viewlet-gradereport', get_string('gradesreport', 'workshop'));
            echo $output->box_start('generalbox gradesreport');
            echo $output->container(groups_print_activity_menu($workshop->cm, $PAGE->url, true), 'groupwidget');
            echo $output->render($pagingbar);
            echo $output->render(new workshop_grading_report($data, $reportopts));
            echo $output->render($pagingbar);
            echo $output->perpage_selector($perpage);
            echo $output->box_end();
            print_collapsible_region_end();
        }
    }
    if (has_capability('mod/workshop:overridegrades', $workshop->context)) {
        print_collapsible_region_start('', 'workshop-viewlet-cleargrades', get_string('toolbox', 'workshop'), false, true);
        echo $output->box_start('generalbox toolbox');

        // Clear aggregated grades
        $url = new moodle_url($workshop->toolbox_url('clearaggregatedgrades'));
        $btn = new single_button($url, get_string('clearaggregatedgrades', 'workshop'), 'post');
        $btn->add_confirm_action(get_string('clearaggregatedgradesconfirm', 'workshop'));
        echo $output->container_start('toolboxaction');
        echo $output->render($btn);
        echo $output->help_icon('clearaggregatedgrades', 'workshop');
        echo $output->container_end();
        // Clear assessments
        $url = new moodle_url($workshop->toolbox_url('clearassessments'));
        $btn = new single_button($url, get_string('clearassessments', 'workshop'), 'post');
        $btn->add_confirm_action(get_string('clearassessmentsconfirm', 'workshop'));
        echo $output->container_start('toolboxaction');
        echo $output->render($btn);
        echo $output->help_icon('clearassessments', 'workshop');
        echo html_writer::empty_tag('img', array('src' => $output->pix_url('i/risk_dataloss'),
                                                 'title' => get_string('riskdatalossshort', 'admin'),
                                                 'alt' => get_string('riskdatalossshort', 'admin'),
                                                 'class' => 'workshop-risk-dataloss'));
        echo $output->container_end();

        echo $output->box_end();
        print_collapsible_region_end();
    }
    if (has_capability('mod/workshop:submit', $PAGE->context)) {
        print_collapsible_region_start('', 'workshop-viewlet-ownsubmission', get_string('yoursubmission', 'workshop'));
        echo $output->box_start('generalbox ownsubmission');
        if ($submission = $workshop->get_submission_by_author($USER->id)) {
            echo $output->render($workshop->prepare_submission_summary($submission, true));
        } else {
            echo $output->container(get_string('noyoursubmission', 'workshop'));
        }
        echo $output->box_end();
        print_collapsible_region_end();
    }
    if ($assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
        print_collapsible_region_start('', 'workshop-viewlet-assignedassessments', get_string('assignedassessments', 'workshop'));
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
            $submission->authoremail        = $assessment->authoremail;

            if (is_null($assessment->grade)) {
                $class = ' notgraded';
                $submission->status = 'notgraded';
                $buttontext = get_string('assess', 'workshop');
            } else {
                $class = ' graded';
                $submission->status = 'graded';
                $buttontext = get_string('reassess', 'workshop');
            }
            echo $output->box_start('generalbox assessment-summary' . $class);
            echo $output->render($workshop->prepare_submission_summary($submission, $shownames));
            echo $output->box_end();
        }
        print_collapsible_region_end();
    }
    break;
case workshop::PHASE_CLOSED:
    if (trim($workshop->conclusion)) {
        $conclusion = file_rewrite_pluginfile_urls($workshop->conclusion, 'pluginfile.php', $workshop->context->id,
            'mod_workshop', 'conclusion', 0, workshop::instruction_editors_options($workshop->context));
        print_collapsible_region_start('', 'workshop-viewlet-conclusion', get_string('conclusion', 'workshop'));
        echo $output->box(format_text($conclusion, $workshop->conclusionformat, array('overflowdiv'=>true)), array('generalbox', 'conclusion'));
        print_collapsible_region_end();
    }
    $finalgrades = $workshop->get_gradebook_grades($USER->id);
    if (!empty($finalgrades)) {
        print_collapsible_region_start('', 'workshop-viewlet-yourgrades', get_string('yourgrades', 'workshop'));
        echo $output->box_start('generalbox grades-yourgrades');
        echo $output->render($finalgrades);
        echo $output->box_end();
        print_collapsible_region_end();
    }
    if (has_capability('mod/workshop:viewallassessments', $PAGE->context)) {
        $perpage = get_user_preferences('workshop_perpage', 10);
        $groupid = groups_get_activity_group($workshop->cm, true);
        $data = $workshop->prepare_grading_report_data($USER->id, $groupid, $page, $perpage, $sortby, $sorthow);
        if ($data) {
            $showauthornames    = has_capability('mod/workshop:viewauthornames', $workshop->context);
            $showreviewernames  = has_capability('mod/workshop:viewreviewernames', $workshop->context);

            // prepare paging bar
            $baseurl = new moodle_url($PAGE->url, array('sortby' => $sortby, 'sorthow' => $sorthow));
            $pagingbar = new paging_bar($data->totalcount, $page, $perpage, $baseurl, 'page');

            // grading report display options
            $reportopts                         = new stdclass();
            $reportopts->showauthornames        = $showauthornames;
            $reportopts->showreviewernames      = $showreviewernames;
            $reportopts->sortby                 = $sortby;
            $reportopts->sorthow                = $sorthow;
            $reportopts->showsubmissiongrade    = true;
            $reportopts->showgradinggrade       = true;

            print_collapsible_region_start('', 'workshop-viewlet-gradereport', get_string('gradesreport', 'workshop'));
            echo $output->box_start('generalbox gradesreport');
            echo $output->container(groups_print_activity_menu($workshop->cm, $PAGE->url, true), 'groupwidget');
            echo $output->render($pagingbar);
            echo $output->render(new workshop_grading_report($data, $reportopts));
            echo $output->render($pagingbar);
            echo $output->perpage_selector($perpage);
            echo $output->box_end();
            print_collapsible_region_end();
        }
    }
    if (has_capability('mod/workshop:submit', $PAGE->context)) {
        print_collapsible_region_start('', 'workshop-viewlet-ownsubmission', get_string('yoursubmission', 'workshop'));
        echo $output->box_start('generalbox ownsubmission');
        if ($submission = $workshop->get_submission_by_author($USER->id)) {
            echo $output->render($workshop->prepare_submission_summary($submission, true));
        } else {
            echo $output->container(get_string('noyoursubmission', 'workshop'));
        }
        echo $output->box_end();

        if (!empty($submission->gradeoverby) and strlen(trim($submission->feedbackauthor)) > 0) {
            echo $output->render(new workshop_feedback_author($submission));
        }

        print_collapsible_region_end();
    }
    if (has_capability('mod/workshop:viewpublishedsubmissions', $workshop->context)) {
        $shownames = has_capability('mod/workshop:viewauthorpublished', $workshop->context);
        if ($submissions = $workshop->get_published_submissions()) {
            print_collapsible_region_start('', 'workshop-viewlet-publicsubmissions', get_string('publishedsubmissions', 'workshop'));
            foreach ($submissions as $submission) {
                echo $output->box_start('generalbox submission-summary');
                echo $output->render($workshop->prepare_submission_summary($submission, $shownames));
                echo $output->box_end();
            }
            print_collapsible_region_end();
        }
    }
    if ($assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
        print_collapsible_region_start('', 'workshop-viewlet-assignedassessments', get_string('assignedassessments', 'workshop'));
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
            $submission->authoremail        = $assessment->authoremail;

            if (is_null($assessment->grade)) {
                $class = ' notgraded';
                $submission->status = 'notgraded';
                $buttontext = get_string('assess', 'workshop');
            } else {
                $class = ' graded';
                $submission->status = 'graded';
                $buttontext = get_string('reassess', 'workshop');
            }
            echo $output->box_start('generalbox assessment-summary' . $class);
            echo $output->render($workshop->prepare_submission_summary($submission, $shownames));
            echo $output->box_end();

            if (strlen(trim($assessment->feedbackreviewer)) > 0) {
                echo $output->render(new workshop_feedback_reviewer($assessment));
            }
        }
        print_collapsible_region_end();
    }
    break;
default:
}

echo $output->footer();
