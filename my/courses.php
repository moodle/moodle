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
 * My Courses.
 *
 * - each user can currently have their own page (cloned from system and then customised)
 * - only the user can see their own dashboard
 * - users can add any blocks they want
 *
 * @package    core
 * @subpackage my
 * @copyright  2021 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/my/lib.php');

redirect_if_major_upgrade_required();

require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$context = context_system::instance();

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page(null, MY_PAGE_PUBLIC, MY_PAGE_COURSES)) {
    throw new Exception('mymoodlesetup');
}

// Start setting up the page.
$PAGE->set_context($context);
$PAGE->set_url('/my/courses.php');
$PAGE->add_body_classes(['limitedwidth', 'page-mycourses']);
$PAGE->set_pagelayout('mycourses');
$PAGE->has_secondary_navigation_setter(false);

$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title(get_string('mycourses'));
$PAGE->set_heading(get_string('mycourses'));
// Force the add block out of the default area.
$PAGE->theme->addblockposition  = BLOCK_ADDBLOCK_POSITION_CUSTOM;

// Add course management if the user has the capabilities for it.
$coursecat = core_course_category::user_top();
if ($coursecat->can_create_course() || $coursecat->has_manage_capability()) {
    $data = [
        'newcourseurl' => new moodle_url('/course/edit.php', ['category' => $coursecat->id]),
        'manageurl' => new moodle_url('/course/management.php', ['categoryid' => $coursecat->id]),
    ];
    $PAGE->add_header_action($OUTPUT->render_from_template('my/dropdown', $data));
}

echo $OUTPUT->header();

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();

// Trigger dashboard has been viewed event.
$eventparams = array('context' => $context);
$event = \core\event\mycourses_viewed::create($eventparams);
$event->trigger();
