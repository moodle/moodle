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
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/submission_form.php');

$cmid   = required_param('cmid', PARAM_INT);            // course module id
$id     = optional_param('id', 0, PARAM_INT);           // submission id
$edit   = optional_param('edit', false, PARAM_BOOL);    // open for editing?

$cm     = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$workshop = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop = new workshop($workshop, $cm, $course);

$PAGE->set_url(new moodle_url($workshop->submission_url(), array('cmid' => $cmid, 'id' => $id, 'edit' => $edit)));

if ($id) { // submission is specified
    $submission = $workshop->get_submission_by_id($id);
} else { // no submission specified
    if (!$submission = $workshop->get_submission_by_author($USER->id)) {
        $submission = new stdClass();
        $submission->id = null;
        $submission->authorid = $USER->id;
    }
}

$ownsubmission  = $submission->authorid == $USER->id;
$canviewall     = has_capability('mod/workshop:viewallsubmissions', $PAGE->context);
$cansubmit      = has_capability('mod/workshop:submit', $PAGE->context);
$isreviewer     = $DB->record_exists('workshop_assessments', array('submissionid' => $submission->id, 'reviewerid' => $USER->id));

if ($submission->id and ($ownsubmission or $canviewall or $isreviewer)) {
    // ok you can go
} elseif (is_null($submission->id) and $cansubmit) {
    // ok you can go
} else {
    print_error('nopermissions');
}

$maxfiles       = $workshop->nattachments;
$maxbytes       = $workshop->maxbytes;
$contentopts    = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
$attachmentopts = array('subdirs' => true, 'maxfiles'=>$maxfiles, 'maxbytes'=>$maxbytes);
$submission     = file_prepare_standard_editor($submission, 'content', $contentopts, $PAGE->context,
                                    'workshop_submission_content', $submission->id);
$submission     = file_prepare_standard_filemanager($submission, 'attachment', $attachmentopts, $PAGE->context,
                                    'workshop_submission_attachment', $submission->id);
$mform          = new workshop_submission_form(null, array('current' => $submission, 'cm' => $cm, 'workshop' => $workshop,
                                    'contentopts' => $contentopts, 'attachmentopts' => $attachmentopts));

if ($mform->is_cancelled()) {
    redirect($workshop->view_url());

} elseif ($cansubmit and $formdata = $mform->get_data()) {
    $timenow = time();
    if (empty($formdata->id)) {
        $formdata->workshopid     = $workshop->id;
        $formdata->example        = 0; // todo add examples support
        $formdata->authorid       = $USER->id;
        $formdata->timecreated    = $timenow;
    }
    $formdata->timemodified       = $timenow;
    $formdata->title              = trim($formdata->title);
    $formdata->content            = '';          // updated later
    $formdata->contentformat      = FORMAT_HTML; // updated later
    $formdata->contenttrust       = 0;           // updated later
    if (empty($formdata->id)) {
        $formdata->id = $DB->insert_record('workshop_submissions', $formdata);
        // todo add to log
    }
    // save and relink embedded images and save attachments
    $formdata = file_postupdate_standard_editor($formdata, 'content', $contentopts, $PAGE->context,
                                                  'workshop_submission_content', $formdata->id);
    $formdata = file_postupdate_standard_filemanager($formdata, 'attachment', $attachmentopts, $PAGE->context,
                                                       'workshop_submission_attachment', $formdata->id);
    // store the updated values or re-save the new submission (re-saving needed because URLs are now rewritten)
    $DB->update_record('workshop_submissions', $formdata);
    redirect($workshop->view_url());
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
echo $OUTPUT->header();
$currenttab = 'submission';
include(dirname(__FILE__) . '/tabs.php');
echo $OUTPUT->heading(format_string($workshop->name), 2);

// if in edit mode, display the form to edit the submission

if ($edit and $ownsubmission) {
    $mform->display();
    echo $OUTPUT->footer();
    die();
}

// else display the submission

if ($submission->id) {
    $wsoutput = $PAGE->theme->get_renderer('mod_workshop', $PAGE);
    echo $wsoutput->submission_full($submission, true);
} else {
    echo $OUTPUT->box(get_string('noyoursubmission', 'workshop'));
}

if ($ownsubmission and $workshop->submitting_allowed()) {
    $editbutton                 = new html_form();
    $editbutton->method         = 'get';
    $editbutton->button->text   = get_string('editsubmission', 'workshop');
    $editbutton->url            = new moodle_url($PAGE->url, array('edit' => 'on', 'id' => $submission->id));
    echo $OUTPUT->button($editbutton);
}

// and possibly display the submission's review(s)

$canviewallassessments  = false;
if (has_capability('mod/workshop:viewallassessments', $PAGE->context)) {
    $canviewallassessments = true;
} elseif ($ownsubmission and $workshop->assessments_available()) {
    $canviewallassessments = true;
} else {
    $canviewallassessments = false;
}

$canviewgrades = false;
if ($isreviewer) {
    $canviewgrades = true;  // reviewers can always see the grades they gave even they are not available yet
} elseif ($ownsubmission or $canviewallassessments) {
    $canviewgrades = $workshop->grades_available(); // bool|null, see the function phpdoc
    if (!$canviewgrades and has_capability('mod/workshop:viewgradesbeforeagreement', $PAGE->context)) {
        $canviewgrades = true;
    }
}

if ($isreviewer) {
    // display own assessment - todo
    $strategy = $workshop->grading_strategy_instance();
}

if ($canviewallassessments) {
    // display all assessments (except the eventual own one - that has been already displayed) - todo
    $strategy = $workshop->grading_strategy_instance();
}

echo $OUTPUT->footer();
