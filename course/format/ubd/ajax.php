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
 * AJAX script for UbD format operations.
 *
 * @package    format_ubd
 * @copyright  2025 Moodle Evolved Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');

$courseid = required_param('courseid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('moodle/course:manageactivities', $context);
require_sesskey();

$response = array('success' => false, 'message' => '');

switch ($action) {
    case 'save_ubd_plan':
        try {
            // Validate input data
            $ubdData = array();
            $ubdFields = array(
                'ubd_stage1_enduring' => 'Stage 1: Enduring Understandings',
                'ubd_stage1_questions' => 'Stage 1: Essential Questions',
                'ubd_stage1_knowledge' => 'Stage 1: Knowledge & Skills',
                'ubd_stage2_performance' => 'Stage 2: Performance Tasks',
                'ubd_stage2_evidence' => 'Stage 2: Other Evidence',
                'ubd_stage3_activities' => 'Stage 3: Learning Activities'
            );

            $validationErrors = array();
            $totalLength = 0;

            foreach ($ubdFields as $field => $fieldName) {
                $value = optional_param($field, '', PARAM_RAW);

                // Clean and validate the input
                $value = trim($value);

                // Check individual field length (max 5000 characters per field)
                if (strlen($value) > 5000) {
                    $validationErrors[] = $fieldName . ' exceeds maximum length of 5000 characters';
                }

                $totalLength += strlen($value);
                $ubdData[$field] = $value;
            }

            // Check total content length (max 25000 characters total)
            if ($totalLength > 25000) {
                $validationErrors[] = 'Total content exceeds maximum length of 25000 characters';
            }

            // Return validation errors if any
            if (!empty($validationErrors)) {
                $response['message'] = 'Validation errors: ' . implode('; ', $validationErrors);
                break;
            }

            // Log the save attempt
            $logData = array(
                'courseid' => $courseid,
                'userid' => $USER->id,
                'action' => 'ubd_plan_save',
                'timestamp' => time(),
                'data_length' => $totalLength
            );

            // Update course format options
            $courseformat = course_get_format($course);
            $result = $courseformat->update_course_format_options($ubdData);

            if ($result) {
                // Log successful save
                error_log('UbD Plan saved successfully for course ' . $courseid . ' by user ' . $USER->id);

                $response['success'] = true;
                $response['message'] = get_string('changessaved');
                $response['saved_at'] = date('Y-m-d H:i:s');
                $response['data_length'] = $totalLength;

                // Trigger course updated event
                $event = \core\event\course_updated::create(array(
                    'objectid' => $course->id,
                    'context' => $context,
                    'other' => array('updatedfields' => array_keys($ubdFields))
                ));
                $event->trigger();

            } else {
                $response['message'] = 'Failed to save UbD plan data';
                error_log('Failed to save UbD Plan for course ' . $courseid . ' by user ' . $USER->id);
            }

        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log('UbD Plan save error for course ' . $courseid . ': ' . $e->getMessage());
        }
        break;

    case 'validate_ubd_data':
        try {
            // Validate data without saving
            $field = required_param('field', PARAM_ALPHA);
            $value = required_param('value', PARAM_RAW);

            $validation = array(
                'valid' => true,
                'length' => strlen($value),
                'warnings' => array()
            );

            if (strlen($value) > 5000) {
                $validation['valid'] = false;
                $validation['warnings'][] = 'Content exceeds maximum length of 5000 characters';
            }

            if (strlen($value) > 1000) {
                $validation['warnings'][] = 'Consider breaking this into smaller sections';
            }

            $response['success'] = true;
            $response['validation'] = $validation;

        } catch (Exception $e) {
            $response['message'] = 'Validation error: ' . $e->getMessage();
        }
        break;

    default:
        $response['message'] = 'Invalid action specified';
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
