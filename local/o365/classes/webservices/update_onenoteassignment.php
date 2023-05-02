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
 * Update assignment API class.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\webservices;

use \local_o365\webservices\exception as exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/course/modlib.php');

/**
 * Update assignment API class.
 */
class update_onenoteassignment extends \external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters The parameters object for this webservice method.
     */
    public static function assignment_update_parameters() {
        return new \external_function_parameters([
            'data' => new \external_single_structure([
                'coursemodule' => new \external_value(PARAM_INT, 'course module id'),
                'course' => new \external_value(PARAM_INT, 'course id'),
                'name' => new \external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, null),
                'intro' => new \external_value(PARAM_TEXT, 'intro', VALUE_DEFAULT, null),
                'section' => new \external_value(PARAM_INT, 'section', VALUE_DEFAULT, null),
                'visible' => new \external_value(PARAM_BOOL, 'visible', VALUE_DEFAULT, null),
            ])
        ]);
    }

    /**
     * Performs assignment creation.
     *
     * @param array $data The incoming data parameter.
     * @return array An array of parameters, if successful.
     */
    public static function assignment_update($data) {
        global $DB;

        $params = self::validate_parameters(self::assignment_update_parameters(), ['data' => $data]);
        $params = $params['data'];

        list($course, $module, $assign) = \local_o365\webservices\utils::verify_assignment($params['coursemodule'],
            $params['course']);

        $context = \context_course::instance($params['course']);
        self::validate_context($context);

        // Update assignment information.
        $updatedassigninfo = [];
        if (isset($params['name']) && $params['name'] !== null) {
            $updatedassigninfo['name'] = $params['name'];
        }
        if (isset($params['intro']) && $params['intro'] !== null) {
            $updatedassigninfo['introeditor'] = ['text' => (string)$params['intro'], 'format' => FORMAT_HTML, 'itemid' => null];
        }
        if (!empty($updatedassigninfo)) {
            $assignkeys = [
                'name',
                'submissiondrafts',
                'sendnotifications',
                'sendlatenotifications',
                'duedate',
                'allowsubmissionsfromdate',
                'grade',
                'requiresubmissionstatement',
                'completionsubmit',
                'cutoffdate',
                'teamsubmission',
                'requireallteammemberssubmit',
                'blindmarking',
                'markingworkflow',
                'markingallocation',
            ];
            $assigninfo = [
                'coursemodule' => $module->id,
                'cmidnumber' => $module->idnumber,
                'introeditor' => ['text' => (string)$assign->intro, 'format' => FORMAT_HTML, 'itemid' => null],
                'assignsubmission_onenote_enabled' => 1,
                'assignsubmission_onenote_maxfiles' => 1,
                'assignsubmission_onenote_maxsizebytes' => 1024,
                'visible' => $module->visible,
                'visibleoncoursepage' => $module->visible,
                'gradingduedate' => 0,
            ];
            foreach ($assignkeys as $key) {
                $assigninfo[$key] = $assign->$key;
            }

            $assigninfo = array_merge($assigninfo, $updatedassigninfo);
            update_module((object)$assigninfo);
        }

        // Update module visibility if requested.
        if (isset($params['visible']) && $params['visible'] !== null) {
            set_coursemodule_visible($module->id, $params['visible']);
            $module = $DB->get_record('course_modules', ['id' => $module->id]);
        }

        // Move module section, if requested.
        if (isset($params['section']) && $params['section'] !== null) {
            // Validate section exists.
            $section = $DB->get_record('course_sections', ['course' => $course->id, 'id' => $params['section']]);
            if (empty($section)) {
                throw new exception\sectionnotfound();
            }
            moveto_module($module, $section);
        }

        $modinfo = \local_o365\webservices\utils::get_assignment_return_info($module->id, $course->id);
        return ['data' => [$modinfo]];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure Object describing return parameters for this webservice method.
     */
    public static function assignment_update_returns() {
        return \local_o365\webservices\utils::get_assignment_return_info_schema();
    }
}
