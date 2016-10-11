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
 * View a single (usually the own) submission, submit own work.
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

$cmid = required_param('cmid', PARAM_INT); // Course module id.
$id = optional_param('id', 0, PARAM_INT); // Submission id.
$edit = optional_param('edit', false, PARAM_BOOL); // Open the page for editing?
$assess = optional_param('assess', false, PARAM_BOOL); // Instant assessment required.
$delete = optional_param('delete', false, PARAM_BOOL); // Submission removal requested.
$confirm = optional_param('confirm', false, PARAM_BOOL); // Submission removal request confirmed.

$cm = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$workshoprecord = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop = new workshop($workshoprecord, $cm, $course);

$PAGE->set_url($workshop->submission_url(), array('cmid' => $cmid, 'id' => $id));

if ($edit) {
    $PAGE->url->param('edit', $edit);
}

if ($id) { // submission is specified
    $submission = $workshop->get_submission_by_id($id);

    $params = array(
        'objectid' => $submission->id,
        'context' => $workshop->context,
        'courseid' => $workshop->course->id,
        'relateduserid' => $submission->authorid,
        'other' => array(
            'workshopid' => $workshop->id
        )
    );

    $event = \mod_workshop\event\submission_viewed::create($params);
    $event->trigger();

} else { // no submission specified
    if (!$submission = $workshop->get_submission_by_author($USER->id)) {
        $submission = new stdclass();
        $submission->id = null;
        $submission->authorid = $USER->id;
        $submission->example = 0;
        $submission->grade = null;
        $submission->gradeover = null;
        $submission->published = null;
        $submission->feedbackauthor = null;
        $submission->feedbackauthorformat = editors_get_preferred_format();
    }
}

$ownsubmission  = $submission->authorid == $USER->id;
$canviewall     = has_capability('mod/workshop:viewallsubmissions', $workshop->context);
$cansubmit      = has_capability('mod/workshop:submit', $workshop->context);
$canallocate    = has_capability('mod/workshop:allocate', $workshop->context);
$canpublish     = has_capability('mod/workshop:publishsubmissions', $workshop->context);
$canoverride    = (($workshop->phase == workshop::PHASE_EVALUATION) and has_capability('mod/workshop:overridegrades', $workshop->context));
$candeleteall   = has_capability('mod/workshop:deletesubmissions', $workshop->context);
$userassessment = $workshop->get_assessment_of_submission_by_user($submission->id, $USER->id);
$isreviewer     = !empty($userassessment);
$editable       = ($cansubmit and $ownsubmission);
$deletable      = $candeleteall;
$ispublished    = ($workshop->phase == workshop::PHASE_CLOSED
                    and $submission->published == 1
                    and has_capability('mod/workshop:viewpublishedsubmissions', $workshop->context));

if (empty($submission->id) and !$workshop->creating_submission_allowed($USER->id)) {
    $editable = false;
}
if ($submission->id and !$workshop->modifying_submission_allowed($USER->id)) {
    $editable = false;
}

$canviewall = $canviewall && $workshop->check_group_membership($submission->authorid);

if ($editable and $workshop->useexamples and $workshop->examplesmode == workshop::EXAMPLES_BEFORE_SUBMISSION
        and !has_capability('mod/workshop:manageexamples', $workshop->context)) {
    // check that all required examples have been assessed by the user
    $examples = $workshop->get_examples_for_reviewer($USER->id);
    foreach ($examples as $exampleid => $example) {
        if (is_null($example->grade)) {
            $editable = false;
            break;
        }
    }
}
$edit = ($editable and $edit);

if (!$candeleteall and $ownsubmission and $editable) {
    // Only allow the student to delete their own submission if it's still editable and hasn't been assessed.
    if (count($workshop->get_assessments_of_submission($submission->id)) > 0) {
        $deletable = false;
    } else {
        $deletable = true;
    }
}

if ($submission->id and $delete and $confirm and $deletable) {
    require_sesskey();
    $workshop->delete_submission($submission);

    // Event information.
    $params = array(
        'context' => $workshop->context,
        'courseid' => $workshop->course->id,
        'relateduserid' => $submission->authorid,
        'other' => array(
            'submissiontitle' => $submission->title
        )
    );
    $params['objectid'] = $submission->id;
    $event = \mod_workshop\event\submission_deleted::create($params);
    $event->add_record_snapshot('workshop', $workshoprecord);
    $event->trigger();

    redirect($workshop->view_url());
}

$seenaspublished = false; // is the submission seen as a published submission?

