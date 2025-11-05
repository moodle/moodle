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
 * External functions for Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/blocks/wds_sportsgrades/classes/search.php');
require_once($CFG->dirroot . '/blocks/wds_sportsgrades/classes/grade_fetcher.php');

/**
 * External services for Sports Grades block
 */
class block_wds_sportsgrades_external extends external_api {
    
    /**
     * Returns description of search_students parameters
     * 
     * @return external_function_parameters
     */
    public static function search_students_parameters() {
        return new external_function_parameters(
            [
                'params' => new external_single_structure(
                    [
                        'universal_id' => new external_value(PARAM_TEXT, 'Universal ID', VALUE_OPTIONAL),
                        'username' => new external_value(PARAM_TEXT, 'Username', VALUE_OPTIONAL),
                        'firstname' => new external_value(PARAM_TEXT, 'First name', VALUE_OPTIONAL),
                        'lastname' => new external_value(PARAM_TEXT, 'Last name', VALUE_OPTIONAL),
                        'major' => new external_value(PARAM_TEXT, 'Major', VALUE_OPTIONAL),
                        'classification' => new external_value(PARAM_TEXT, 'Classification', VALUE_OPTIONAL),
                        'sport' => new external_value(PARAM_TEXT, 'Sport code', VALUE_OPTIONAL),
                    ]
                )
            ]
        );
    }
    
    /**
     * Search for students based on criteria
     * 
     * @param array $params Search parameters
     * @return array Search results
     */
    public static function search_students($params) {
        global $USER;
        
        $params = (object) $params;
        
        // Context validation
        $context = context_system::instance();
        self::validate_context($context);
        
        // Check access
        require_capability('block/wds_sportsgrades:view', $context);
        
        // Execute search
        $search = new \block_wds_sportsgrades\search();
        $results = $search->search_students($params);
        
        return $results;
    }
    
    /**
     * Returns description of search_students return value
     * 
     * @return external_single_structure
     */
    public static function search_students_returns() {
        return new external_single_structure(
            [
                'success' => new external_value(PARAM_BOOL, 'Whether the search was successful', VALUE_OPTIONAL),
                'error' => new external_value(PARAM_TEXT, 'Error message if search failed', VALUE_OPTIONAL),
                'results' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'User ID'),
                            'username' => new external_value(PARAM_TEXT, 'Username'),
                            'firstname' => new external_value(PARAM_TEXT, 'First name'),
                            'lastname' => new external_value(PARAM_TEXT, 'Last name'),
                            'email' => new external_value(PARAM_TEXT, 'Email address'),
                            'universal_id' => new external_value(PARAM_TEXT, 'Universal ID', VALUE_OPTIONAL),
                            'college' => new external_value(PARAM_TEXT, 'College', VALUE_OPTIONAL),
                            'major' => new external_value(PARAM_TEXT, 'Major', VALUE_OPTIONAL),
                            'classification' => new external_value(PARAM_TEXT, 'Classification', VALUE_OPTIONAL),
                            'sports' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'id' => new external_value(PARAM_INT, 'Sport ID'),
                                        'code' => new external_value(PARAM_TEXT, 'Sport code'),
                                        'name' => new external_value(PARAM_TEXT, 'Sport name'),
                                    ]
                                ),
                                'Sports the student is enrolled in',
                                VALUE_OPTIONAL
                            ),
                        ]
                    ),
                    'Search results',
                    VALUE_OPTIONAL
                ),
            ]
        );
    }
    
    /**
     * Returns description of get_student_grades parameters
     * 
     * @return external_function_parameters
     */
    public static function get_student_grades_parameters() {
        return new external_function_parameters(
            [
                'studentid' => new external_value(PARAM_INT, 'Student ID'),
            ]
        );
    }
    
    /**
     * Get grades for a student
     * 
     * @param int $studentid Student ID
     * @return array Grade data
     */
    public static function get_student_grades($studentid) {
        global $USER, $DB;
        
        // Context validation
        $context = context_system::instance();
        self::validate_context($context);
        
        // Check access
        require_capability('block/wds_sportsgrades:viewgrades', $context);
        
        // Parameter validation
        $params = ['studentid' => $studentid];
        $params = self::validate_parameters(self::get_student_grades_parameters(), $params);
        
        // Get student data
        $student = $DB->get_record('user', ['id' => $studentid], 'id, firstname, lastname');
        
        // Execute grade fetching
        $grade_fetcher = new \block_wds_sportsgrades\grade_fetcher();
        $grades = $grade_fetcher->get_course_grades($studentid);
        
        // Add student data to the response
        if (!isset($grades['error'])) {
            $grades['student'] = $student;
        }
        
        return $grades;
    }
    
    /**
     * Returns description of get_student_grades return value
     * 
     * @return external_single_structure
     */
    public static function get_student_grades_returns() {
        return new external_single_structure(
            [
                'error' => new external_value(PARAM_TEXT, 'Error message if retrieval failed', VALUE_OPTIONAL),
                'student' => new external_single_structure(
                    [
                        'id' => new external_value(PARAM_INT, 'Student ID'),
                        'firstname' => new external_value(PARAM_TEXT, 'First name'),
                        'lastname' => new external_value(PARAM_TEXT, 'Last name'),
                    ],
                    'Student information',
                    VALUE_OPTIONAL
                ),
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'Course ID'),
                            'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
                            'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
                            'section' => new external_value(PARAM_TEXT, 'Course section', VALUE_OPTIONAL),
                            'term' => new external_value(PARAM_TEXT, 'Academic term', VALUE_OPTIONAL),
                            'startdate' => new external_value(PARAM_INT, 'Course start date as timestamp'),
                            'final_grade' => new external_value(PARAM_FLOAT, 'Final grade', VALUE_OPTIONAL, null),
                            'final_grade_formatted' => new external_value(PARAM_TEXT, 'Formatted final grade'),
                            'letter_grade' => new external_value(PARAM_TEXT, 'Letter grade'),
                            'grade_items' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'id' => new external_value(PARAM_INT, 'Grade item ID'),
                                        'name' => new external_value(PARAM_TEXT, 'Grade item name'),
                                        'type' => new external_value(PARAM_TEXT, 'Grade item type'),
                                        'module' => new external_value(PARAM_TEXT, 'Module type', VALUE_OPTIONAL),
                                        'weight' => new external_value(PARAM_FLOAT, 'Weight percentage', VALUE_OPTIONAL, null),
                                        'weight_formatted' => new external_value(PARAM_TEXT, 'Formatted weight percentage'),
                                        'grade' => new external_value(PARAM_FLOAT, 'Grade value', VALUE_OPTIONAL, null),
                                        'grade_formatted' => new external_value(PARAM_TEXT, 'Formatted grade value'),
                                        'grademax' => new external_value(PARAM_FLOAT, 'Maximum grade value'),
                                        'percentage' => new external_value(PARAM_FLOAT, 'Grade as percentage', VALUE_OPTIONAL, null),
                                        'percentage_formatted' => new external_value(PARAM_TEXT, 'Formatted percentage'),
                                        'contribution' => new external_value(PARAM_FLOAT, 'Contribution to final grade', VALUE_OPTIONAL, null),
                                        'contribution_formatted' => new external_value(PARAM_TEXT, 'Formatted contribution to final grade'),
                                    ]
                                ),
                                'Grade items',
                                VALUE_OPTIONAL
                            ),
                        ]
                    ),
                    'Courses with grades',
                    VALUE_OPTIONAL
                ),
            ]
        );
    }
}
