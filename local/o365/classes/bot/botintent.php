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
 * Class botintent - general class for accessing specific intent based on params.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot;

defined('MOODLE_INTERNAL') || die();

/**
 * Class botintent - general class for accessing specific intent based on params
 *
 * @var string $intentclass - Specific intent class name
 * @var string $userlanguage - Current user language set in Moodle
 * @var mixed $entities - Intent entities if intent needs them (optional)
 * @var array $availableintents - Implemented intents name => class name list
 */
class botintent {
    /**
     * @var string|null intent class.
     */
    private $intentclass;

    /**
     * @var user language.
     */
    private $userlanguage;

    /**
     * @var mixed|null entities.
     */
    private $entities;

    /**
     * @var string[] available intents.
     */
    private $availableintents = [
            'student-assignment-comparison-results' => ["classname" => "assignmentcomparison", "text" => "question_student_assignments_compared",
                "clickable" => true, "permission" => "accessbotstudentdata"],
            'student-due-assignments' => ["classname" => "dueassignments", "text" => "question_student_assignments_due",
                "clickable" => true, "permission" => "accessbotstudentdata"],
            'student-latest-grades' => ["classname" => "latestgrades", "text" => "question_student_latest_grades",
                "clickable" => true, "permission" => "accessbotstudentdata"],
            'teacher-assignments-for-grading' => ["classname" => "assignmentsforgrading", "text" => "question_teacher_assignments_for_grading",
                "clickable" => true, "permission" => "accessbotteacherdata"],
            'teacher-absent-students' => ["classname" => "absentstudents", "text" => "question_teacher_absent_students",
                "clickable" => true, "permission" => "accessbotteacherdata"],
            'teacher-incomplete-assignments' => ["classname" => "incompleteassignments", "text" => "question_teacher_absent_students",
                "clickable" => true, "permission" => "accessbotteacherdata"],
            'teacher-last-student-login' => ["classname" => "laststudentlogin", "text" => "question_teacher_student_last_logged",
                "clickable" => false, "permission" => "accessbotteacherdata"],
            'teacher-late-submissions' => ["classname" => "latesubmissions", "text" => "question_teacher_late_submissions",
                "clickable" => true, "permission" => "accessbotteacherdata"],
            'teacher-recent-students' => ["classname" => "recentstudents", "text" => "question_teacher_last_logged_students",
                "clickable" => true, "permission" => "accessbotteacherdata"],
            'teacher-latest-students' => ["classname" => "lateststudents", "text" => "question_teacher_latest_logged_students",
                "clickable" => true, "permission" => "accessbotteacherdata"],
            'teacher-worst-students-last-assignment' => ["classname" => "worststudentslastassignments", "text" => "question_teacher_least_scored_in_assignment",
                "clickable" => true, "permission" => "accessbotteacherdata"],
            'get-help' => ["classname" => "help"],
    ];

    /**
     * Botintent constructor to set object properties
     * @param array $params - webservice call params containing intent name and its entities (optional)
     */
    public function __construct($params) {
        global $USER;
        $contextsystem = \context_system::instance();
        $this->intentclass = null;
        $this->userlanguage = $USER->lang;
        if (!empty($params) && is_array($params)) {
            $this->entities = (empty($params['entities']) ? null : json_decode($params['entities']));
            $intent = (empty($params['intent']) ? null : $params['intent']);
            if (!is_null($intent) && !empty($this->availableintents[$intent]) && (empty($this->availableintents[$intent]['permission'])
                    || self::check_permission($this->availableintents[$intent]['permission']))) {
                    $this->intentclass = "\\local_o365\\bot\\intents\\{$this->availableintents[$intent]['classname']}";
                    if (!class_exists($this->intentclass)) {
                        $this->intentclass = null;
                    } else if ($intent == 'get-help') {
                        $this->entities = $this->availableintents;
                    }
            }
        }
    }

    /**
     * General get_message function to access specific intent get_message function
     * @return array - The answer message with all required details for bot
     */
    public function get_message() {
        if (!is_null($this->intentclass)) {
            $message = $this->intentclass::get_message($this->userlanguage, $this->entities);
            $message['language'] = $this->userlanguage;
            return $message;
        } else {
            return array(
                    'message' => get_string('sorry_do_not_understand', 'local_o365'),
                    'listTitle' => '',
                    'listItems' => [],
                    'warnings' => [],
                    'language' => $this->userlanguage
            );
        }
    }

    /**
     * @var array to cache user permission status
     */
    public static function check_permission($permission) {
        global $USER;
        static $checkedpermissions = [];
        if (!isset($checkedpermissions[$permission])) {
            $checkedpermissions[$permission] = false;
            $roles = get_roles_with_capability('local/o365:'.$permission);
            foreach ($roles as $role) {
                if (user_has_role_assignment($USER->id, $role->id)) {
                    $checkedpermissions[$permission] = true;
                    break;
                }
            }
        }
        return $checkedpermissions[$permission];
    }
}
