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
 * Submit own assignment or edit the already submitted own work
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
require_capability('mod/workshop:submit', $PAGE->context);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop   = new workshop($workshop, $cm, $course);

if ($id) { // submission is specified
    $submission = $DB->get_record('workshop_submissions', array('id' => $id, 'workshopid' => $workshop->id), '*', MUST_EXIST);
} else { // no submission specified
    if (!$submission = $workshop->get_submission_by_author($USER->id)) {
        $submission = new stdClass();
        $submission->id = null;
        $submission->userid = $USER->id;
    }
}

if ($submission->userid !== $USER->id) {
    print_error('nopermissiontoviewpage', 'error', $workshop->view_url());
}

$maxfiles       = $workshop->nattachments;
$maxbytes       = $workshop->maxbytes;
$contentopts    = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
$attachmentopts = array('subdirs' => false, 'maxfiles'=>$maxfiles, 'maxbytes'=>$maxbytes);
$submission     = file_prepare_standard_editor($submission, 'content', $contentopts, $PAGE->context,
                                    'workshop_submission_content', $submission->id);
$submission     = file_prepare_standard_filemanager($submission, 'attachment', $attachmentopts, $PAGE->context,
                                    'workshop_submission_attachment', $submission->id);
$mform          = new workshop_submission_form(null, array('current' => $submission, 'cm' => $cm, 'workshop' => $workshop,
                                    'contentopts' => $contentopts, 'attachmentopts' => $attachmentopts));

if ($mform->is_cancelled()) {
    redirect($workshop->view_url());

} elseif ($formdata = $mform->get_data()) {
    $timenow = time();
    if (empty($formdata->id)) {
        $formdata->workshopid     = $workshop->id;
        $formdata->example        = 0; // todo add examples support
        $formdata->userid         = $USER->id;
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

$PAGE->set_url('mod/workshop/submission.php', array('cmid' => $cm->id));
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);

// Output starts here
$stredit    = empty($submission->id) ? get_string('editingsubmission', 'workshop') : get_string('edit');
$navigation = build_navigation($stredit, $cm);
echo $OUTPUT->header($navigation);
echo $OUTPUT->heading(format_string($workshop->name), 2);

if ($edit) {
    $mform->display();
    echo $OUTPUT->footer();
    die();
}

if (!empty($submission->id)) {
    $wsoutput = $PAGE->theme->get_renderer('mod_workshop', $PAGE);
    echo $wsoutput->submission_full($submission, true, $USER);
}

if ($workshop->submitting_allowed()) {
    $editbutton                 = new html_form();
    $editbutton->method         = 'get';
    $editbutton->button->text   = get_string('editsubmission', 'workshop');
    $editbutton->url            = new moodle_url($PAGE->url, array('edit' => 'on', 'id' => $submission->id));
    echo $OUTPUT->button($editbutton);
}

echo $OUTPUT->footer();
