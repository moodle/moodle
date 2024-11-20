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

namespace mod_lti\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');

/**
 * External function to delete a course tool type.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_course_tool_type extends external_api {

    /**
     * Get parameter definition.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tooltypeid' => new external_value(PARAM_INT, 'Tool type ID'),
        ]);
    }

    /**
     * Delete a course tool type.
     *
     * @param int $tooltypeid the id of the course external tool type.
     * @return bool true
     * @throws \invalid_parameter_exception if the provided id refers to a site level tool which cannot be deleted.
     */
    public static function execute(int $tooltypeid): bool {

        ['tooltypeid' => $tooltypeid] = self::validate_parameters(self::execute_parameters(), ['tooltypeid' => $tooltypeid]);

        global $DB;
        $course = (int) $DB->get_field('lti_types', 'course', ['id' => $tooltypeid]);
        if ($course == get_site()->id) {
            throw new \invalid_parameter_exception('This is a site-level tool and cannot be deleted via this service');
        }

        $context = \context_course::instance($course);
        self::validate_context($context);
        require_capability('mod/lti:addcoursetool', $context);

        \lti_delete_type($tooltypeid);
        return true;
    }

    /**
     * Get service returns definition.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL, 'Success');
    }
}
