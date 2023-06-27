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
 * Cards Format - A topics based format that uses card layout to diaply the content.
 *
 * @package format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/output/list_all_sections_summary_renderable.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/output/card_all_sections_summary_renderable.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/output/list_all_sections_renderable.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/output/list_one_section_renderable.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/output/card_one_section_renderable.php');

// Edwiser Course Format Usage Tracking (Edwiser Course Format Analytics).
$ranalytics = new \format_remuiformat\usage_tracking();
$ranalytics->send_usage_analytics();

$renderer = $PAGE->get_renderer('format_remuiformat');
// Backward Compatibility.
if ($topic = optional_param('topic', 0, PARAM_INT)) {
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}

// End backwards-compatible aliasing.
$coursecontext = context_course::instance($course->id);

// Retrieve course format option fields and add them to the $course object.
$course = course_get_format($course)->get_course();


if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure section 0 is created.
course_create_sections_if_missing($course, 0);

// Include JS Files Required.
$stringman = get_string_manager();
$strings = $stringman->load_component_strings('format_remuiformat', 'en');
$PAGE->requires->strings_for_js(array_keys($strings), 'format_remuiformat');

$section = optional_param('section', 0, PARAM_INT);
$baserenderer = $renderer->get_base_renderer();

// Get current course format.
$courseformat = course_get_format($course);
$settings = $courseformat->get_settings();
$rformat = $settings['remuicourseformat'];
$type = 'list';

if ($section) {
    // List Format -> One Section Page : render_list_one_section -> list_one_section.
    if ($course->remuicourseformat && $course->coursedisplay) {
        $renderer->render_list_one_section(
            new \format_remuiformat\output\format_remuiformat_list_one_section($course, $displaysection, $baserenderer)
        );
    }
}
// List Format -> All Section Summary Page : render_list_all_sections_summary -> list_all_sections_summary.
if ($course->remuicourseformat && $course->coursedisplay && !$section) {
    if ($USER->editing) {
        $renderer->render_list_all_sections(
            new \format_remuiformat\output\format_remuiformat_list_all_sections($course, $baserenderer)
        );
    } else {
        $renderer->render_list_all_sections_summary(
            new \format_remuiformat\output\format_remuiformat_list_all_sections_summary($course, $baserenderer)
        );
    }
} else if ($displaysection && !$course->remuicourseformat) {
    // Card Format -> One Section Page : render_card_one_section -> card_one_section.
    $type = 'card';
    $renderer->render_card_one_section(
        new \format_remuiformat\output\format_remuiformat_card_one_section($course, $displaysection, $baserenderer)
    );
} else if (!$displaysection) {
    // Card Format -> All Section Page : render_card_all_sections_summary -> card_all_sections_summary.
    if ($rformat == REMUI_CARD_FORMAT) {
        $type = 'card';
        $renderer->render_card_all_sections_summary(
            new \format_remuiformat\output\format_remuiformat_card_all_sections_summary($course, $baserenderer)
        );
    }

    // List Format -> All Section Page : render_list_all_sections -> list_all_sections.
    if ($rformat == REMUI_LIST_FORMAT) {
        $renderer->render_list_all_sections(
            new \format_remuiformat\output\format_remuiformat_list_all_sections($course, $baserenderer)
        );
    }
}
// Include course format js module.
$PAGE->requires->js('/course/format/remuiformat/format_' . $type . '.js');
