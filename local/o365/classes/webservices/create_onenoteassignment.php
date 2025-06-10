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
 * Create assignment API class.
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
 * Create assignment API class.
 */
class create_onenoteassignment extends \external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters The parameters object for this webservice method.
     */
    public static function assignment_create_parameters() {
        return new \external_function_parameters([
            'data' => new \external_single_structure([
                'name' => new \external_value(PARAM_TEXT, 'name'),
                'course' => new \external_value(PARAM_INT, 'course id'),
                'intro' => new \external_value(PARAM_TEXT, 'intro', VALUE_DEFAULT, ''),
                'section' => new \external_value(PARAM_INT, 'section', VALUE_DEFAULT, 0),
                'visible' => new \external_value(PARAM_BOOL, 'visible', VALUE_DEFAULT, false),
                'duedate' => new \external_value(PARAM_INT, 'duedate', VALUE_DEFAULT, 0),
            ])
        ]);
    }

    /**
     * Performs assignment creation.
     *
     * @param array $data The incoming data parameter.
     * @return array An array of parameters, if successful.
     */
    public static function assignment_create($data) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::assignment_create_parameters(), ['data' => $data]);
        $params = $params['data'];

        $context = \context_course::instance($params['course']);
        self::validate_context($context);

        $defaults = [
            'submissiondrafts' => 0,
            'requiresubmissionstatement' => 0,
            'sendnotifications' => 0,
            'sendlatenotifications' => 0,
            'duedate' => 0,
            'cutoffdate' => 0,
            'allowsubmissionsfromdate' => 0,
            'grade' => 0,
            'gradingduedate' => 0,
            'completionsubmit' => 0,
            'teamsubmission' => 0,
            'requireallteammemberssubmit' => 0,
            'blindmarking' => 0,
            'markingworkflow' => 0,
            'markingallocation' => 0,
            'grade' => (isset($CFG->gradepointdefault)) ? $CFG->gradepointdefault : 100,
        ];

        $course = get_course($params['course']);

        $modinfo = [
            'modulename' => 'assign',
            'course' => $course->id,
            'section' => $params['section'],
            'visible' => (int)$params['visible'],
            'duedate' => (int)$params['duedate'],
            'name' => $params['name'],
            'cmidnumber' => '',
            'introeditor' => ['text' => $params['intro'], 'format' => FORMAT_HTML, 'itemid' => null],
            'assignsubmission_onenote_enabled' => 1,
            'assignsubmission_onenote_maxfiles' => 1,
            'assignsubmission_onenote_maxsizebytes' => 1024,
        ];

        $modinfo = array_merge($defaults, $modinfo);
        $modinfo = create_module((object)$modinfo, $course);

        $modinfo = \local_o365\webservices\utils::get_assignment_return_info($modinfo->coursemodule, $modinfo->course);
        return ['data' => [$modinfo]];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure Object describing return parameters for this webservice method.
     */
    public static function assignment_create_returns() {
        return \local_o365\webservices\utils::get_assignment_return_info_schema();
    }
}
