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
 * Display the course home page.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_course
 */

require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');

redirect_if_major_upgrade_required();

$id = optional_param('id', 0, PARAM_INT);
$name = optional_param('name', '', PARAM_TEXT);
$edit = optional_param('edit', -1, PARAM_BOOL);
$hide = optional_param('hide', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$show = optional_param('show', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$duplicatesection = optional_param('duplicatesection', 0, PARAM_INT);
$idnumber = optional_param('idnumber', '', PARAM_RAW);
$sectionid = optional_param('sectionid', 0, PARAM_INT);
$section = optional_param('section', null, PARAM_INT);
$expandsection = optional_param('expandsection', -1, PARAM_INT);
$move = optional_param('move', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$marker = optional_param('marker', -1 , PARAM_INT); // TODO remove this param as part of MDL-83530.
$switchrole = optional_param('switchrole', -1, PARAM_INT); // Deprecated, use course/switchrole.php instead.
$return = optional_param('return', 0, PARAM_LOCALURL);

$params = [];
if (!empty($name)) {
    $params = ['shortname' => $name];
} else if (!empty($idnumber)) {
    $params = ['idnumber' => $idnumber];
} else if (!empty($id)) {
    $params = ['id' => $id];
} else {
    throw new \moodle_exception('unspecifycourseid', 'error');
}

$course = $DB->get_record('course', $params, '*', MUST_EXIST);

$urlparams = ['id' => $course->id];

// Sectionid should get priority over section number.
if ($sectionid) {
    $section = $DB->get_field('course_sections', 'section', ['id' => $sectionid, 'course' => $course->id], MUST_EXIST);
}
if (!is_null($section)) {
    $urlparams['section'] = $section;
}
if ($expandsection !== -1) {
    $urlparams['expandsection'] = $expandsection;
}

$PAGE->set_url('/course/view.php', $urlparams); // Defined here to avoid notices on errors etc.

// Prevent caching of this page to stop confusion when changing page after making AJAX changes.
$PAGE->set_cacheable(false);

context_helper::preload_course($course->id);
$context = context_course::instance($course->id, MUST_EXIST);

// Remove any switched roles before checking login.
if ($switchrole == 0 && confirm_sesskey()) {
    role_switch($switchrole, $context);
}

require_login($course);

// Switchrole - sanity check in cost-order...
$resetuserallowedediting = false;
if ($switchrole > 0 && confirm_sesskey() &&
    has_capability('moodle/role:switchroles', $context)) {
    // Is this role assignable in this context?
    // Inquiring minds want to know.
    $aroles = get_switchable_roles($context);
    if (is_array($aroles) && isset($aroles[$switchrole])) {
        role_switch($switchrole, $context);
        // Double check that this role is allowed here.
        require_login($course);
    }
    // Reset course page state. This prevents some weird problems.
    $USER->activitycopy = false;
    $USER->activitycopycourse = null;
    unset($USER->activitycopyname);
    unset($SESSION->modform);
    $USER->editing = 0;
    $resetuserallowedediting = true;
}

// If course is hosted on an external server, redirect to corresponding
// url with appropriate authentication attached as parameter.
$hook = new \core_course\hook\before_course_viewed($course);
\core\hook\manager::get_instance()->dispatch($hook);

require_once($CFG->dirroot.'/calendar/lib.php'); // This is after login because it needs $USER.

// Must set layout before gettting section info. See MDL-47555.
$PAGE->set_pagelayout('course');
$PAGE->add_body_class('limitedwidth');

if ($section && $section > 0) {

    // Get section details and check it exists.
    $modinfo = get_fast_modinfo($course);
    $coursesections = $modinfo->get_section_info($section, MUST_EXIST);

    // Check user is allowed to see it.
    if (!$coursesections->uservisible) {
        // Check if coursesection has conditions affecting availability and if
        // so, output availability info.
        if ($coursesections->visible && $coursesections->availableinfo) {
            $sectionname = get_section_name($course, $coursesections);
            $message = get_string('notavailablecourse', '', $sectionname);
            redirect(course_get_url($course), $message, null, \core\output\notification::NOTIFY_ERROR);
        } else {
            // Note: We actually already know they don't have this capability
            // or uservisible would have been true; this is just to get the
            // correct error message shown.
            require_capability('moodle/course:viewhiddensections', $context);
        }
    }
}

// Fix course format if it is no longer installed.
$format = course_get_format($course);
$course->format = $format->get_format();

$PAGE->set_pagetype('course-view-' . $course->format);
$PAGE->set_other_editing_capability('moodle/course:update');
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_other_editing_capability('moodle/course:activityvisibility');
if (course_format_uses_sections($course->format)) {
    $PAGE->set_other_editing_capability('moodle/course:sectionvisibility');
    $PAGE->set_other_editing_capability('moodle/course:movesections');
}

// Preload course format renderer before output starts.
// This is a little hacky but necessary since
// format.php is not included until after output starts.
$renderer = $format->get_renderer($PAGE);

if ($resetuserallowedediting) {
    // Ugly hack.
    unset($PAGE->_user_allowed_editing);
}

if (!isset($USER->editing)) {
    $USER->editing = 0;
}
if ($PAGE->user_allowed_editing()) {
    if (($edit == 1) && confirm_sesskey()) {
        $USER->editing = 1;
        // Redirect to site root if Editing is toggled on frontpage.
        if ($course->id == SITEID) {
            redirect($CFG->wwwroot .'/?redirect=0');
        } else if (!empty($return)) {
            redirect($CFG->wwwroot . $return);
        } else {
            $url = new moodle_url($PAGE->url, ['notifyeditingon' => 1]);
            redirect($url);
        }
    } else if (($edit == 0) && confirm_sesskey()) {
        $USER->editing = 0;
        if (!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
            $USER->activitycopy = false;
            $USER->activitycopycourse = null;
        }
        // Redirect to site root if Editing is toggled on frontpage.
        if ($course->id == SITEID) {
            redirect($CFG->wwwroot .'/?redirect=0');
        } else if (!empty($return)) {
            redirect($CFG->wwwroot . $return);
        } else {
            redirect($PAGE->url);
        }
    }

    // TODO remove this if as part of MDL-83530.
    if (has_capability('moodle/course:sectionvisibility', $context)) {
        if ($hide && confirm_sesskey()) {
            debugging(
                'The hide param in course view is deprecated. Please use course/format/update.php instead.',
                DEBUG_DEVELOPER
            );
            set_section_visible($course->id, $hide, '0');
            if ($sectionid) {
                redirect(course_get_url($course, $section, ['navigation' => true]));
            } else {
                redirect($PAGE->url);
            }
        }

        if ($show && confirm_sesskey()) {
            debugging(
                'The show param in course view is deprecated. Please use course/format/update.php instead.',
                DEBUG_DEVELOPER
            );
            set_section_visible($course->id, $show, '1');
            if ($sectionid) {
                redirect(course_get_url($course, $section, ['navigation' => true]));
            } else {
                redirect($PAGE->url);
            }
        }
    }

    // TODO remove this if as part of MDL-83530.
    if ($marker >= 0 && confirm_sesskey()) {
        debugging(
            'The marker param in course view is deprecated. Please use course/format/update.php instead.',
            DEBUG_DEVELOPER
        );
        course_set_marker($course->id, $marker);
        if ($sectionid) {
            redirect(course_get_url($course, $section, ['navigation' => true]));
        } else {
            redirect($PAGE->url);
        }
    }

    if (
        !empty($section) && !empty($coursesections) && !empty($duplicatesection)
        && has_capability('moodle/course:update', $context) && confirm_sesskey()
    ) {
        $newsection = $format->duplicate_section($coursesections);
        redirect(course_get_url($course, $newsection->section));
    }

    // TODO remove this if as part of MDL-83530.
    if (
        !empty($section)
        && !empty($move)
        && has_capability('moodle/course:movesections', $context) && confirm_sesskey()
    ) {
        debugging(
            'The move param is deprecated. Please use the standard move modal instead.',
            DEBUG_DEVELOPER
        );
        $destsection = $section + $move;
        if (move_section_to($course, $section, $destsection)) {
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot . '/?redirect=0');
            } else {
                if ($format->get_course_display() == COURSE_DISPLAY_MULTIPAGE) {
                    redirect(course_get_url($course));
                } else {
                    redirect(course_get_url($course, $destsection));
                }
            }
        } else {
            echo $OUTPUT->notification('An error occurred while moving a section');
        }
    }
} else {
    $USER->editing = 0;
}

$SESSION->fromdiscussion = $PAGE->url->out(false);


if ($course->id == SITEID) {
    // This course is not a real course.
    redirect($CFG->wwwroot .'/?redirect=0');
}

// Determine whether the user has permission to download course content.
$candownloadcourse = \core\content::can_export_context($context, $USER);

// We are currently keeping the button here from 1.x to help new teachers figure out
// what to do, even though the link also appears in the course admin block.  It also
// means you can back out of a situation where you removed the admin block.
if ($PAGE->user_allowed_editing()) {
    $buttons = $OUTPUT->edit_button($PAGE->url);
    $PAGE->set_button($buttons);
}

$editingtitle = '';
if ($PAGE->user_is_editing()) {
    // Append this to the page title's lang string to get its equivalent when editing mode is turned on.
    $editingtitle = 'editing';
}

// If viewing a section, make the title more specific.
if ($section && $section > 0 && course_format_uses_sections($course->format)) {
    $sectionname = $format->get_generic_section_name();
    $sectiontitle = $format->get_section_name($section);
    $PAGE->set_title(
        get_string(
            'coursesectiontitle' . $editingtitle,
            'moodle',
            ['course' => $course->fullname, 'sectiontitle' => $sectiontitle, 'sectionname' => $sectionname]
        )
    );
} else {
    $PAGE->set_title(get_string('coursetitle' . $editingtitle, 'moodle', ['course' => $course->fullname]));
}

// Add bulk editing control.
$bulkbutton = $renderer->bulk_editing_button($format);
if (!empty($bulkbutton)) {
    $PAGE->add_header_action($bulkbutton);
}

$PAGE->set_heading($course->fullname);

// Make sure that section 0 exists (this function will create one if it is missing).
course_create_sections_if_missing($course, 0);

// Get information about course modules and existing module types.
// format.php in course formats may rely on presence of these variables.
$modinfo = get_fast_modinfo($course);
$modnames = get_module_types_names();
$modnamesplural = get_module_types_names(true);
$modnamesused = $modinfo->get_used_module_names();
$mods = $modinfo->get_cms();
$sections = $modinfo->get_section_info_all();

// Include course AJAX. This should be done before starting the UI
// to allow page header, blocks, or drawers use the course editor.
include_course_ajax($course, $modnamesused);

echo $OUTPUT->header();

// Show communication room status notification.
if (has_capability('moodle/course:update', $context)) {
    core_communication\helper::get_course_communication_status_notification($course);
}

if ($USER->editing == 1) {

    // MDL-65321 The backup libraries are quite heavy, only require the bare minimum.
    require_once($CFG->dirroot . '/backup/util/helper/async_helper.class.php');

    if (async_helper::is_async_pending($id, 'course', 'backup')) {
        echo $OUTPUT->notification(get_string('pendingasyncedit', 'backup'), 'warning');
    }
}

// Course wrapper start.
echo html_writer::start_tag('div', ['class' => 'course-content']);

// CAUTION, hacky fundamental variable defintion to follow!
// Note that because of the way course fromats are constructed though
// inclusion we pass parameters around this way.
$displaysection = $section;

// Include the actual course format.
require($CFG->dirroot .'/course/format/'. $course->format .'/format.php');
// Content wrapper end.

echo html_writer::end_tag('div');

// Trigger course viewed event.
// We don't trust $context here. Course format inclusion above executes in the global space. We can't assume
// anything after that point.
course_view(context_course::instance($course->id), $section);

// If available, include the JS to prepare the download course content modal.
if ($candownloadcourse) {
    $PAGE->requires->js_call_amd('core_course/downloadcontent', 'init');
}

// Load the view JS module if completion tracking is enabled for this course.
$completion = new completion_info($course);
if ($completion->is_enabled()) {
    $PAGE->requires->js_call_amd('core_course/view', 'init');
}

echo $OUTPUT->footer();
