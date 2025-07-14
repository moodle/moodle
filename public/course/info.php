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
 * Displays external information about a course
 * @package    core_course
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once("../config.php");
    require_once("lib.php");

    $id   = optional_param('id', false, PARAM_INT); // Course id
    $name = optional_param('name', false, PARAM_RAW); // Course short name

    if (!$id and !$name) {
        throw new \moodle_exception("unspecifycourseid");
    }

    if ($name) {
        if (!$course = $DB->get_record("course", array("shortname"=>$name))) {
            throw new \moodle_exception("invalidshortname");
        }
    } else {
        if (!$course = $DB->get_record("course", array("id"=>$id))) {
            throw new \moodle_exception("invalidcourseid");
        }
    }

    $site = get_site();

    if ($CFG->forcelogin) {
        require_login();
    }

    $context = context_course::instance($course->id);
    if (!core_course_category::can_view_course_info($course) && !is_enrolled($context, null, '', true)) {
        throw new \moodle_exception('cannotviewcategory', '', $CFG->wwwroot .'/');
    }

    $PAGE->set_course($course);
    $PAGE->set_pagelayout('incourse');
    $PAGE->set_url('/course/info.php', array('id' => $course->id));

    $strcourseinfo = get_string('courseinfo');
    $PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, [$strcourseinfo, $course->fullname]));
    $PAGE->set_heading($strcourseinfo);

    $PAGE->navbar->add(get_string('summary'));

    echo $OUTPUT->header();

    // print enrol info
    if ($texts = enrol_get_course_description_texts($course)) {
        echo $OUTPUT->box_start('generalbox icons');
        echo implode($texts);
        echo $OUTPUT->box_end();
    }

    /** @var core_course_renderer $courserenderer */
    $courserenderer = $PAGE->get_renderer('core', 'course');
    echo $courserenderer->course_info_box($course);

    echo "<br />";

    // Trigger event, course information viewed.
    $eventparams = array('context' => $context, 'objectid' => $course->id);
    $event = \core\event\course_information_viewed::create($eventparams);
    $event->trigger();

    echo $OUTPUT->footer();


