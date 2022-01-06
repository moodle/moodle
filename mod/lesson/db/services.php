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
 * Lesson external functions and service definitions.
 *
 * @package    mod_lesson
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'mod_lesson_get_lessons_by_courses' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_lessons_by_courses',
        'description'   => 'Returns a list of lessons in a provided list of courses,
                            if no list is provided all lessons that the user can view will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'mod_lesson_get_lesson_access_information' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_lesson_access_information',
        'description'   => 'Return access information for a given lesson.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'mod_lesson_view_lesson' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'view_lesson',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_questions_attempts' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_questions_attempts',
        'description'   => 'Return the list of questions attempts in a given lesson.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_user_grade' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_user_grade',
        'description'   => 'Return the final grade in the lesson for the given user.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_user_attempt_grade' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_user_attempt_grade',
        'description'   => 'Return grade information in the attempt for a given user.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_content_pages_viewed' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_content_pages_viewed',
        'description'   => 'Return the list of content pages viewed by a user during a lesson attempt.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_user_timers' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_user_timers',
        'description'   => 'Return the timers in the current lesson for the given user.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_pages' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_pages',
        'description'   => 'Return the list of pages in a lesson (based on the user permissions).',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_launch_attempt' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'launch_attempt',
        'description'   => 'Starts a new attempt or continues an existing one.',
        'type'          => 'write',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_page_data' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_page_data',
        'description'   => 'Return information of a given page, including its contents.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_process_page' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'process_page',
        'description'   => 'Processes page responses.',
        'type'          => 'write',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_finish_attempt' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'finish_attempt',
        'description'   => 'Finishes the current attempt.',
        'type'          => 'write',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_attempts_overview' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_attempts_overview',
        'description'   => 'Get a list of all the attempts made by users in a lesson.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:viewreports',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_user_attempt' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_user_attempt',
        'description'   => 'Return information about the given user attempt (including answers).',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:viewreports',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_pages_possible_jumps' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_pages_possible_jumps',
        'description'   => 'Return all the possible jumps for the pages in a given lesson.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_lesson_get_lesson' => array(
        'classname'     => 'mod_lesson_external',
        'methodname'    => 'get_lesson',
        'description'   => 'Return information of a given lesson.',
        'type'          => 'read',
        'capabilities'  => 'mod/lesson:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);
