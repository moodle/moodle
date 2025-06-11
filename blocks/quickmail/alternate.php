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
    'course_id' => optional_param('courseid', 0, PARAM_INT),
    'action' => optional_param('action', '', PARAM_TEXT),
    'alternate_id' => optional_param('id', 0, PARAM_INT),
    'token' => optional_param('token', '', PARAM_TEXT),
];

// Authentication and identifying context.
require_login();

// If we're scoping to a specific course.
if ($pageparams['course_id']) {
    // If we're scoping to the site level course.
    if ($pageparams['course_id'] == SITEID) {
        // Throw an exception if user does not have site-level capability for this block.
        block_quickmail_plugin::require_user_has_course_message_access($USER, $pageparams['course_id'], 'alternate');
        // Otherwise, we're scoping to a course.
    } else {
        // Throw an exception if user does not have capability of having alternates.
        block_quickmail_plugin::require_user_capability(
            'allowalternate',
            $USER,
            context_course::instance($pageparams['course_id'])
        );
    }
}

$usercontext = context_user::instance($USER->id);
$PAGE->set_context($usercontext);
$PAGE->set_url(new moodle_url('/blocks/quickmail/alternate.php', $pageparams));

// Construct the page.
$PAGE->set_pagetype('block-quickmail');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('manage_alternates'));

if ($pageparams['course_id']) {
    $PAGE->navbar->add(
        block_quickmail_string::get('pluginname'),
        new moodle_url('/blocks/quickmail/qm.php',
        array('courseid' => $pageparams['course_id']))
    );
}

$PAGE->navbar->add(block_quickmail_string::get('manage_alternates'));
$PAGE->set_heading(block_quickmail_string::get('pluginname') . ': ' . block_quickmail_string::get('manage_alternates'));
$PAGE->requires->css(new moodle_url('/blocks/quickmail/style.css'));
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('/blocks/quickmail/js/alternate-form.js'));

block_quickmail\controllers\alternate_index_controller::handle($PAGE, [
    'context' => $usercontext,
    'user' => $USER,
    'course_id' => $pageparams['course_id'],
    'page_params' => $pageparams
], $pageparams['action']);
