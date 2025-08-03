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
 * Display a course section.
 *
 * @package     core_course
 * @copyright   2023 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');

redirect_if_major_upgrade_required();

$sectionid = required_param('id', PARAM_INT);
// This parameter is used by the classic theme to force editing on.
$edit = optional_param('edit', -1, PARAM_BOOL);

if (!$section = $DB->get_record('course_sections', ['id' => $sectionid], '*')) {
    $url = new moodle_url('/');
    $PAGE->set_context(\core\context\system::instance());
    $PAGE->set_url($url);
    $PAGE->set_pagelayout('course');
    $PAGE->add_body_classes(['limitedwidth', 'single-section-page']);
    $PAGE->set_title(get_string('notfound', 'error'));
    $PAGE->set_heading($SITE->fullname);
    echo $OUTPUT->header();

    $errortext = new \core\output\notification(
            get_string('sectioncantbefound', 'error'),
            \core\output\notification::NOTIFY_ERROR
    );
    echo $OUTPUT->render($errortext);

    $button = new single_button($url, get_string('gobacktosite'), 'get', single_button::BUTTON_PRIMARY);
    $button->class = 'continuebutton';
    echo $OUTPUT->render($button);

    echo $OUTPUT->footer();
    die();
}

// Defined here to avoid notices on errors.
$PAGE->set_url('/course/section.php', ['id' => $sectionid]);

if ($section->course == SITEID) {
    // The home page is not a real course.
    redirect($CFG->wwwroot .'/?redirect=0');
}

$course = get_course($section->course);
// Fix course format if it is no longer installed.
$format = course_get_format($course);
$course->format = $format->get_format();
$format->set_sectionid($section->id);

// When the course format doesn't support sections, redirect to course page.
if (!course_format_uses_sections($course->format)) {
    redirect(new moodle_url('/course/view.php', ['id' => $course->id]));
}

// Prevent caching of this page to stop confusion when changing page after making AJAX changes.
$PAGE->set_cacheable(false);

context_helper::preload_course($course->id);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);

// Must set layout before getting section info. See MDL-47555.
$PAGE->set_pagelayout('course');
$PAGE->add_body_classes(['limitedwidth', 'single-section-page']);

// Get section details and check it exists.
$modinfo = get_fast_modinfo($course);
$sectioninfo = $modinfo->get_section_info($section->section, MUST_EXIST);

// Check user is allowed to see it.
if (!$sectioninfo->uservisible) {
    // Check if coursesection has conditions affecting availability and if
    // so, output availability info.
    if ($sectioninfo->visible && $sectioninfo->availableinfo) {
        $sectionname = get_section_name($course, $sectioninfo);
        $message = get_string('notavailablecourse', '', $sectionname);
        redirect(course_get_url($course), $message, null, \core\output\notification::NOTIFY_ERROR);
    } else {
        // Note: We actually already know they don't have this capability
        // or uservisible would have been true; this is just to get the
        // correct error message shown.
        require_capability('moodle/course:viewhiddensections', $context);
    }
}

$PAGE->set_pagetype('course-view-section-' . $course->format);
$PAGE->set_other_editing_capability('moodle/course:update');
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_other_editing_capability('moodle/course:activityvisibility');
$PAGE->set_other_editing_capability('moodle/course:sectionvisibility');
$PAGE->set_other_editing_capability('moodle/course:movesections');

$renderer = $PAGE->get_renderer('format_' . $course->format);

// This is used by the Classic theme to change the editing mode based on the 'edit' parameter value.
if (!isset($USER->editing)) {
    $USER->editing = 0;
}
if ($PAGE->user_allowed_editing()) {
    if (($edit == 1) && confirm_sesskey()) {
        $USER->editing = 1;
        $url = new moodle_url($PAGE->url, ['notifyeditingon' => 1]);
        redirect($url);
    } else if (($edit == 0) && confirm_sesskey()) {
        $USER->editing = 0;
        if (!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
            $USER->activitycopy = false;
            $USER->activitycopycourse = null;
        }
        redirect($PAGE->url);
    }
}

// This is used by the Classic theme, to display the Turn editing on/off button.
// We are currently keeping the button here from 1.x to help new teachers figure out what to do, even though the link also appears
// in the course admin block. It also means you can back out of a situation where you removed the admin block.
if ($PAGE->user_allowed_editing()) {
    $buttons = $OUTPUT->edit_button($PAGE->url);
    $PAGE->set_button($buttons);
}

// Make the title more specific when editing, for accessibility reasons.
$editingtitle = '';
if ($PAGE->user_is_editing()) {
    $editingtitle = 'editing';
}
$sectionname = $format->get_generic_section_name();
$sectiontitle = $format->get_section_name($section);
$PAGE->set_title(
    get_string(
        'coursesectiontitle' . $editingtitle,
        'moodle',
        ['course' => $course->fullname, 'sectiontitle' => $sectiontitle, 'sectionname' => $sectionname]
    )
);

// Add bulk editing control.
$bulkbutton = $renderer->bulk_editing_button($format);
if (!empty($bulkbutton)) {
    $PAGE->add_header_action($bulkbutton);
}

$outputclass = $format->get_output_classname('content');
/** @var \core_courseformat\output\local\content */
$sectionoutput = new $outputclass($format);

// Add to the header the control menu for the section.
if ($format->show_editor()) {
    $menu = $sectionoutput->get_page_header_action($renderer);
    if ($menu) {
        $PAGE->add_header_action($menu);
    }
    $sectionheading = $OUTPUT->container(
        $OUTPUT->render($format->inplace_editable_render_section_name($sectioninfo, false)),
        attributes: ['data-for' => 'section_title'],
    );
    $PAGE->set_heading($sectionheading, false, false);
} else {
    $PAGE->set_heading($sectiontitle);
}

$PAGE->set_secondary_navigation(false);

echo $OUTPUT->header();

// Show communication room status notification.
if (core_communication\api::is_available() && has_capability('moodle/course:update', $context)) {
    $communication = \core_communication\api::load_by_instance(
        $context,
        'core_course',
        'coursecommunication',
        $course->id
    );
    $communication->show_communication_room_status_notification();
}

$containerattributes = [];
if ($PAGE->user_is_editing()) {
    require_once($CFG->dirroot . '/backup/util/helper/async_helper.class.php');
    // Display a warning if asynchronous backups are pending for this course.
    if (async_helper::is_async_pending($course->id, 'course', 'backup')) {
        echo $OUTPUT->notification(get_string('pendingasyncedit', 'backup'), 'warning');
    }

    // Allow drag and drop in the course index.
    $containerattributes = [
        'data-courseindexdndallowed' => 'true',
    ];
}

echo $renderer->container_start('course-content', attributes: $containerattributes);

// Include course AJAX.
include_course_ajax($course, $modinfo->get_used_module_names());

echo $renderer->render($sectionoutput);

// Include course format javascript files.
$jsfiles = $format->get_required_jsfiles();
foreach ($jsfiles as $jsfile) {
    $PAGE->requires->js($jsfile);
}

echo $renderer->container_end();

// Trigger section viewed event.
course_section_view($context, $sectionid);

// Load the view JS module if completion tracking is enabled for this course.
$completion = new completion_info($course);
if ($completion->is_enabled()) {
    $PAGE->requires->js_call_amd('core_course/view', 'init');
}

echo $OUTPUT->footer();
