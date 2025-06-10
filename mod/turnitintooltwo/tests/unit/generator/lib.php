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

defined('MOODLE_INTERNAL') || die();
global $CFG;
global $DB;

require_once $CFG->dirroot.'/mod/turnitintooltwo/turnitintooltwo_assignment.class.php';
require_once $CFG->dirroot.'/mod/turnitintooltwo/turnitintooltwo_user.class.php';
include_once $CFG->dirroot.'/course/lib.php';
require_once $CFG->dirroot.'/webservice/tests/helpers.php';

/**
* Turnitintooltwo module data generator class
* Usage:
*   - Test class must extend this class.
*   - Create a test function and call one of these functions from within it using (for example):
*     <code>$this->make_test_users(5,"learner");</code>
*
* @category  Test
* @package  mod_turnitintooltwo
* @copyright  2017 Turnitin
* @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
abstract class test_lib extends advanced_testcase {
    /**
     * Create a test part on the specified assignment.
     *
     * @param string $modname Module name (turnitintool or turnitintooltwo)
     * @param int $assignmentid Assignment Module ID
     * @param int $number_of_parts - The number of parts to create
     * @param int $tiiassignid - Specify a Turnitin assignment ID - use when creating multiple assignments to differentiate them.
     *
     * @return array $parts_created - parts added to the assignment listed as partid => partobject
     */
    public function make_test_parts($modname, $assignmentid, $number_of_parts, $tiiassignid = null) {
        global $DB;

        $modulevar = $modname.'id';
        $part = new stdClass();
        $part->$modulevar = $assignmentid;
        $part->tiiassignid = is_null($tiiassignid) ? 0 : $tiiassignid;
        $part->dtstart = 0;
        $part->dtdue = 0;
        $part->dtpost = 0;
        $part->maxmarks = 0;
        $part->deleted = 0;
        $part->submitted = 0;
        
        $parts_created = array();
        for ($i=0; $i < $number_of_parts; $i++) {
            $part->partname = uniqid("Part - ", false);
            $partid = $DB->insert_record($modname.'_parts', $part);
            $part->id = $partid;
            $parts_created[$partid] = $part;
            $parts_created[$partid]->peermark_assignments = array();
        }

        return $parts_created;
    }
    
    /**
     * Make a test Turnitin assignment module for use in various test cases.
     * @param int $courseid - Moodle course ID
     * @param string $modname - Module name (turnitintool or turnitintooltwo)
     * @param int $assignmentid - Assignment id to which the coursemodule should be added
     * @param boolean $addtocm - for certain tests we may not want the module added to the course_modules table
     *
     * @return  int $cm - id of the course module added
     */
    public function make_test_module($courseid, $modname, $assignmentid, $addtocm = true) {
        global $DB;

        // Set up a course module.
        $module = $DB->get_record("modules", array("name" => $modname));
        $coursemodule = new stdClass();
        $coursemodule->course = $courseid;
        $coursemodule->module = $module->id;
        $coursemodule->added = time();
        $coursemodule->instance = $assignmentid;
        $coursemodule->section = 0;
        if ($addtocm) {
            // Add Course module and get course section.
            $coursemodule->coursemodule = add_course_module($coursemodule);

            if (is_callable('course_add_cm_to_section')) {
                $sectionid = course_add_cm_to_section($coursemodule->course, $coursemodule->coursemodule, $coursemodule->section);
            } else {
                $sectionid = add_mod_to_section($coursemodule);
            }

            $DB->set_field("course_modules", "section", $sectionid, array("id" => $coursemodule->coursemodule));

            rebuild_course_cache($coursemodule->coursemodule);

            return $coursemodule->coursemodule;
        }
        
        return 0;
    }

    /**
     * Creates a moodle user and a corresponding entry in the turnitintooltwo_users table
     * for the tii user specified
     * @param  object $turnitintooltwo_user - turnitintooltwo user object
     *
     * @return  int $turnitintooltwo_user_id id of turnitintool user join (for use in get_record queries on turnitintooltwo_users table)
     */
    public function join_test_user($turnitintooltwo_user) {
        global $DB;

        $mdl_user = $this->getDataGenerator()->create_user();
        $tiiUserRecord = new stdClass();
        $tiiUserRecord->userid = $mdl_user->id;
        $tiiUserRecord->turnitin_uid = $turnitintooltwo_user->id;
        $tiiUserRecord->user_agreement_accepted = 1;
        $turnitintooltwo_user_id = $DB->insert_record('turnitintooltwo_users', $tiiUserRecord);
        
        return $turnitintooltwo_user_id;
    }

    /**
     * Creates a number of test turnitintooltwo users, creates an equivalent moodle user for each, and handles the database association work.
     *
     * @param int $number_of_users
     * @param array $roles - an array of strings, each of which should be 'learner' or 'instructor'.
     * @return object $return - object of two arrays of equal length, one full of turnitintooltwo_user types and the other with ids for dbtable turnitintooltwo_users. The indices of these arrays DO align.
     */
    public function make_test_users($number_of_users, $roles) {
        $return['turnitintooltwo_users'] = array();
        $return['joins'] = array();

        for ($i=0; $i < $number_of_users; $i++) {
            $role = isset($roles[$i]) ? $roles[$i] : 'Instructor';
            $new_user = new turnitintooltwo_user( $i+1, $role, false, 'site', false );
            array_push($return['turnitintooltwo_users'], $new_user);
            $joinid = $this->join_test_user($new_user);
            array_push($return['joins'], $joinid);
        }

        return $return;
    }

    /**
     * Make a test turnitintooltwo assignment.
     * Also constructs a moodle course for use in assignment creation.
     *
     * @return turnitintooltwo $turnitintooltwoassignment - an instance of a turnitintooltwoassignment class.
     */
    public function make_test_tii_assignment() {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        $turnitintooltwo = new stdClass();
        $turnitintooltwo->course = $course->id;
        $turnitintooltwo->name = "test V2";
        $turnitintooltwo->dateformat = "d/m/Y";
        $turnitintooltwo->usegrademark = 0;
        $turnitintooltwo->gradedisplay = 0;
        $turnitintooltwo->autoupdates = 0;
        $turnitintooltwo->commentedittime = 0;
        $turnitintooltwo->commentmaxsize = 0;
        $turnitintooltwo->autosubmission = 0;
        $turnitintooltwo->shownonsubmission = 0;
        $turnitintooltwo->studentreports = 1;
        $turnitintooltwo->grade = 0;
        $turnitintooltwo->numparts = 1;
        $turnitintooltwo->anon = 0;
        $turnitintooltwo->allowlate = 0;
        $turnitintooltwo->legacy = 0;
        $turnitintooltwo->id = $DB->insert_record("turnitintooltwo", $turnitintooltwo);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($turnitintooltwo->id, $turnitintooltwo);
        
        return $turnitintooltwoassignment;
    }

    /**
     * enrols a moodle user onto a moodle course.
     *
     * @param int $moodle_user - the ID for the moodle user to be enrolled
     * @param int $course - the ID for the course on which to enrol $moodle_user
     * @param string $role - either "Instructor" or "Learner"
     * 
     * @return void
     */
    public function enrol_test_user($moodle_user, $course, $role) {
        global $DB;
        $roleid = $role == "Instructor";
        $enrol = enrol_get_plugin('manual');
        $instance = $DB->get_record("enrol", array('courseid' => $course, 'enrol' => 'manual'));
        $enrol->enrol_user($instance, $moodle_user, $roleid);
    }

    /**
     * Create a test submission.
     *
     * @param $turnitintooltwoassignment
     * @param $author
     * @param $partid
     */
    public function create_test_submission($turnitintooltwoassignment, $author, $partid) {
        $submission = new turnitintooltwo_submission(0, "moodle", $turnitintooltwoassignment, 1);

        $data = new stdClass();
        $data->userid = $author;
        $data->turnitintooltwoid = $turnitintooltwoassignment->turnitintooltwo->id;
        $data->submission_part = $partid;
        $data->submission_title = "Submission title";
        $data->submission_type = 1;
        $data->submission_objectid = null;
        $data->submission_unanon = 0;
        $data->submission_grade = null;
        $data->submission_gmimaged = 0;
        $data->submission_hash = $author.'_'.$turnitintooltwoassignment->turnitintooltwo->id.'_'.$partid;

        $submission->insert_submission($data);
    }
}
