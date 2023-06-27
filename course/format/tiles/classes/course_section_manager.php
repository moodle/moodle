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
 * Course section manager class for format tiles.
 * @package    format_tiles
 * @copyright  2020 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles;

defined('MOODLE_INTERNAL') || die();

/**
 * Course section manager class for format tiles.
 * @package    format_tiles
 * @copyright  2020 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_section_manager {

    /**
     * For a course, check if it has mis-numbered sections and, if so, trigger a re-order.
     * @param int $courseid
     */
    public static function resolve_section_misnumbering($courseid) {
        global $DB, $CFG;
        // First check we don't have too many sections in the course.
        // We do not want to attempt a re-order if so, as may take too long.  (User must delete excess sections first).
        $maxsections = self::get_max_sections();

        $count = $DB->count_records('course_sections', array('course' => $courseid));
        if ($count > $maxsections + 1) {
            debugging('Too many sections to re-order', DEBUG_DEVELOPER);
            return false;
        }

        $context = \context_course::instance($courseid);
        require_capability('moodle/course:movesections', $context);

        $sections = $DB->get_records_sql(
            "SELECT section, id FROM {course_sections} WHERE course = :courseid ORDER BY section",
            array('courseid' => $courseid)
        );

        // Find if any section has a gap before it, and renumber course if one is found.
        if (count($sections) > 1) {
            require_once($CFG->dirroot . '/course/lib.php'); // Require course lib for move_section_to() function.
            $previoussectionnumber = 0;
            foreach ($sections as $section) {
                if ($section->section > $previoussectionnumber + 1) {
                    // We have found a mis-numbered section.
                    // So get the last section in the course and move it up one. This will cause Moodle to run its re-ordering algo.
                    $course = get_course($courseid);
                    $lastsection = end($sections);
                    $destination = array_keys($sections)[count($sections) - 2];
                    move_section_to($course, $lastsection->section, $destination);

                    // Then move it back to the end.
                    $maxsectionnum = $DB->get_field('course_sections', 'MAX(section)',  array('course' => $courseid));
                    $sectionnumtomoveback = $DB->get_field('course_sections', 'section',  array('id' => $lastsection->id));
                    move_section_to($course, $sectionnumtomoveback, $maxsectionnum);
                    return true;
                } else {
                    $previoussectionnumber = $section->section;
                }
            }
        }
        return false;
    }

    /**
     * Find all courses which have too many sections.
     * @param int $maxsections maximum number of course sections allowed.
     * @return array
     * @throws \dml_exception
     */
    public static function get_problem_courses ($maxsections) {
        global $DB;
        return $DB->get_records_sql(
            "SELECT
            c.id,
            c.fullname,
            COUNT(cs.id) as count_sections,
            MAX(cs.section) as max_section_number
            FROM {course_sections} cs
            JOIN {course} c on c.id = cs.course
            GROUP BY c.id
            HAVING MAX(section) > :maxsections ",
            array('maxsections' => $maxsections)
        );
    }

    /**
     * Get the URL the user needs to list problem courses in this environment.
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public static function get_list_problem_courses_url() {
        return new \moodle_url(
            '/course/format/tiles/admintools.php',
            array('action' => 'listproblemcourses', 'sesskey' => sesskey())
        );
    }

    /**
     * Schedule a task to delete all empty sections in a course.
     * @param int $courseid
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function schedule_empty_sec_deletion ($courseid) {
        global $USER;
        if (!has_capability('moodle/site:config', \context_system::instance())) {
            throw new \moodle_exception('You do not have permission to perform this action.');
        }
        set_config('delete_empty_sections_' . $courseid, 1, 'format_tiles');
        $task = new \format_tiles\task\delete_empty_sections();
        $task->set_component('format_tiles');
        $task->set_userid($USER->id);
        \core\task\manager::queue_adhoc_task($task, true);
    }

    /**
     * Cancel a previously scheduled task to delete all empty sections in a given course.
     * @param int $courseid
     */
    public static function cancel_empty_sec_deletion ($courseid) {
        unset_config('delete_empty_sections_' . $courseid, 'format_tiles');
    }

    /**
     *  If the user is an admin, render a button allowing them to use the experimental empty section delete tool.
     * @param int $courseid
     * @return string HTML button
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function get_schedule_button($courseid) {
        $isadmin = has_capability('moodle/site:config', \context_system::instance());
        $alreadyscheduled = get_config( 'format_tiles', 'delete_empty_sections_' . $courseid);
        if ($alreadyscheduled) {
            $message = get_string('scheduleddeleteemptysections', 'format_tiles');
            if ($isadmin) {
                return \html_writer::div(
                    $message . ' '
                    . \html_writer::link(
                        new \moodle_url(
                            '/course/format/tiles/admintools.php',
                            array('action' => 'canceldeleteemptysections', 'courseid' => $courseid, 'sesskey' => sesskey())
                        ),
                        get_string('canceltask', 'format_tiles'),
                        array('class' => 'btn btn-secondary ml-2')
                    )
                );
            } else {
                return \html_writer::div($message);
            }
        } else {
            if ($isadmin) {
                return \html_writer::link(
                    new \moodle_url(
                        '/course/format/tiles/admintools.php',
                        array('action' => 'deleteemptysections', 'courseid' => $courseid, 'sesskey' => sesskey())
                    ),
                    get_string('deleteemptytiles', 'format_tiles'),
                    array('class' => 'btn btn-secondary ml-2')
                );
            } else {
                // Only admin can use this feature.
                return '';
            }
        }

    }

    /**
     * Get all the sections in a course which are totally empty / unused (as they may need deleting).
     * @param int $courseid
     * @param int $startatsection
     * @return array
     * @throws \dml_exception
     */
    public static function get_empty_sections($courseid, $startatsection) {
        global $DB;
        return $DB->get_records_sql(
            "SELECT * FROM {course_sections}
                            WHERE course = :courseid AND section >= :startatsection
                            AND name is NULL AND (sequence IS NULL OR sequence = '')
                            AND availability IS NULL
                            ORDER BY section DESC",
            array('courseid' => $courseid, 'startatsection' => $startatsection)
        );
    }

    /**
     * Delete a course section and log it.
     * @param \stdClass $section
     * @param \context_course $coursecontext
     * @return bool whether deleted
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function delete_section($section, $coursecontext) {
        global $DB;

        // Double check section has no course modules before proceeding.
        // Why do this here? Because if we are deleting lots of sections, we want the check to be proximate in time to the deletion.
        // We do not check deletioninprogress field as we are being cautious and exiting whatever its value.
        $hascoursemods = $DB->record_exists_sql(
            'SELECT id from {course_modules} WHERE course = ? AND section = ?', [$section->course, $section->id]
        );
        if ($hascoursemods) {
            return false;
        }

        $result = $DB->delete_records('course_sections', array('id' => $section->id));

        if ($result) {
            $DB->delete_records('course_format_options', array('sectionid' => $section->id));
            $event = \core\event\course_section_deleted::create(
                array(
                    'objectid' => $section->id,
                    'courseid' => $coursecontext->instanceid,
                    'context' => $coursecontext,
                    'other' => array(
                        'sectionnum' => $section->section,
                        'sectionname' => '',
                    )
                )
            );
            $event->add_record_snapshot('course_sections', $section);
            $event->trigger();
            return true;
        }
        return false;
    }

    /**
     * Get the max number of sections allowed in a course.
     * @return int
     * @throws \dml_exception
     */
    public static function get_max_sections() {
        $maxsections = get_config('moodlecourse', 'maxsections');
        if (!$maxsections || !is_numeric($maxsections)) {
            $maxsections = 52;
        }
        return $maxsections;
    }

}
