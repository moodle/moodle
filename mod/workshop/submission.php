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
 * Submit an assignment or edit the already submitted work
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/submission_form.php');

$cmid = required_param('cmid', PARAM_INT);            // course module id
$id   = optional_param('id', 0, PARAM_INT);           // submission id

if (!$cm = get_coursemodule_from_id('workshop', $cmid)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (isguestuser()) {
    print_error('guestnoedit', 'workshop', "$CFG->wwwroot/mod/workshop/view.php?id=$cmid");
}

if (!$workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
    print_error('invalidid', 'workshop');
}

if ($id) { // submission is specified
    if (!$submission = $DB->get_record('workshop_submissions', array('id' => $id, 'workshopid' => $workshop->id))) {
        print_error('invalidsubmissionid', 'workshop');
    }
    // todo check access rights
    //require_capability('mod/workshop:submit', $context) or user has cap edit all submissions?

} else { // no submission specified
    //todo require_capability('mod/workshop:submit', $context);
    if (!$submission = workshop_get_user_submission($workshop, $USER->id)) {
        $submission = new object();
        $submission->id = null;
    }
}
unset($id); // not needed anymore

$maxfiles = $workshop->nattachments;
$maxbytes = $workshop->maxbytes;

$dataoptions = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
$attachmentoptions = array('subdirs' => false, 'maxfiles'=>$maxfiles, 'maxbytes'=>$maxbytes);

$submission = file_prepare_standard_editor($submission, 'data', $dataoptions, $context, 'workshop_submission', $submission->id);
$submission = file_prepare_standard_filemanager($submission, 'attachment', $attachmentoptions, $context,
                                                'workshop_attachment', $submission->id);

$submission->cmid = $cm->id;

// create form and set initial data
$mform = new workshop_submission_form(null, array('current' => $submission, 'cm' => $cm, 'workshop'=>$workshop,
                                                 'dataoptions' => $dataoptions, 'attachmentoptions'=>$attachmentoptions));

if ($mform->is_cancelled()) {
    redirect("view.php?id=$cm->id");
} else if ($submission = $mform->get_data()) {

    $timenow = time();

    if (empty($submission->id)) {
        $submission->workshopid     = $workshop->id;
        $submission->example        = 0; // todo add examples support
        $submission->userid         = $USER->id;
        $submission->timecreated    = $timenow;
    }

    $submission->timemodified       = $timenow;
    $submission->title              = trim($submission->title);
    $submission->data               = '';          // updated later
    $submission->dataformat         = FORMAT_HTML; // updated later
    $submission->datatrust          = 0;           // updated later

    if (empty($submission->id)) {
        $submission->id = $DB->insert_record('workshop_submissions', $submission);
        // todo add to log
    }

    // save and relink embedded images and save attachments
    $submission = file_postupdate_standard_editor($submission, 'data', $dataoptions, $context,
                                                    'workshop_submission', $submission->id);
    $submission = file_postupdate_standard_filemanager($submission, 'attachment', $attachmentoptions, $context,
                                                    'workshop_attachment', $submission->id);

    // store the updated values or re-save the new submission
    $DB->update_record('workshop_submissions', $submission);

    redirect("view.php?id=$cm->id");
}

$stredit = empty($submission->id) ? get_string('editingsubmission', 'workshop') : get_string('edit');

$navigation = build_navigation($stredit, $cm);
print_header_simple(format_string($workshop->name), "", $navigation, "", "", true, "", navmenu($course, $cm));

print_heading(format_string($workshop->name));

$mform->display();

print_footer($course);
