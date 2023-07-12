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
 * Tiles course format.  Display the whole course as "tiles" made of course modules.
 *
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $PAGE, $USER;

// Horrible backwards compatible parameter aliasing..
if ($topic = optional_param('topic', 0, PARAM_INT)) {
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing..
$context = context_course::instance($course->id);

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

$courseformat = course_get_format($course);
// Make sure all sections are created.
$course = $courseformat->get_course();
$isediting = $PAGE->user_is_editing();
$renderer = $PAGE->get_renderer('format_tiles');
$ismobile = core_useragent::get_device_type() == core_useragent::DEVICETYPE_MOBILE ? 1 : 0;
$allowphototiles = get_config('format_tiles', 'allowphototiles');
$userstopjsnav = get_user_preferences('format_tiles_stopjsnav', 0);

// JS navigation and modals in Internet Explorer are not supported by this plugin so we disable JS nav here.
$usejsnav = !$userstopjsnav && get_config('format_tiles', 'usejavascriptnav') && !core_useragent::is_ie();

// Inline CSS may be requried if this course is using different tile colours to default - echo this first if so.
$templateable = new \format_tiles\output\inline_css_output($course, $ismobile, $usejsnav, $allowphototiles);
$data = $templateable->export_for_template($renderer);
echo $renderer->render_from_template('format_tiles/inline-css', $data);

if ($isediting) {
    if ($cmid = optional_param('labelconvert', 0, PARAM_INT)) {
        require_sesskey();
        require_once($CFG->dirroot . '/course/format/tiles/locallib.php');
        format_tiles_convert_label_to_page($cmid, $course);
    }

    // Check if we need to change any session params for teachers expanded section preferences.
    if (optional_param('expanded', 0, PARAM_INT) == 1) {
        // User is expanding all sections in course on command.
        $SESSION->editing_all_sections_expanded_course = $course->id;
        unset($SESSION->editing_last_edited_section);
    } else if (optional_param('expanded', 0, PARAM_INT) == -1) {
        // Cancel all epxanded if user cancels it.
        unset($SESSION->editing_all_sections_expanded_course);
        unset($SESSION->editing_last_edited_section);
    } else if ($secnum = optional_param('expand', 0, PARAM_INT)) {
        // User is expanding one section.
        unset($SESSION->editing_all_sections_expanded_course);
        if ($secnum == -1) {
            unset($SESSION->editing_last_edited_section);
        } else {
            $SESSION->editing_last_edited_section = $course->id . "-" . $secnum;
        }
    }
}

// We display the multi section page if the user is not requesting a specific single section.
// We also display it if user is requesting a specific section (URL &section=xx) with JS enabled.
// We know they have JS if $SESSION->format_tiles_jssuccessfullyused is set.
// In that case we show them the multi section page and use JS to open the section.
if (optional_param('canceljssession', false, PARAM_BOOL)) {
    // The user is shown a link to cancel the successful JS flag for this session in <noscript> tags if their JS is off.
    unset($SESSION->format_tiles_jssuccessfullyused);
}


if (display_multiple_section_page($displaysection, $usejsnav, $context, $isediting)) {
    $renderer->print_multiple_section_page($course, null, null, null, null);
} else {
    $SESSION->editing_last_edited_section = $course->id . "-" . $displaysection;
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
}

// Include format.js (required for dragging sections around).
$PAGE->requires->js('/course/format/tiles/format.js');

// Include amd module required for AJAX calls to change tile icon, filter buttons etc.
if (!empty($displaysection)) {
    $jssectionnum = $displaysection;
} else if (! $jssectionnum = optional_param('expand', 0, PARAM_INT)) {
    $jssectionnum = 0;
} else if (isset($SESSION->editing_last_edited_section)) {
    $jssectionnum = $SESSION->editing_last_edited_section;
}

$allowedmodmodals = format_tiles_allowed_modal_modules();

$jsparams = array(
    'courseId' => $course->id,
    'useJSNav' => $usejsnav, // See also lib.php page_set_course().
    'isMobile' => $ismobile,
    'jsSectionNum' => $jssectionnum,
    'displayFilterBar' => $course->displayfilterbar,
    'assumeDataStoreContent' => get_config('format_tiles', 'assumedatastoreconsent'),
    'reOpenLastSection' => get_config('format_tiles', 'reopenlastsection'),
    'userId' => $USER->id,
    'fitTilesToWidth' => get_config('format_tiles', 'fittilestowidth')
        && !optional_param("skipcheck", 0, PARAM_INT)
        && !isset($SESSION->format_tiles_skip_width_check),
    'enablecompletion' => $course->enablecompletion
);
if (!$isediting) {
    // Initalise the main JS module for non editing users.
    $PAGE->requires->js_call_amd(
        'format_tiles/course', 'init', $jsparams
    );
}
if ($isediting) {
    // Initalise the main JS module for editing users.
    $jsparams['pagetype'] = $PAGE->pagetype;
    $jsparams['allowphototiles'] = $allowphototiles;
    $jsparams['usesubtiles'] = get_config('format_tiles', 'allowsubtilesview') && $course->courseusesubtiles;
    $jsparams['areconvertinglabel'] = optional_param('labelconvert', 0, PARAM_INT);
    $jsparams['documentationurl'] = get_config('format_tiles', 'documentationurl');


    $PAGE->requires->js_call_amd('format_tiles/edit_course', 'init', $jsparams);
    if (strpos($PAGE->pagetype, 'course-view-') === 0 && $PAGE->theme->name == 'snap') {
        \core\notification::ERROR(
            get_string('snapwarning', 'format_tiles') . ' ' .
            html_writer::link(
                get_docs_url(get_string('snapwarning_help', 'format_tiles')),
                get_string('morehelp')
            )
        );
    }
}
// Now the modules which we want whether editing or not.

// If we are allowing course modules to be displayed in modal windows when clicked.
if (!$userstopjsnav && (count($allowedmodmodals['resources']) > 0 || count($allowedmodmodals['modules']) > 0)) {
    $PAGE->requires->js_call_amd(
        'format_tiles/course_mod_modal', 'init', array($course->id, $isediting)
    );
}
if ($course->enablecompletion) {
    $PAGE->requires->js_call_amd('format_tiles/completion', 'init',
        array(
            $course->id,
            get_string('complete-y-auto', 'format_tiles'),
        )
    );
}

/**
 * Should we display a multiple section page or not?
 * I.e. do we display all tiles on screen or just one open section?
 * @param int $displaysection the param to say if we are displaying one sec and if so which.
 * @param bool $usejsnav are we using JS nav or not.
 * @param \context_course $context the context we are in
 * @param bool $isediting are we editing or not.
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function display_multiple_section_page($displaysection, $usejsnav, $context, $isediting) {
    global $SESSION;
    if (empty($displaysection)) {
        // If the URL does not request a specific section page (&section=xx) we always show multiple secs.
        return true;
    }

    if (optional_param('singlesec', 0, PARAM_INT)) {
        // Singlesec param is appended to inplace editable links by format_tiles\inplace_editable_render_section_name().
        return false;
    }

    // Otherwise, even if URL requests single, we may show multiple in certain situations.
    if ($usejsnav && isset($SESSION->format_tiles_jssuccessfullyused)) {
        if (!$isediting && get_config('format_tiles', 'usejsnavforsinglesection')) {
            return true;
        }
    }
    return false;
}
