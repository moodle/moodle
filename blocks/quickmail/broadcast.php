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
    'courseid' => SITEID,
    'draftid' => optional_param('draftid', 0, PARAM_INT),
    'page' => optional_param('page', 0, PARAM_INT),
    'per_page' => optional_param('per_page', 20, PARAM_INT),
    'sort_by' => optional_param('sort_by', 'lastname', PARAM_ALPHA),
    'sort_dir' => optional_param('sort_dir', 'asc', PARAM_ALPHA)
];

// Handle authentication.
require_login();
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/blocks/quickmail/broadcast.php', $pageparams));

// Throw an exception if user does not have capability to broadcast messages.
block_quickmail_plugin::require_user_can_send('broadcast', $USER, $systemcontext, 'broadcast');

// Get (site) course.
$course = get_course($pageparams['courseid']);

// Construct the page.
$PAGE->set_pagetype('block-quickmail');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('broadcast'));
$PAGE->navbar->add(block_quickmail_string::get('pluginname'));
$PAGE->navbar->add(block_quickmail_string::get('broadcast'));
$PAGE->set_heading(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('broadcast'));
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
            // @TODO - Notify user that message was already sent??
            $draftmessage = null;
            $pageparams['draftid'] = 0;
        }
    }
} else {
    $draftmessage = null;
}

// Instantiate user filter for recipient filtering.
$broadcastrecipientfilter = block_quickmail_broadcast_recipient_filter::make($pageparams, $draftmessage);

// Intantiate the form.
$broadcastform = \block_quickmail\forms\broadcast_message_form::make(
    $systemcontext,
    $USER,
    $course,
    $draftmessage
);

// Handle the request.
$request = block_quickmail_request::for_route('broadcast')->with_form($broadcastform);

// If a POST was submitted, attempt to take appropriate actions.
try {
    // Cancel the request.
    if ($request->is_form_cancellation()) {

        // Clear any recipient user filtering session data.
        $broadcastrecipientfilter->clear_session();

        // Redirect back to home page.
        $request->redirect_to_url('/my');

        // Send the message.
    } else if ($request->to_send_message()) {

        $sendastask = \block_quickmail_config::block('send_as_tasks');
        $message = \block_quickmail\messenger\messenger::broadcast(
                       $USER,
                       $course,
                       $broadcastform->get_data(),
                       $broadcastrecipientfilter,
                       $draftmessage,
                       $sendastask);

        // Clear any recipient user filtering session data.
        $broadcastrecipientfilter->clear_session();

        // Resolve redirect message.
        if ($message->is_sent_message()) {
            $redirectmessage = 'message_sent_now';
        } else if ($message->is_queued_message()) {
            $redirectmessage = 'message_queued';
        } else {
            $redirectmessage = 'message_sent_asap';
        }

        $request->redirect_as_success(block_quickmail_string::get($redirectmessage, $course->fullname), '/my');

        // Save the draft.
    } else if ($request->to_save_draft()) {

        // Attempt to save draft, handle any exceptions.
        $message = \block_quickmail\messenger\messenger::save_broadcast_draft(
                       $USER,
                       $course,
                       $broadcastform->get_data(),
                       $broadcastrecipientfilter,
                       $draftmessage);

        // Clear any recipient user filtering session data.
        $broadcastrecipientfilter->clear_session();

        $request->redirect_as_info(block_quickmail_string::get(
                                                               'redirect_back_to_course_from_message_after_save',
                                                               $course->fullname), '/my');
    }
} catch (\block_quickmail\exceptions\validation_exception $e) {
    $broadcastform->set_error_exception($e);
} catch (\block_quickmail\exceptions\critical_exception $e) {
    throw new moodle_exception('critical_error', 'block_quickmail');
}

// Render the page.
$renderedbroadcastform = $renderer->broadcast_message_component([
    'context' => $systemcontext,
    'user' => $USER,
    'course' => $course,
    'broadcast_form' => $broadcastform,
]);

$renderedbroadcastrecipientfilterresults = $renderer->broadcast_recipient_filter_results_component([
    'broadcast_recipient_filter' => $broadcastrecipientfilter
]);

echo $OUTPUT->header();
$broadcastform->render_error_notification();

// Begin rendering user filter/results.
$broadcastrecipientfilter->render_add();
$broadcastrecipientfilter->render_active();

if ($broadcastrecipientfilter->get_result_user_count()) {
    // Pagination bar (if appropriate).
    if ($broadcastrecipientfilter->get_result_user_count() > $pageparams['per_page']) {
        $broadcastrecipientfilter->render_paging_bar();
    }

    // Table of displayed users.
    echo $renderedbroadcastrecipientfilterresults;

    // Pagination bar (if appropriate).
    if ($broadcastrecipientfilter->get_result_user_count() > $pageparams['per_page']) {
        $broadcastrecipientfilter->render_paging_bar();
    }
}

echo $renderedbroadcastform;
echo $OUTPUT->footer();
