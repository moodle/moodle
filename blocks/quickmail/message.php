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
    'message_id' => required_param('id', PARAM_INT),
];

// Authentication.
require_login();

// Check that the message has not been deleted.
if (!$message = \block_quickmail\persistents\message::find_or_null($pageparams['message_id'])) {
    redirect(new \moodle_url('/my'),
        block_quickmail_string::get('redirect_back_from_message_detail_message_deleted'),
        2,
        \core\output\notification::NOTIFY_ERROR);
}

// Check that the user can view this message.
if ($message->get('user_id') !== $USER->id) {
    redirect(new \moodle_url('/my'),
        block_quickmail_string::get('redirect_back_from_message_detail_no_access'),
        2,
        \core\output\notification::NOTIFY_ERROR);
}

$usercontext = context_user::instance($USER->id);
$PAGE->set_context($usercontext);
$PAGE->set_url(new moodle_url('/blocks/quickmail/message.php', $pageparams));

// Construct the page.
$PAGE->set_pagetype('block-quickmail');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('view_message_detail'));

$courseid = $message->get('course_id');
$PAGE->navbar->add(block_quickmail_string::get('pluginname'),
    new moodle_url('/blocks/quickmail/qm.php', array('courseid' => $courseid)));
$PAGE->navbar->add(block_quickmail_string::get('view_message_detail'));
$PAGE->set_heading(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('view_message_detail'));
$PAGE->requires->css(new moodle_url('/blocks/quickmail/style.css'));

block_quickmail\controllers\view_message_controller::handle($PAGE, [
    'context' => $usercontext,
    'user' => $USER,
    'message' => $message,
]);
