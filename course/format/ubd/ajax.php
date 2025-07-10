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
            // Get UbD data from POST
            $ubdData = array();
            $ubdFields = array(
                'ubd_stage1_enduring',
                'ubd_stage1_questions', 
                'ubd_stage1_knowledge',
                'ubd_stage2_performance',
                'ubd_stage2_evidence',
                'ubd_stage3_activities'
            );
            
            foreach ($ubdFields as $field) {
                $ubdData[$field] = optional_param($field, '', PARAM_RAW);
            }
            
            // Update course format options
            $courseformat = course_get_format($course);
            $courseformat->update_course_format_options($ubdData);
            
            $response['success'] = true;
            $response['message'] = get_string('changessaved');
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        break;
        
    default:
        $response['message'] = 'Invalid action';
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
