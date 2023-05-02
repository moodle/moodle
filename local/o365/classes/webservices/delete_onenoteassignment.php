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
 * Delete assignment API class.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\webservices;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/course/modlib.php');

/**
 * Delete assignment API class.
 */
class delete_onenoteassignment extends \external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters The parameters object for this webservice method.
     */
    public static function assignment_delete_parameters() {
        return new \external_function_parameters([
            'data' => new \external_single_structure([
                'coursemodule' => new \external_value(PARAM_INT, 'course module id'),
                'course' => new \external_value(PARAM_INT, 'course id'),
            ])
        ]);
    }

    /**
     * Performs assignment read.
     *
     * @param array $data The incoming data parameter.
     * @return array An array of parameters, if successful.
     */
    public static function assignment_delete($data) {
        global $DB;

        $params = self::validate_parameters(self::assignment_delete_parameters(), ['data' => $data]);
        $params = $params['data'];

        list($course, $module, $assign) = \local_o365\webservices\utils::verify_assignment($params['coursemodule'],
            $params['course']);

        $context = \context_course::instance($params['course']);
        self::validate_context($context);

        // Course_delete_module will throw exception if error, so we can return true b/c if we get there it was successful.
        course_delete_module($module->id);
        return ['result' => true];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure Object describing return parameters for this webservice method.
     */
    public static function assignment_delete_returns() {
        $params = [
            'result' => new \external_value(PARAM_BOOL, 'success/failure'),
        ];
        return new \external_single_structure($params);
    }
}