if ($submission->id and ($ownsubmission or $canviewall or $isreviewer)) {
    // ok you can go
} elseif ($submission->id and $ispublished) {
    // ok you can go
    $seenaspublished = true;
} elseif (is_null($submission->id) and $cansubmit) {
    // ok you can go
} else {
    print_error('nopermissions', 'error', $workshop->view_url(), 'view or create submission');
}

if ($assess and $submission->id and !$isreviewer and $canallocate and $workshop->assessing_allowed($USER->id)) {
    require_sesskey();
    $assessmentid = $workshop->add_allocation($submission, $USER->id);
    redirect($workshop->assess_url($assessmentid));
}

if ($edit) {
    require_once(__DIR__.'/submission_form.php');

    $submission = file_prepare_standard_editor($submission, 'content', $workshop->submission_content_options(),
        $workshop->context, 'mod_workshop', 'submission_content', $submission->id);

    $submission = file_prepare_standard_filemanager($submission, 'attachment', $workshop->submission_attachment_options(),
        $workshop->context, 'mod_workshop', 'submission_attachment', $submission->id);

    $mform = new workshop_submission_form($PAGE->url, array('current' => $submission, 'workshop' => $workshop,
        'contentopts' => $workshop->submission_content_options(), 'attachmentopts' => $workshop->submission_attachment_options()));

    if ($mform->is_cancelled()) {
        redirect($workshop->view_url());

    } elseif ($cansubmit and $formdata = $mform->get_data()) {
        if ($formdata->example == 0) {
            // this was used just for validation, it must be set to zero when dealing with normal submissions
            unset($formdata->example);
        } else {
            throw new coding_exception('Invalid submission form data value: example');
        }
        $timenow = time();
        if (is_null($submission->id)) {
            $formdata->workshopid     = $workshop->id;
            $formdata->example        = 0;
            $formdata->authorid       = $USER->id;
            $formdata->timecreated    = $timenow;
            $formdata->feedbackauthorformat = editors_get_preferred_format();
        }
        $formdata->timemodified       = $timenow;
        $formdata->title              = trim($formdata->title);
        $formdata->content            = '';          // updated later
        $formdata->contentformat      = FORMAT_HTML; // updated later
        $formdata->contenttrust       = 0;           // updated later
        $formdata->late               = 0x0;         // bit mask
        if (!empty($workshop->submissionend) and ($workshop->submissionend < time())) {
            $formdata->late = $formdata->late | 0x1;
        }
        if ($workshop->phase == workshop::PHASE_ASSESSMENT) {
            $formdata->late = $formdata->late | 0x2;
        }

        // Event information.
        $params = array(
            'context' => $workshop->context,
            'courseid' => $workshop->course->id,
            'other' => array(
                'submissiontitle' => $formdata->title
            )
        );
        $logdata = null;
        if (is_null($submission->id)) {
            $submission->id = $formdata->id = $DB->insert_record('workshop_submissions', $formdata);
            $params['objectid'] = $submission->id;
            $event = \mod_workshop\event\submission_created::create($params);
            $event->trigger();
        } else {
            if (empty($formdata->id) or empty($submission->id) or ($formdata->id != $submission->id)) {
                throw new moodle_exception('err_submissionid', 'workshop');
            }
        }
        $params['objectid'] = $submission->id;

        // Save and relink embedded images and save attachments.
        $formdata = file_postupdate_standard_editor($formdata, 'content', $workshop->submission_content_options(),
            $workshop->context, 'mod_workshop', 'submission_content', $submission->id);

        $formdata = file_postupdate_standard_filemanager($formdata, 'attachment', $workshop->submission_attachment_options(),
            $workshop->context, 'mod_workshop', 'submission_attachment', $submission->id);

        if (empty($formdata->attachment)) {
            // explicit cast to zero integer
            $formdata->attachment = 0;
        }
        // store the updated values or re-save the new submission (re-saving needed because URLs are now rewritten)
        $DB->update_record('workshop_submissions', $formdata);
        $event = \mod_workshop\event\submission_updated::create($params);
        $event->add_record_snapshot('workshop', $workshoprecord);
        $event->trigger();

        // send submitted content for plagiarism detection
        $fs = get_file_storage();
        $files = $fs->get_area_files($workshop->context->id, 'mod_workshop', 'submission_attachment', $submission->id);

        $params['other']['content'] = $formdata->content;
        $params['other']['pathnamehashes'] = array_keys($files);

        $event = \mod_workshop\event\assessable_uploaded::create($params);
        $event->set_legacy_logdata($logdata);
        $event->trigger();

        redirect($workshop->submission_url($formdata->id));
    }
}

