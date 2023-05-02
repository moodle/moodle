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
 * Webservices utilities.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\webservices;

use \local_o365\webservices\exception as exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Webservices utilities.
 */
class utils {
    /**
     * Verify this is an assignment we can affect.
     *
     * This checks whether the given module ID is a OneNote assignment.
     *
     * @param int $coursemoduleid The course module ID.
     * @param int $courseid
     * @return array Whether we can proceed or not.
     */
    public static function verify_assignment($coursemoduleid, $courseid) {
        global $DB;

        list($course, $module, $assign) = static::get_assignment_info($coursemoduleid, $courseid);

        require_capability('moodle/course:manageactivities', \context_module::instance($module->id));

        $pluginconfigparams = [
            'assignment' => $assign->id,
            'plugin' => 'onenote',
            'subtype' => 'assignsubmission',
            'name' => 'enabled',
        ];
        $assignpluginconfig = $DB->get_record('assign_plugin_config', $pluginconfigparams);

        if (empty($assignpluginconfig) || empty($assignpluginconfig->value)) {
            throw new exception\invalidassignment();
        }

        return [$course, $module, $assign];
    }

    /**
     * Get the external structure schema when returning information about an assignment.
     *
     * @return \external_single_structure The return data schema.
     */
    public static function get_assignment_return_info_schema() {
        $params = [
            'data' => new \external_multiple_structure(
                new \external_single_structure([
                    'course' => new \external_value(PARAM_INT, 'course id'),
                    'coursemodule' => new \external_value(PARAM_INT, 'coursemodule id'),
                    'name' => new \external_value(PARAM_TEXT, 'name'),
                    'intro' => new \external_value(PARAM_TEXT, 'intro'),
                    'section' => new \external_value(PARAM_INT, 'section'),
                    'visible' => new \external_value(PARAM_INT, 'visible'),
                    'instance' => new \external_value(PARAM_INT, 'instance id'),
                ])
            ),
        ];
        return new \external_single_structure($params);
    }

    /**
     * Get assignment, module, and course information based on a coursemoduleid and courseid.
     *
     * @param int $coursemoduleid The course module ID.
     * @param int $courseid The course id the module belongs to.
     * @return array Array of assignment information, following the same schema as get_assignment_info_schema.
     */
    public static function get_assignment_info($coursemoduleid, $courseid) {
        global $DB;
        $course = get_course($courseid);

        $module = $DB->get_record('course_modules', ['id' => $coursemoduleid]);
        if (empty($module)) {
            throw new exception\modulenotfound();
        }

        $assign = $DB->get_record('assign', ['id' => $module->instance]);
        if (empty($assign)) {
            throw new exception\assignnotfound();
        }
        return [$course, $module, $assign];
    }

    /**
     * Get assignment info to return when returning assignment info.
     *
     * @param int $coursemoduleid The course module ID.
     * @param int $courseid The course id the module belongs to.
     * @return array Array of assignment information, following the same schema as get_assignment_info_schema.
     */
    public static function get_assignment_return_info($coursemoduleid, $courseid) {
        list($course, $module, $assign) = static::get_assignment_info($coursemoduleid, $courseid);
        return [
            'course' => $course->id,
            'coursemodule' => $module->id,
            'name' => $assign->name,
            'intro' => $assign->intro,
            'section' => $module->section,
            'visible' => $module->visible,
            'instance' => $module->instance,
        ];
    }
}
