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

use Integrations\PhpSdk\TiiClass;

/**
 * @package   plagiarism_turnitin
 * @copyright 2018 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_comms.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_user.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_submission.class.php');

class turnitin_assignment {

    private $id;
    private $turnitincomms;

    public function __construct($id = 0, $turnitincomms = null) {
        $this->id = $id;
        $this->turnitincomms = $turnitincomms;

        if (!$turnitincomms) {
            $this->turnitincomms = new turnitin_comms();
        }
    }

    /**
     * Find the course data, including Turnitin id
     *
     * @param int $courseid The ID of the course to get the data for
     * @param string $workflowcontext whether we are in a cron context (from PP) or using the site as normal.
     * @return object The course object with the Turnitin Class data if it's been created
     */
    public static function get_course_data($courseid, $workflowcontext = "site") {
        global $DB;

        if (!$course = $DB->get_record("course", array("id" => $courseid))) {
            if ($workflowcontext != "cron") {
                plagiarism_turnitin_print_error('coursegeterror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                exit;
            }
        }

        $course->turnitin_cid = 0;
        $course->turnitin_ctl = "";
        $course->tii_rel_id = '';
        if ($turnitincourse = $DB->get_record('plagiarism_turnitin_courses', array("courseid" => $courseid))) {
            $course->turnitin_cid = $turnitincourse->turnitin_cid;
            $course->turnitin_ctl = $turnitincourse->turnitin_ctl;
            $course->tii_rel_id = $turnitincourse->id;
        }

        return $course;
    }

    /**
     * Create the course in Turnitin
     *
     * @global type $DB
     * @param object $course The course object
     * @param string $workflowcontext The workflow being used to call this - site or cron.
     * @return object the turnitin course if created
     */
    public function create_tii_course($course, $workflowcontext = "site") {
        global $DB;

        $turnitincall = $this->turnitincomms->initialise_api();

        $class = new TiiClass();
        $tiititle = $this->truncate_title( $course->fullname, PLAGIARISM_TURNITIN_COURSE_TITLE_LIMIT );
        $class->setTitle( $tiititle );

        try {
            $response = $this->api_create_class($turnitincall, $class);
            $newclass = $this->api_get_class($response);

            $turnitincourse = new stdClass();
            $turnitincourse->courseid = $course->id;
            $turnitincourse->turnitin_cid = $this->api_get_class_id($newclass);
            $turnitincourse->turnitin_ctl = $course->fullname . " (Moodle PP)";

            if (empty($course->tii_rel_id)) {
                $method = "insert_record";
            } else {
                $method = "update_record";
                $turnitincourse->id = $course->tii_rel_id;
            }

            if (!$insertid = $DB->$method('plagiarism_turnitin_courses', $turnitincourse)) {
                plagiarism_turnitin_print_error('classupdateerror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                exit();
            }

            if (empty($turnitincourse->id)) {
                $turnitincourse->id = $insertid;
            }

            plagiarism_turnitin_activitylog("Class created - ".$turnitincourse->courseid." | ".$turnitincourse->turnitin_cid.
                " | ".$course->fullname . " (Moodle PP)" , "REQUEST");

            return $turnitincourse;
        } catch (Exception $e) {
            if ($workflowcontext == "cron") {
                mtrace(get_string('pp_classcreationerror', 'plagiarism_turnitin'));
            }
            $this->turnitincomms->handle_exceptions($e, 'pp_classcreationerror', false);
        }
    }

    /**
     * Edit the course title in Turnitin
     *
     * @global type $DB
     * @param var $course The course object
     */
    public function edit_tii_course($course) {
        global $DB;

        $turnitincall = $this->turnitincomms->initialise_api();

        $class = new TiiClass();
        $this->api_set_class_id($class, $course->turnitin_cid);

        $title = $this->truncate_title( $course->fullname, PLAGIARISM_TURNITIN_COURSE_TITLE_LIMIT );
        $class->setTitle( $title );

        // If a course end date is specified in Moodle then we set this in Turnitin with an additional month to
        // account for the Turnitin viewer becoming read-only once the class end date passes.
        if (!empty($course->enddate)) {
            $enddate = strtotime('+1 month', $course->enddate);
            $class->setEndDate(gmdate("Y-m-d\TH:i:s\Z", $enddate));
        }

        try {
            $this->api_update_class($turnitincall, $class);

            $turnitincourse = $DB->get_record("plagiarism_turnitin_courses", array("courseid" => $course->id));

            $update = new stdClass();
            $update->id = $turnitincourse->id;
            $update->courseid = $course->id;
            $update->turnitin_cid = $course->turnitin_cid;
            $update->turnitin_ctl = $course->fullname . " (Moodle PP)";

            if (!$insertid = $DB->update_record('plagiarism_turnitin_courses', $update)) {
                plagiarism_turnitin_print_error('classupdateerror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                exit();
            } else {
                plagiarism_turnitin_activitylog("Class edited - ".$update->turnitin_ctl." (".$update->id.")", "REQUEST");
            }
        } catch (Exception $e) {
            $this->turnitincomms->handle_exceptions($e, 'classupdateerror', false);
        }
    }

    /**
     * Truncate the course and assignment titles to match Turnitin requirements and add a suffix on the end.
     *
     * @param string $title The course id on Turnitin
     * @param int $limit The course title on Turnitin
     */
    public static function truncate_title($title, $limit) {
        $suffix = " (Moodle PP)";
        $limit = $limit - strlen($suffix);
        $truncatedtitle = "";

        if ( mb_strlen( $title, 'UTF-8' ) > $limit ) {
            $truncatedtitle .= mb_substr( $title, 0, $limit - 3, 'UTF-8' ) . "...";
        } else {
            $truncatedtitle .= $title;
        }
        $truncatedtitle .= $suffix;

        return $truncatedtitle;
    }

    /**
     * Create Assignment on Turnitin and return id, delete the instance if it fails
     *
     * @param object $assignment add assignment instance
     * @param string $workflowcontext The workflow being used to call this - site or cron.
     */
    public function create_tii_assignment($assignment, $workflowcontext = "site") {
        $turnitincall = $this->turnitincomms->initialise_api();

        try {
            $response = $this->api_create_assignment($turnitincall, $assignment);
            $newassignment = $this->api_get_assignment($response);
            $assignmentid = $this->api_get_assignment_id($newassignment);

            plagiarism_turnitin_activitylog("Assignment created as Turnitin Assignment (".$assignmentid.")", "REQUEST");

            return $assignmentid;
        } catch (Exception $e) {
            $toscreen = true;

            if ($workflowcontext == "cron") {
                mtrace(get_string('ppassignmentcreateerror', 'plagiarism_turnitin'));
                mtrace($e->getMessage());
                $toscreen = false;
            }
            $this->turnitincomms->handle_exceptions($e, 'createassignmenterror', $toscreen);
        }
    }

    /**
     * Edit Assignment on Turnitin
     *
     * @param object $assignment edit assignment instance
     * @param string $workflowcontext The workflow being used to call this - site or cron.
     */
    public function edit_tii_assignment($assignment, $workflowcontext = "site") {
        $turnitincall = $this->turnitincomms->initialise_api();
        $assignmentid = $this->api_get_assignment_id($assignment);

        try {
            $this->api_update_assignment($turnitincall, $assignment);

            $_SESSION["assignment_updated"][$assignmentid] = time();

            plagiarism_turnitin_activitylog("Turnitin Assignment updated - id: ".$assignmentid, "REQUEST");

            return array('success' => true, 'tiiassignmentid' => $assignmentid);

        } catch (Exception $e) {
            $toscreen = true;

            // Separate error handling for the Plagiarism plugin.
            if ($workflowcontext == "cron") {

                $error = new stdClass();
                $error->title = $this->api_get_title($assignment);
                $error->assignmentid = $assignmentid;
                $errorstr = get_string('ppassignmentediterror', 'plagiarism_turnitin', $error);

                mtrace($errorstr);
                $toscreen = false;
            }

            $this->turnitincomms->handle_exceptions($e, 'editassignmenterror', $toscreen);

            // Return error string as we use this in the plagiarism plugin.
            if ($workflowcontext == "cron") {
                return array('success' => false, 'error' => $errorstr, 'tiiassignmentid' => $assignmentid);
            } else {
                return array('success' => false, 'error' => get_string('editassignmenterror', 'plagiarism_turnitin'));
            }
        }
    }

    /**
     * Get the Peermark assignments for this activity.
     *
     * @global type $DB
     * @param $tiiassignid
     * @return array
     */
    public function get_peermark_assignments($tiiassignid) {
        global $DB;
        if ($peermarks = $DB->get_records("plagiarism_turnitin_peermark", array("parent_tii_assign_id" => $tiiassignid))) {
            return $peermarks;
        } else {
            return array();
        }
    }

    /**
     * Wrapper for Turnitin API call createClass().
     *
     * @param $turnitincall
     * @param $class
     * @return mixed
     */
    public function api_create_class($turnitincall, $class) {
        return $turnitincall->createClass($class);
    }

    /**
     * Wrapper for Turnitin API call createClass().
     *
     * @param $turnitincall
     * @param $class
     * @return mixed
     */
    public function api_update_class($turnitincall, $class) {
        return $turnitincall->updateClass($class);
    }

    /**
     * Wrapper for Turnitin API call getClass().
     *
     * @param $object
     * @return mixed
     */
    public function api_get_class($object) {
        return $object->getClass();
    }

    /**
     * Wrapper for Turnitin API call getClassId().
     *
     * @param $class
     * @return mixed
     */
    public function api_get_class_id($class) {
        return $class->getClassId();
    }

    /**
     * Wrapper for Turnitin API call setClassId().
     *
     * @param $object
     * @param $classid
     * @return mixed
     */
    public function api_set_class_id($object, $classid) {
        return $object->setClassId($classid);
    }

    /**
     * Wrapper for Turnitin API call createAssignment().
     *
     * @param $turnitincall
     * @param $assignment
     * @return mixed
     */
    public function api_create_assignment($turnitincall, $assignment) {
        return $turnitincall->createAssignment($assignment);
    }

    /**
     * Wrapper for Turnitin API call createAssignment().
     *
     * @param $turnitincall
     * @param $assignment
     * @return mixed
     */
    public function api_update_assignment($turnitincall, $assignment) {
        return $turnitincall->updateAssignment($assignment);
    }

    /**
     * Wrapper for Turnitin API call getAssignment().
     *
     * @param $object
     * @return mixed
     */
    public function api_get_assignment($object) {
        return $object->getAssignment();
    }

    /**
     * Wrapper for Turnitin API call getAssignmentId().
     *
     * @param $assignment
     * @return mixed
     */
    public function api_get_assignment_id($assignment) {
        return $assignment->getAssignmentId();
    }

    /**
     * Wrapper for Turnitin API call getTitle().
     *
     * @param $assignment
     * @return mixed
     */
    public function api_get_title($assignment) {
        return $assignment->getTitle();
    }
}