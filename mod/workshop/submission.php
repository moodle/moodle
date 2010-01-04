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

$cm         = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('mod/workshop:submit', $PAGE->context);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop   = new workshop_api($workshop, $cm, $course);

if ($id) { // submission is specified
    $submission = $DB->get_record('workshop_submissions', array('id' => $id, 'workshopid' => $workshop->id), '*', MUST_EXIST);
} else { // no submission specified
    if (!$submission = $workshop->get_submission_by_author($USER->id)) {
        $submission = new object();
        $submission->id = null;
    }
}
unset($id); // not needed anymore

$maxfiles           = $workshop->nattachments;
$maxbytes           = $workshop->maxbytes;
$contentoptions     = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
$attachmentoptions  = array('subdirs' => false, 'maxfiles'=>$maxfiles, 'maxbytes'=>$maxbytes);
$submission         = file_prepare_standard_editor($submission, 'content', $contentoptions, $PAGE->context,
                                                   'workshop_submission_content', $submission->id);
$submission         = file_prepare_standard_filemanager($submission, 'attachment', $attachmentoptions, $PAGE->context,
                                                        'workshop_submission_attachment', $submission->id);
// create form and set initial data
$mform = new workshop_submission_form(null, array('current' => $submission, 'cm' => $cm, 'workshop' => $workshop,
                                    'contentoptions' => $contentoptions, 'attachmentoptions' => $attachmentoptions));

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
    $submission->content            = '';          // updated later
    $submission->contentformat      = FORMAT_HTML; // updated later
    $submission->contenttrust       = 0;           // updated later
    if (empty($submission->id)) {
        $submission->id = $DB->insert_record('workshop_submissions', $submission);
        // todo add to log
    }
    // save and relink embedded images and save attachments
    $submission = file_postupdate_standard_editor($submission, 'content', $contentoptions, $PAGE->context,
                                                  'workshop_submission_content', $submission->id);
    $submission = file_postupdate_standard_filemanager($submission, 'attachment', $attachmentoptions, $PAGE->context,
                                                       'workshop_submission_attachment', $submission->id);
    // store the updated values or re-save the new submission
    $DB->update_record('workshop_submissions', $submission);
    redirect("view.php?id=$cm->id");
}

// Output starts here
$PAGE->set_url('mod/workshop/submission.php', array('cmid' => $cm->id));
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);

$stredit    = empty($submission->id) ? get_string('editingsubmission', 'workshop') : get_string('edit');
$navigation = build_navigation($stredit, $cm);
$menu       = navmenu($course, $cm);

echo $OUTPUT->header($navigation, $menu);
echo $OUTPUT->heading(format_string($workshop->name), 2);
$mform->display();
echo $OUTPUT->footer();
