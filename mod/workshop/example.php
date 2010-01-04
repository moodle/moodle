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
 * View or edit a single example example
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$cmid   = required_param('cmid', PARAM_INT);            // course module id
$id     = required_param('id', PARAM_INT);              // example submission id, 0 for the new one
$edit   = optional_param('edit', false, PARAM_BOOL);    // open for editing?
$delete = optional_param('delete', false, PARAM_BOOL);  // example removal requested
$confirm = optional_param('confirm', false, PARAM_BOOL);  // example removal request confirmed

$cm     = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$workshop = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop = new workshop($workshop, $cm, $course);

$PAGE->set_url(new moodle_url($workshop->example_url($id), array('edit' => $edit)));

if ($id) { // example is specified
    $example = $workshop->get_example_by_id($id);
} else { // no example specified - create new one
    require_capability('mod/workshop:manageexamples', $workshop->context);
    $example = new stdClass();
    $example->id = null;
    $example->authorid = $USER->id;
}

$canmanage  = has_capability('mod/workshop:manageexamples', $workshop->context);
$isreviewer = $DB->record_exists('workshop_assessments', array('submissionid' => $example->id, 'reviewerid' => $USER->id));

if ($id and $delete and $confirm and $canmanage) {
    require_sesskey();
    $workshop->delete_submission($example);
    redirect($workshop->view_url());
}

if ($example->id and ($canmanage or $isreviewer)) {
    // ok you can go
} elseif (is_null($example->id) and $canmanage) {
    // ok you can go
} else {
    print_error('nopermissions');
}

if ($edit and $canmanage) {
    require_once(dirname(__FILE__).'/submission_form.php');

    $maxfiles       = $workshop->nattachments;
    $maxbytes       = $workshop->maxbytes;
    $contentopts    = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
    $attachmentopts = array('subdirs' => true, 'maxfiles' => $maxfiles, 'maxbytes' => $maxbytes);
    $example        = file_prepare_standard_editor($example, 'content', $contentopts, $workshop->context,
                                        'workshop_submission_content', $example->id);
    $example        = file_prepare_standard_filemanager($example, 'attachment', $attachmentopts, $workshop->context,
                                        'workshop_submission_attachment', $example->id);

    $mform          = new workshop_submission_form($PAGE->url, array('current' => $example, 'workshop' => $workshop,
                                                    'contentopts' => $contentopts, 'attachmentopts' => $attachmentopts));

    if ($mform->is_cancelled()) {
        redirect($workshop->view_url());

    } elseif ($canmanage and $formdata = $mform->get_data()) {
        $timenow = time();
        if (empty($formdata->id)) {
            $formdata->workshopid     = $workshop->id;
            $formdata->example        = 1;
            $formdata->authorid       = $USER->id;
            $formdata->timecreated    = $timenow;
            $formdata->feedbackauthorformat = FORMAT_HTML; // todo better default
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
        $formdata = file_postupdate_standard_editor($formdata, 'content', $contentopts, $workshop->context,
                                                      'workshop_submission_content', $formdata->id);
        $formdata = file_postupdate_standard_filemanager($formdata, 'attachment', $attachmentopts, $workshop->context,
                                                           'workshop_submission_attachment', $formdata->id);
        if (empty($formdata->attachment)) {
            // explicit cast to zero integer
            $formdata->attachment = 0;
        }
        // store the updated values or re-save the new example (re-saving needed because URLs are now rewritten)
        $DB->update_record('workshop_submissions', $formdata);
        redirect($workshop->example_url($formdata->id));
    }
}

$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
if ($edit) {
    $PAGE->navbar->add(get_string('exampleediting', 'workshop'));
} else {
    $PAGE->navbar->add(get_string('example', 'workshop'));
}

// Output starts here
echo $OUTPUT->header();
$currenttab = 'submission';
include(dirname(__FILE__) . '/tabs.php');
echo $OUTPUT->heading(format_string($workshop->name), 2);

// if in edit mode, display the form to edit the example
if ($edit and $canmanage) {
    $mform->display();
    echo $OUTPUT->footer();
    die();
}

// else display the example...
if ($example->id) {
    if ($canmanage and $delete) {
    echo $OUTPUT->confirm(get_string('exampledeleteconfirm', 'workshop'),
            new moodle_url($PAGE->url, array('delete' => 1, 'confirm' => 1)), $workshop->view_url());
    }
    $wsoutput = $PAGE->theme->get_renderer('mod_workshop', $PAGE);
    echo $wsoutput->example_full($example, true);
}
// ...with an option to edit and remove it
if ($canmanage) {
    echo $OUTPUT->container_start('buttonsbar');
    if (empty($edit) and empty($delete)) {
        $editbutton                 = new html_form();
        $editbutton->method         = 'get';
        $editbutton->button->text   = get_string('exampleedit', 'workshop');
        $editbutton->url            = new moodle_url($workshop->example_url($example->id), array('edit' => 'on'));
        echo $OUTPUT->button($editbutton);
    }
    if (empty($delete)) {
        $deletebutton               = new html_form();
        $deletebutton->method       = 'get';
        $deletebutton->button->text = get_string('exampledelete', 'workshop');
        $deletebutton->url          = new moodle_url($workshop->example_url($example->id), array('delete' => 'on'));
        echo $OUTPUT->button($deletebutton);
    }
    echo $OUTPUT->container_end();
}

// and possibly display the example's review(s) - todo
echo $OUTPUT->footer();
