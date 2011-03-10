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
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$cmid   = required_param('cmid', PARAM_INT);            // course module id
$id     = optional_param('id', 0, PARAM_INT);           // submission id
$edit   = optional_param('edit', false, PARAM_BOOL);    // open for editing?
$assess = optional_param('assess', false, PARAM_BOOL);  // instant assessment required

$cm     = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$workshop = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop = new workshop($workshop, $cm, $course);

$PAGE->set_url($workshop->submission_url(), array('cmid' => $cmid, 'id' => $id, 'edit' => $edit));

if ($id) { // submission is specified
    $submission = $workshop->get_submission_by_id($id);
    $workshop->log('view submission', $workshop->submission_url($submission->id), $submission->id);

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
$userassessment = $workshop->get_assessment_of_submission_by_user($submission->id, $USER->id);
$isreviewer     = !empty($userassessment);
$editable       = ($cansubmit and $ownsubmission);
$ispublished    = ($workshop->phase == workshop::PHASE_CLOSED
                    and $submission->published == 1
                    and has_capability('mod/workshop:viewpublishedsubmissions', $workshop->context));

if (empty($submission->id) and !$workshop->creating_submission_allowed()) {
    $editable = false;
}
if ($submission->id and !$workshop->modifying_submission_allowed()) {
    $editable = false;
}

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

if ($assess and $submission->id and !$isreviewer and $canallocate and $workshop->assessing_allowed()) {
    require_sesskey();
    $assessmentid = $workshop->add_allocation($submission, $USER->id);
    redirect($workshop->assess_url($assessmentid));
}

if ($edit) {
    require_once(dirname(__FILE__).'/submission_form.php');

    $maxfiles       = $workshop->nattachments;
    $maxbytes       = $workshop->maxbytes;
    $contentopts    = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
    $attachmentopts = array('subdirs' => true, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
    $submission     = file_prepare_standard_editor($submission, 'content', $contentopts, $workshop->context,
                                        'mod_workshop', 'submission_content', $submission->id);
    $submission     = file_prepare_standard_filemanager($submission, 'attachment', $attachmentopts, $workshop->context,
                                        'mod_workshop', 'submission_attachment', $submission->id);

    $mform          = new workshop_submission_form($PAGE->url, array('current' => $submission, 'workshop' => $workshop,
                                                    'contentopts' => $contentopts, 'attachmentopts' => $attachmentopts));

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
        if (is_null($submission->id)) {
            $submission->id = $formdata->id = $DB->insert_record('workshop_submissions', $formdata);
            $workshop->log('add submission', $workshop->submission_url($submission->id), $submission->id);
        } else {
            $workshop->log('update submission', $workshop->submission_url($submission->id), $submission->id);
            if (empty($formdata->id) or empty($submission->id) or ($formdata->id != $submission->id)) {
                throw new moodle_exception('err_submissionid', 'workshop');
            }
        }
        // save and relink embedded images and save attachments
        $formdata = file_postupdate_standard_editor($formdata, 'content', $contentopts, $workshop->context,
                                                      'mod_workshop', 'submission_content', $submission->id);
        $formdata = file_postupdate_standard_filemanager($formdata, 'attachment', $attachmentopts, $workshop->context,
                                                           'mod_workshop', 'submission_attachment', $submission->id);
        if (empty($formdata->attachment)) {
            // explicit cast to zero integer
            $formdata->attachment = 0;
        }
        // store the updated values or re-save the new submission (re-saving needed because URLs are now rewritten)
        $DB->update_record('workshop_submissions', $formdata);
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

// show instructions for submitting as thay may contain some list of questions and we need to know them
// while reading the submitted answer
if (trim($workshop->instructauthors)) {
    $instructions = file_rewrite_pluginfile_urls($workshop->instructauthors, 'pluginfile.php', $PAGE->context->id,
        'mod_workshop', 'instructauthors', 0, workshop::instruction_editors_options($PAGE->context));
    print_collapsible_region_start('', 'workshop-viewlet-instructauthors', get_string('instructauthors', 'workshop'));
    echo $output->box(format_text($instructions, $workshop->instructauthorsformat, array('overflowdiv'=>true)), array('generalbox', 'instructions'));
    print_collapsible_region_end();
}

// if in edit mode, display the form to edit the submission

if ($edit) {
    $mform->display();
    echo $output->footer();
    die();
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

if ($submission->id and !$edit and !$isreviewer and $canallocate and $workshop->assessing_allowed()) {
    $url = new moodle_url($PAGE->url, array('assess' => 1));
    echo $output->single_button($url, get_string('assess', 'workshop'), 'post');
}

// and possibly display the submission's review(s)

if ($isreviewer) {
    $strategy = $workshop->grading_strategy_instance();
    $mform = $strategy->get_assessment_form($PAGE->url, 'assessment', $userassessment, false);
    echo $output->heading(get_string('assessmentbyyourself', 'workshop'), 2);
    // reviewers can always see the grades they gave even they are not available yet
    if (is_null($userassessment->grade)) {
        echo $output->heading(get_string('notassessed', 'workshop'), 3);
        if ($workshop->assessing_allowed()) {
            echo $output->container($output->single_button($workshop->assess_url($userassessment->id), get_string('assess', 'workshop'), 'get'),
                    array('class' => 'buttonsbar'));
        }
    } else {
        $a = new stdclass();
        $a->max = $workshop->real_grade(100);
        $a->received = $workshop->real_grade($userassessment->grade);
        echo $output->heading(get_string('gradeinfo', 'workshop', $a), 3);
        if ($userassessment->weight != 1) {
            echo $output->heading(get_string('weightinfo', 'workshop', $userassessment->weight), 3);
        }
        if ($workshop->assessing_allowed()) {
            echo $output->container($output->single_button($workshop->assess_url($userassessment->id), get_string('reassess', 'workshop'), 'get'),
                    array('class' => 'buttonsbar'));
        }
        $mform->display();
    }
}

if (has_capability('mod/workshop:viewallassessments', $workshop->context) or ($ownsubmission and $workshop->assessments_available())) {
    $strategy = $workshop->grading_strategy_instance();
    $assessments = $workshop->get_assessments_of_submission($submission->id);
    $canviewreviewernames = has_capability('mod/workshop:viewreviewernames', $workshop->context);
    foreach ($assessments as $assessment) {
        if ($assessment->reviewerid == $USER->id) {
            // own assessment has been displayed already
            continue;
        }
        if (is_null($assessment->grade)) {
            // not graded assessment are not displayed
            continue;
        }
        if ($canviewreviewernames) {
            $reviewer = new stdclass();
            $reviewer->firstname = $assessment->reviewerfirstname;
            $reviewer->lastname = $assessment->reviewerlastname;
            echo $output->heading(get_string('assessmentbyknown', 'workshop', fullname($reviewer)), 2);
        } else {
            echo $output->heading(get_string('assessmentbyunknown', 'workshop'), 2);
        }
        $a = new stdclass();
        $a->max = $workshop->real_grade(100);
        $a->received = $workshop->real_grade($assessment->grade);
        echo $output->heading(get_string('gradeinfo', 'workshop', $a), 3);
        if ($assessment->weight != 1) {
            echo $output->heading(get_string('weightinfo', 'workshop', $assessment->weight), 3);
        }
        if (has_capability('mod/workshop:overridegrades', $workshop->context)) {
            echo $output->container($output->single_button($workshop->assess_url($assessment->id), get_string('assessmentsettings', 'workshop'), 'get'),
                    array('class' => 'buttonsbar'));
        }
        $mform = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, false);
        $mform->display();
    }
}

if (!$edit and $canoverride) {
    // display a form to override the submission grade
    $feedbackform->display();
}

echo $output->footer();
