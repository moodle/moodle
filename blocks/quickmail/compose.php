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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/quickmail/lib.php');

$pageparams = [
    'courseid' => required_param('courseid', PARAM_INT),
    'draftid' => optional_param('draftid', 0, PARAM_INT),
];

$course = get_course($pageparams['courseid']);

// Authentication.
require_course_login($course, false);
$coursecontext = context_course::instance($course->id);
$PAGE->set_context($coursecontext);
$PAGE->set_url(new moodle_url('/blocks/quickmail/compose.php', $pageparams));
$PAGE->requires->js_call_amd('block_quickmail/compose', 'init');

// Throw an exception if user does not have capability to compose messages.
block_quickmail_plugin::require_user_can_send('compose', $USER, $coursecontext, 'compose');

// Get course user/role/group data for selection.
$courseuserdata = block_quickmail_plugin::get_compose_message_recipients(
    $course,
    $USER,
    $coursecontext,
    block_quickmail_plugin::user_prefers_multiselect_recips($USER)
);

// Construct the page.
$PAGE->set_pagetype('block-quickmail');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('compose'));
$PAGE->navbar->add(
    block_quickmail_string::get('pluginname'),
    new moodle_url('/blocks/quickmail/qm.php',
    array('courseid' => $course->id))
);
$PAGE->navbar->add(block_quickmail_string::get('compose'));
$PAGE->set_heading(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('compose'));
$PAGE->requires->css(new moodle_url('/blocks/quickmail/style.css'));

$renderer = $PAGE->get_renderer('block_quickmail');

// If a draft id was passed.
if ($pageparams['draftid']) {
    // Attempt to fetch the draft which must belong to this course and user.
    $draftmessage = block_quickmail\repos\draft_repo::find_for_user_course_or_null($pageparams['draftid'], $USER->id, $course->id);

    // If no valid draft message was found, reset param.
    if (empty($draftmessage)) {
        $pageparams['draftid'] = 0;
    } else {
        // Make sure this draft message has not already been sent.
        if ($draftmessage->is_sent_message()) {
            // Reset the passed param to 0.
            // TODO - Notify user that message was already sent?
            $draftmessage = null;
            $pageparams['draftid'] = 0;
        }
    }
} else {
    $draftmessage = null;
}

// File attachment handling.
// Get the attachments draft area id.
$attachmentsdraftitemid = file_get_submitted_draft_itemid('attachments');

// Prepare the draft area with any existing, relevant files.
file_prepare_draft_area(
    $attachmentsdraftitemid,
    $coursecontext->id,
    'block_quickmail',
    'attachments',
    $pageparams['draftid'] ?: null,
    block_quickmail_config::get_filemanager_options()
);

$messagedraftitemid = file_get_submitted_draft_itemid('message_editor');
$messagebody = $draftmessage ? $draftmessage->get('body') : "";
$messagebody = file_prepare_draft_area(
    $messagedraftitemid,
   $coursecontext->id,
   'block_quickmail',
   'message_editor',
   $pageparams['draftid'] ?: null,
   block_quickmail_config::get_filemanager_options(),
   $messagebody
);

if ($draftmessage) {
    $draftmessage->set('body', $messagebody);
}

// Instantiate the form.
$composeform = \block_quickmail\forms\compose_message_form::make(
    $coursecontext,
    $USER,
    $course,
    $courseuserdata,
    $draftmessage,
    $attachmentsdraftitemid
);

// Handle the Request.
$request = block_quickmail_request::for_route('compose')->with_form($composeform);

// If a POST was submitted, attempt to take appropriate actions..
try {
    // Cancel.
    if ($request->is_form_cancellation()) {

        // Redirect back to course page.
        $request->redirect_to_url('/course/view.php', ['id' => $course->id]);

        // Send.
    } else if ($request->to_send_message()) {

        $sendastask = \block_quickmail_config::block('send_as_tasks');

        $message = \block_quickmail\messenger\messenger::compose(
            $USER,
            $course,
            $composeform->get_data(),
            $draftmessage,
            $sendastask
        );

        // Resolve redirect message.
        if ($message->is_sent_message()) {
            $redirectmessage = 'message_sent_now';
        } else if ($message->is_queued_message()) {
            $redirectmessage = 'message_queued';
        } else {
            $redirectmessage = 'message_sent_asap';
        }

        // Redirect back to course page with message.
        $request->redirect_as_success(block_quickmail_string::get(
            $redirectmessage,
            $course->fullname), '/course/view.php', ['id' => $course->id]);

        // Save a draft.
    } else if ($request->to_save_draft()) {

        // Attempt to save draft, handle exceptions.
        $message = \block_quickmail\messenger\messenger::save_compose_draft(
            $USER,
            $course,
            $composeform->get_data(),
            $draftmessage
        );

        // Redirect back to course page.
        $request->redirect_as_info(block_quickmail_string::get(
            'redirect_back_to_course_from_message_after_save',
            $course->fullname), '/course/view.php', ['id' => $course->id]);
    }
} catch (\block_quickmail\exceptions\validation_exception $e) {
    $composeform->set_error_exception($e);
} catch (\block_quickmail\exceptions\critical_exception $e) {
    throw new moodle_exception('critical_error', 'block_quickmail');
}

// Render page.
$renderedcomposeform = $renderer->compose_message_component([
    'context' => $coursecontext,
    'user' => $USER,
    'course' => $course,
    'compose_form' => $composeform,
]);

echo $OUTPUT->header();
$composeform->render_error_notification();
echo $renderedcomposeform;
echo $OUTPUT->footer();