// load the form to override grade and/or publish the submission and process the submitted data eventually
if (!$edit and ($canoverride or $canpublish)) {
    $options = array(
        'editable' => true,
        'editablepublished' => $canpublish,
        'overridablegrade' => $canoverride);
    $feedbackform = $workshop->get_feedbackauthor_form($PAGE->url, $submission, $options);
    if ($data = $feedbackform->get_data()) {
        $data = file_postupdate_standard_editor($data, 'feedbackauthor', array(), $workshop->context);
        $record = new stdclass();
        $record->id = $submission->id;
        if ($canoverride) {
            $record->gradeover = $workshop->raw_grade_value($data->gradeover, $workshop->grade);
            $record->gradeoverby = $USER->id;
            $record->feedbackauthor = $data->feedbackauthor;
            $record->feedbackauthorformat = $data->feedbackauthorformat;
        }
        if ($canpublish) {
            $record->published = !empty($data->published);
        }
        $DB->update_record('workshop_submissions', $record);
        redirect($workshop->view_url());
    }
}

$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
if ($edit) {
    $PAGE->navbar->add(get_string('mysubmission', 'workshop'), $workshop->submission_url(), navigation_node::TYPE_CUSTOM);
    $PAGE->navbar->add(get_string('editingsubmission', 'workshop'));
} elseif ($ownsubmission) {
    $PAGE->navbar->add(get_string('mysubmission', 'workshop'));
} else {
    $PAGE->navbar->add(get_string('submission', 'workshop'));
}

// Output starts here
$output = $PAGE->get_renderer('mod_workshop');
echo $output->header();
echo $output->heading(format_string($workshop->name), 2);
echo $output->heading(get_string('mysubmission', 'workshop'), 3);

// show instructions for submitting as thay may contain some list of questions and we need to know them
// while reading the submitted answer
if (trim($workshop->instructauthors)) {
    $instructions = file_rewrite_pluginfile_urls($workshop->instructauthors, 'pluginfile.php', $PAGE->context->id,
        'mod_workshop', 'instructauthors', null, workshop::instruction_editors_options($PAGE->context));
    print_collapsible_region_start('', 'workshop-viewlet-instructauthors', get_string('instructauthors', 'workshop'));
    echo $output->box(format_text($instructions, $workshop->instructauthorsformat, array('overflowdiv'=>true)), array('generalbox', 'instructions'));
    print_collapsible_region_end();
}

// if in edit mode, display the form to edit the submission

if ($edit) {
    if (!empty($CFG->enableplagiarism)) {
        require_once($CFG->libdir.'/plagiarismlib.php');
        echo plagiarism_print_disclosure($cm->id);
    }
    $mform->display();
    echo $output->footer();
    die();
}

// Confirm deletion (if requested).
if ($deletable and $delete) {
    $prompt = get_string('submissiondeleteconfirm', 'workshop');
    if ($candeleteall) {
        $count = count($workshop->get_assessments_of_submission($submission->id));
        if ($count > 0) {
            $prompt = get_string('submissiondeleteconfirmassess', 'workshop', ['count' => $count]);
        }
    }
    echo $output->confirm($prompt, new moodle_url($PAGE->url, ['delete' => 1, 'confirm' => 1]), $workshop->view_url());
}

// else display the submission

if ($submission->id) {
    if ($seenaspublished) {
        $showauthor = has_capability('mod/workshop:viewauthorpublished', $workshop->context);
    } else {
        $showauthor = has_capability('mod/workshop:viewauthornames', $workshop->context);
    }
    echo $output->render($workshop->prepare_submission($submission, $showauthor));
} else {
    echo $output->box(get_string('noyoursubmission', 'workshop'));
}

// If not at removal confirmation screen, some action buttons can be displayed.
if (!$delete) {
    // Display create/edit button.
    if ($editable) {
        if ($submission->id) {
            $btnurl = new moodle_url($PAGE->url, array('edit' => 'on', 'id' => $submission->id));
            $btntxt = get_string('editsubmission', 'workshop');
        } else {
            $btnurl = new moodle_url($PAGE->url, array('edit' => 'on'));
            $btntxt = get_string('createsubmission', 'workshop');
        }
        echo $output->single_button($btnurl, $btntxt, 'get');
    }

    // Display delete button.
    if ($submission->id and $deletable) {
        $url = new moodle_url($PAGE->url, array('delete' => 1));
        echo $output->single_button($url, get_string('deletesubmission', 'workshop'), 'get');
    }

    // Display assess button.
    if ($submission->id and !$edit and !$isreviewer and $canallocate and $workshop->assessing_allowed($USER->id)) {
        $url = new moodle_url($PAGE->url, array('assess' => 1));
        echo $output->single_button($url, get_string('assess', 'workshop'), 'post');
    }
}

