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
 * Trigger view event for file resource modules.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../../../lib/externallib.php');
require_once(__DIR__.'/../../../../../mod/resource/lib.php');

/**
 * Trigger view event for file resource modules.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_file_view extends loggable_external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'id' => new \external_value(PARAM_ALPHANUM, 'File path name SHA1 hash'),
            'userid' => new \external_value(PARAM_INT, 'User ID of the person viewing the file')
        ]);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Success status of viewing module associated to file')
        ]);
    }

    /**
     * @param string $id The file path name hash
     * @param int $userid
     * @return array
     * @throws \WebserviceInvalidParameterException
     * @throws \WebserviceParameterException
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public static function execute_service($id, $userid) {
        global $DB;

        $params = self::validate_parameters(self::service_parameters(), ['id' => $id, 'userid' => $userid]);

        $file = get_file_storage()->get_file_by_hash($params['id']);
        if (!$file instanceof \stored_file) {
            throw new \moodle_exception('filenotfound', 'error');
        }

        // The only module we want to mark as complete on file view is the file resource.
        if ($file->get_component() != 'mod_resource') {
            return ['success' => true];
        }

        $context = \context::instance_by_id($file->get_contextid());
        self::validate_context($context);

        $coursecontext = $context->get_course_context();
        $course = get_course($coursecontext->instanceid);

        // Make sure web service user has access.
        require_capability('moodle/course:view', $context);
        if ($course->visible === 0) {
            require_capability('moodle/course:viewhiddencourses', $context);
        }
        require_capability('mod/resource:view', $context);

        // Make sure user viewing resource has access.
        if ($course->visible === 0) {
            require_capability('moodle/course:viewhiddencourses', $context, $userid);
        }

        $modinfo = get_fast_modinfo($course, $userid);
        $cmid = $context->instanceid;
        $cm = $modinfo->get_cm($cmid);

        if (!$cm->uservisible) {
            throw new \coding_exception(
                'The user with id '.$userid.' is currently not allowed to view the requested resource'
            );
        }

        $resource = $DB->get_record('resource', array('id' => $cm->instance), '*', MUST_EXIST);
        resource_view($resource, $course, $cm, $context);

        return ['success' => true];
    }
}
