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

namespace enrol_meta\external;

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use context_course;
use moodle_exception;

/**
 * Web service function relating to add enrol meta instances
 *
 * @package    enrol_meta
 * @copyright  2021 WKS KV Bildung
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_instances extends external_api {

    /**
     * Parameters for adding meta enrolment instances
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'instances' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'metacourseid' => new external_value(PARAM_INT, 'ID of the course where meta enrolment is added.'),
                        'courseid' => new external_value(PARAM_RAW, 'ID of the course where meta enrolment is linked to.'),
                        'creategroup' => new external_value(PARAM_BOOL,
                            'Creates group in meta course named after linked course and '
                            . 'puts all enrolled users in this group', VALUE_DEFAULT, false),
                    ]
                ), 'List of course meta enrolment instances to create.', VALUE_DEFAULT, []
            ),
        ]);
    }

    /**
     * Adding meta enrolment instances
     *
     * @param  array $instances
     * @return array
     */
    public static function execute(array $instances): array {
        global $DB;
        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), [
            'instances' => $instances,
        ]);

        if (!count($params['instances'])) {
            throw new invalid_parameter_exception(get_string('wsnoinstancesspecified', 'enrol_meta'));
        }

        $result = [];
        foreach ($params['instances'] as $instance) {
            // Ensure the metacourse exists.
            $metacourserecord = $DB->get_record('course', ['id' => $instance['metacourseid']], 'id,visible');
            if (!$metacourserecord) {
                throw new invalid_parameter_exception(get_string('wsinvalidmetacourse', 'enrol_meta', $instance['metacourseid']));
            }
            // Ensure the current user is allowed to access metacourse.
            $contextmeta = context_course::instance($instance['metacourseid'], IGNORE_MISSING);
            try {
                self::validate_context($contextmeta);
                require_all_capabilities(['moodle/course:enrolconfig', 'enrol/meta:config'], $contextmeta);
            } catch (moodle_exception $e) {
                throw new invalid_parameter_exception(get_string('wsinvalidmetacourse', 'enrol_meta', $instance['metacourseid']));
            }

            // Ensure the linked course exists.
            $courserecord = $DB->get_record('course', ['id' => $instance['courseid']], 'id,visible');
            if (!$courserecord) {
                throw new invalid_parameter_exception(get_string('wsinvalidcourse', 'enrol_meta', $instance['courseid']));
            }

            // Ensure the current user is allowed to access linked course.
            $context = context_course::instance($instance['courseid'], IGNORE_MISSING);
            try {
                self::validate_context($context);
                if (!$courserecord->visible) {
                    require_capability('moodle/course:viewhiddencourses', $context);
                }
                require_capability('enrol/meta:selectaslinked', $context);
            } catch (moodle_exception $e) {
                throw new invalid_parameter_exception(get_string('wsinvalidcourse', 'enrol_meta', $instance['courseid']));
            }

            // Check for existing meta course link.
            $enrolrecord = $DB->get_record('enrol',
                    ['enrol' => 'meta', 'courseid' => $instance['metacourseid'], 'customint1' => $instance['courseid']]);
            if ($enrolrecord) {
                // Link exists.
                $result[] = [
                    'metacourseid' => $instance['metacourseid'],
                    'courseid' => $instance['courseid'],
                    'status' => false,
                ];
                continue;
            }

            // Check for permission to create group.
            if ($instance['creategroup']) {
                try {
                    require_capability('moodle/course:managegroups', $context);
                } catch (moodle_exception $e) {
                    throw new invalid_parameter_exception(get_string('wscannotcreategroup', 'enrol_meta', $instance['courseid']));
                }
            }

            // Create instance.
            $enrolplugin = enrol_get_plugin('meta');
            $fields = [
                'customint1' => $instance['courseid'],
                'customint2' => $instance['creategroup'] ? ENROL_META_CREATE_GROUP : 0,
            ];
            $addresult = $enrolplugin->add_instance($metacourserecord, $fields);
            $result[] = [
                'metacourseid' => $instance['metacourseid'],
                'courseid' => $instance['courseid'],
                'status' => (bool) $addresult,
            ];
        }

        return $result;
    }

    /**
     * Return for adding enrolment instances.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'metacourseid' => new external_value(PARAM_INT, 'ID of the course where meta enrolment is added.'),
                    'courseid' => new external_value(PARAM_RAW, 'ID of the course where meta enrolment is linked to.'),
                    'status' => new external_value(PARAM_BOOL, 'True on success, false if link already exists.'),
                ]
            ), 'List of course meta enrolment instances that were created.', VALUE_DEFAULT, []
        );
    }
}