if (($workshop->phase == workshop::PHASE_CLOSED) and ($ownsubmission or $canviewall)) {
    if (!empty($submission->gradeoverby) and strlen(trim($submission->feedbackauthor)) > 0) {
        echo $output->render(new workshop_feedback_author($submission));
    }
}

// and possibly display the submission's review(s)

if ($isreviewer) {
    // user's own assessment
    $strategy   = $workshop->grading_strategy_instance();
    $mform      = $strategy->get_assessment_form($PAGE->url, 'assessment', $userassessment, false);
    $options    = array(
        'showreviewer'  => true,
        'showauthor'    => $showauthor,
        'showform'      => !is_null($userassessment->grade),
        'showweight'    => true,
    );
    $assessment = $workshop->prepare_assessment($userassessment, $mform, $options);
    $assessment->title = get_string('assessmentbyyourself', 'workshop');

    if ($workshop->assessing_allowed($USER->id)) {
        if (is_null($userassessment->grade)) {
            $assessment->add_action($workshop->assess_url($assessment->id), get_string('assess', 'workshop'));
        } else {
            $assessment->add_action($workshop->assess_url($assessment->id), get_string('reassess', 'workshop'));
        }
    }
    if ($canoverride) {
        $assessment->add_action($workshop->assess_url($assessment->id), get_string('assessmentsettings', 'workshop'));
    }

    echo $output->render($assessment);

    if ($workshop->phase == workshop::PHASE_CLOSED) {
        if (strlen(trim($userassessment->feedbackreviewer)) > 0) {
            echo $output->render(new workshop_feedback_reviewer($userassessment));
        }
    }
}

if (has_capability('mod/workshop:viewallassessments', $workshop->context) or ($ownsubmission and $workshop->assessments_available())) {
    // other assessments
    $strategy       = $workshop->grading_strategy_instance();
    $assessments    = $workshop->get_assessments_of_submission($submission->id);
    $showreviewer   = has_capability('mod/workshop:viewreviewernames', $workshop->context);
    foreach ($assessments as $assessment) {
        if ($assessment->reviewerid == $USER->id) {
            // own assessment has been displayed already
            continue;
        }
        if (is_null($assessment->grade) and !has_capability('mod/workshop:viewallassessments', $workshop->context)) {
            // students do not see peer-assessment that are not graded yet
            continue;
        }
        $mform      = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, false);
        $options    = array(
            'showreviewer'  => $showreviewer,
            'showauthor'    => $showauthor,
            'showform'      => !is_null($assessment->grade),
            'showweight'    => true,
        );
        $displayassessment = $workshop->prepare_assessment($assessment, $mform, $options);
        if ($canoverride) {
            $displayassessment->add_action($workshop->assess_url($assessment->id), get_string('assessmentsettings', 'workshop'));
        }
        echo $output->render($displayassessment);

        if ($workshop->phase == workshop::PHASE_CLOSED and has_capability('mod/workshop:viewallassessments', $workshop->context)) {
            if (strlen(trim($assessment->feedbackreviewer)) > 0) {
                echo $output->render(new workshop_feedback_reviewer($assessment));
            }
        }
    }
}

if (!$edit and $canoverride) {
    // display a form to override the submission grade
    $feedbackform->display();
}

// If portfolios are enabled and we are not on the edit/removal confirmation screen, display a button to export this page.
// The export is not offered if the submission is seen as a published one (it has no relation to the current user.
if (!empty($CFG->enableportfolios)) {
    if (!$delete and !$edit and !$seenaspublished and $submission->id and ($ownsubmission or $canviewall or $isreviewer)) {
        if (has_capability('mod/workshop:exportsubmissions', $workshop->context)) {
            require_once($CFG->libdir.'/portfoliolib.php');

            $button = new portfolio_add_button();
            $button->set_callback_options('mod_workshop_portfolio_caller', array(
                'id' => $workshop->cm->id,
                'submissionid' => $submission->id,
            ), 'mod_workshop');
            $button->set_formats(PORTFOLIO_FORMAT_RICHHTML);
            echo html_writer::start_tag('div', array('class' => 'singlebutton'));
            echo $button->to_html(PORTFOLIO_ADD_FULL_FORM, get_string('exportsubmission', 'workshop'));
            echo html_writer::end_tag('div');
        }
    }
}

echo $output->footer();
