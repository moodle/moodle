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
];

$course = get_course($pageparams['courseid']);

// Authentication.
require_course_login($course, false);
$coursecontext = context_course::instance($course->id);
$PAGE->set_context($coursecontext);
$PAGE->set_url(new moodle_url('/blocks/quickmail/create_notification.php', $pageparams));

// Throw an exception if user does not have capability to create notifications.
block_quickmail_plugin::require_user_can_create_notifications($USER, $coursecontext);

// Construct the page.
$PAGE->set_pagetype('block-quickmail');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('create_notification'));
$PAGE->set_heading(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('create_notification'));
$PAGE->navbar->add(block_quickmail_string::get('pluginname'),
    new moodle_url('/blocks/quickmail/qm.php', array('courseid' => $course->id)));
$PAGE->navbar->add(block_quickmail_string::get('create_notification'));
$PAGE->requires->css(new moodle_url('/blocks/quickmail/style.css'));

block_quickmail\controllers\create_notification_controller::handle($PAGE, [
    'context' => $coursecontext,
    'user' => $USER,
    'course' => $course
]);
