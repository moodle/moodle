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
 * @package   turnitintooltwo
 * @copyright 2012 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__."/lib.php");
require_once(__DIR__.'/turnitintooltwo_comms.class.php');
require_once(__DIR__.'/turnitintooltwo_user.class.php');
require_once(__DIR__.'/turnitintooltwo_submission.class.php');

class turnitintooltwo_assignment {

    private $timecreated;
    private $id;
    private $type;
    public $turnitintooltwo;

    public function __construct($id = 0, $turnitintooltwo = '', $type = 'TT') {
        global $DB;
        $this->id = $id;
        $this->type = $type;

        if (($type == 'TT') || ($type == 'V1')) {
            if (!empty($turnitintooltwo)) {
                $this->turnitintooltwo = $turnitintooltwo;
            } else {
                $this->turnitintooltwo = $DB->get_record("turnitintooltwo", array("id" => $id));

                // Get part variables if data not passed in (used in restore).
                $parts = $this->get_parts();
                $i = 0;
                foreach ($parts as $part) {
                    $i++;

                    $attribute = "maxmarks".$i;
                    $this->turnitintooltwo->$attribute = $part->maxmarks;

                    $attribute = "dtstart".$i;
                    $this->turnitintooltwo->$attribute = $part->dtstart;

                    $attribute = "dtdue".$i;
                    $this->turnitintooltwo->$attribute = $part->dtdue;

                    $attribute = "dtpost".$i;
                    $this->turnitintooltwo->$attribute = $part->dtpost;

                    $attribute = "partname".$i;
                    $this->turnitintooltwo->$attribute = $part->partname;
                }
            }
        }
    }

    /**
     * Get all the assignments in the course
     *
     * @param object $course
     * @return array instances of each assignment
     */
    public static function get_all_assignments_in_course($course) {
        if (!$turnitintooltwos = get_all_instances_in_course("turnitintooltwo", $course)) {
            turnitintooltwo_print_error('noturnitinassignemnts', 'turnitintooltwo', null, null, __FILE__, __LINE__);
            exit;
        }
        return $turnitintooltwos;
    }

    /**
     * Add a Moodle Tutor to the class in Turnitin
     *
     * @param int $tutorid
     * @return string $notice to display to user whether mehtod has been successful or failed
     */
    public function add_tii_tutor($tutorid) {
        // Get Course data.
        $coursetype = turnitintooltwo_get_course_type($this->turnitintooltwo->legacy);
        $course = $this->get_course_data($this->turnitintooltwo->course, $coursetype);
        $user = new turnitintooltwo_user($tutorid, 'Instructor');
        $notice = array();

        if ($user->join_user_to_class($course->turnitin_cid)) {
            $notice = get_string('tutoradded', 'turnitintooltwo');
        } else {
            $notice = get_string('tutoraddingerror', 'turnitintooltwo');
        }
        return $notice;
    }

    /**
     * Remove a user from the specified role in a class in Turnitin
     *
     * @param int $membershipid id that links user to the class
     * @param string $role the user has in the class
     * @return string to display to user whether method has been successful or failed
     */
    public function remove_tii_user_by_role($membershipid, $role = "Learner") {
        if (turnitintooltwo_user::remove_user_from_class($membershipid)) {
            $notice = ($role == "Learner") ? 'studentremoved' : 'tutorremoved';
        } else {
            $notice = ($role == "Learner") ? 'studentremovingerror' : 'tutorremovingerror';
        }
        return get_string($notice, 'turnitintooltwo');
    }

    /**
     * Returns the Turnitin owner of the current class
     *
     * @global object
     * @param int $courseid The ID of the course to check the owner of
     * @param string $coursetype whether the course is TT (Turnitintool) or PP (Plagiarism Plugin)
     * @return object The user object for the Turnitin Class Owner or null if there is no owner stored
     */
    private function get_tii_owner($courseid, $coursetype = "TT") {
        global $DB;
        if ($course = $DB->get_record('turnitintooltwo_courses', array("courseid" => $courseid, "course_type" => $coursetype))) {
            return $course->ownerid;
        } else {
            return null;
        }
    }

    /**
     * Find the course data, including Turnitin id
     *
     * @param int $courseid The ID of the course to get the data for
     * @param string $coursetype whether the course is TT (Turnitintool) or PP (Plagiarism Plugin)
     * @param string $workflowcontext whether we are in a cron context (from PP) or using the site as normal.
     * @return object The course object with the Turnitin Class data if it's been created
     */
    public static function get_course_data($courseid, $coursetype = "TT", $workflowcontext = "site") {
        global $DB;

        if (!$course = $DB->get_record("course", array("id" => $courseid))) {
            if ($workflowcontext != "cron") {
                turnitintooltwo_print_error('coursegeterror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
                exit;
            }
        }

        $course->turnitin_cid = 0;
        $course->turnitin_ctl = "";
        $course->course_type = $coursetype;
        $course->tii_rel_id = '';
        if ($turnitincourse = $DB->get_record('turnitintooltwo_courses',
                                array("courseid" => $courseid, "course_type" => $coursetype))) {
            $course->turnitin_cid = $turnitincourse->turnitin_cid;
            $course->turnitin_ctl = $turnitincourse->turnitin_ctl;
            $course->course_type = $turnitincourse->course_type;
            $course->ownerid = $turnitincourse->ownerid;
            $course->tii_rel_id = $turnitincourse->id;
        }

        return $course;
    }

    /**
     * Create a migrated turnitin assignment in Moodle
     *
     * @global type $DB
     * @global type $CFG
     * @param array $partids the ids of turnitin assignment to create as parts of new assignment
     * @param int $courseid
     * @param string $assignmentname
     * @return boolean false if failed
     */
    public static function create_migration_assignment($partids, $courseid, $assignmentname) {
        global $DB, $CFG, $OUTPUT;
        $config = turnitintooltwo_admin_config();

        $partids = (array)$partids;
        $tempassignment = new turnitintooltwo_assignment(0, '', 'M');
        $newassignment = $tempassignment->update_assignment_from_tii($partids);
        $newassignment["turnitintooltwo"]->course = $courseid;
        $newassignment["turnitintooltwo"]->name = $assignmentname;
        $newassignment["turnitintooltwo"]->numparts = count($partids);
        $newassignment["turnitintooltwo"]->gradedisplay = $config->default_gradedisplay;
        $newassignment["turnitintooltwo"]->shownonsubmission = 1;
        $newassignment["turnitintooltwo"]->usegrademark = $config->usegrademark;
        // Get maximum grade.
        $newassignment["turnitintooltwo"]->grade = 0;
        foreach ($newassignment["parts"] as $part) {
            if ($newassignment["turnitintooltwo"]->grade < $part->maxmarks) {
                $newassignment["turnitintooltwo"]->grade = $part->maxmarks;
            }
        }

        $turnitintooltwoassignment = new turnitintooltwo_assignment(0, $newassignment["turnitintooltwo"]);
        if (!$toolid = $DB->insert_record("turnitintooltwo", $turnitintooltwoassignment->turnitintooltwo)) {
            turnitintooltwo_activitylog(get_string('migrationassignmentcreationerror', 'turnitintooltwo', $courseid), "REQUEST");
            return false;
        } else {
            turnitintooltwo_activitylog(get_string('migrationassignmentcreated', 'turnitintooltwo', $toolid), "REQUEST");
        }

        $module = $DB->get_record("modules", array("name" => "turnitintooltwo"));
        $coursemodule = new stdClass();
        $coursemodule->course = $courseid;
        $coursemodule->module = $module->id;
        $coursemodule->added = time();
        $coursemodule->instance = $toolid;
        $coursemodule->section = 0;

        include_once($CFG->dirroot."/course/lib.php");

        // Add Course module and get course section.
        if (! $coursemodule->coursemodule = add_course_module($coursemodule) ) {
            echo $OUTPUT->notification(get_string('migrationassignmenterror1', 'turnitintooltwo', $courseid));
            turnitintooltwo_activitylog(get_string('migrationassignmenterror1', 'turnitintooltwo', $courseid), "REQUEST");
            return false;
        }

        if (is_callable('course_add_cm_to_section')) {
            if (!$sectionid = course_add_cm_to_section($coursemodule->course,
                                                $coursemodule->coursemodule, $coursemodule->section)) {
                echo $OUTPUT->notification(get_string('migrationassignmenterror2', 'turnitintooltwo', $courseid));
                turnitintooltwo_activitylog(get_string('migrationassignmenterror2', 'turnitintooltwo', $courseid), "REQUEST");
                return false;
            }
        } else {
            if (!$sectionid = add_mod_to_section($coursemodule)) {
                echo $OUTPUT->notification(get_string('migrationassignmenterror2', 'turnitintooltwo', $courseid));
                turnitintooltwo_activitylog(get_string('migrationassignmenterror2', 'turnitintooltwo', $courseid), "REQUEST");
                return false;
            }
        }
        $DB->set_field("course_modules", "section", $sectionid, array("id" => $coursemodule->coursemodule));
        rebuild_course_cache($courseid);

        foreach ($newassignment["parts"] as $part) {
            $part->turnitintooltwoid = $toolid;
            $part->deleted = 0;
            $part->migrated = -1;
            if ($part->id = $DB->insert_record("turnitintooltwo_parts", $part)) {
                turnitintooltwo_activitylog(get_string('migrationassignmentpartcreated', 'turnitintooltwo', $part->id), "REQUEST");
            }
            if ($turnitintooltwoassignment->create_event($toolid, $part->partname, $part->dtdue)) {
                $part->migrated = 1;
                $DB->update_record("turnitintooltwo_parts", $part);
            } else {
                echo $OUTPUT->notification(get_string('migrationassignmenterror3', 'turnitintooltwo', $courseid));
                turnitintooltwo_activitylog(get_string('migrationassignmenterror3', 'turnitintooltwo', $courseid), "REQUEST");
            }
        }
    }

    /**
     * Create a course in Moodle (Migration)
     *
     * @global type $DB
     * @global type $CFG
     * @global type $USER
     * @param int $tiicourseid The course id on Turnitin
     * @param string $tiicoursetitle The course title on Turnitin
     * @param string $coursename The new course name on Moodle
     * @param int $coursecategory The category that the course is to be created in
     * @return mixed course object if created or 0 if failed
     */
    public static function create_moodle_course($tiicourseid, $tiicoursetitle, $coursename, $coursecategory) {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot."/course/lib.php");

        $data = new stdClass();
        $data->category = $coursecategory;
        $data->fullname = $coursename;
        $data->shortname = "Turnitin (".$tiicourseid.")";
        $data->maxbytes = 2097152;

        if ($course = create_course($data)) {
            $turnitincourse = new stdClass();
            $turnitincourse->courseid = $course->id;
            $turnitincourse->turnitin_cid = $tiicourseid;
            $turnitincourse->turnitin_ctl = $tiicoursetitle;
            $turnitincourse->ownerid = $USER->id;
            $turnitincourse->course_type = 'TT';

            // Enrol user as instructor on course in moodle if they are not a site admin.
            if (!is_siteadmin()) {
                // Get the role id for a teacher.
                $roles1 = get_roles_with_capability('mod/turnitintooltwo:grade');
                $roles2 = get_roles_with_capability('mod/turnitintooltwo:addinstance');
                $roles = array_intersect_key($roles1, $roles2);
                $role = current($roles);

                // Enrol $USER in the courses using the manual plugin.
                $enrol = enrol_get_plugin('manual');

                $enrolinstances = enrol_get_instances($course->id, true);
                foreach ($enrolinstances as $courseenrolinstance) {
                    if ($courseenrolinstance->enrol == "manual") {
                        $instance = $courseenrolinstance;
                        break;
                    }
                }
                $enrol->enrol_user($instance, $USER->id, $role->id);
            } else {
                // Enrol admin as an instructor incase they are not on the account.
                $turnitintooltwouser = new turnitintooltwo_user($USER->id, "Instructor");
                $turnitintooltwouser->join_user_to_class($tiicourseid);
            }

            if (!$insertid = $DB->insert_record('turnitintooltwo_courses', $turnitincourse)) {
                turnitintooltwo_activitylog(get_string('migrationcoursecreatederror', 'turnitintooltwo',
                                            $tiicourseid).' - '.$course->id, "REQUEST");
                return 0;
            } else {
                turnitintooltwo_activitylog(get_string('migrationcoursecreated', 'turnitintooltwo').' - '.
                                            $course->id.' ('.$tiicourseid.')', "REQUEST");
                return $course;
            }
        } else {
            turnitintooltwo_activitylog(get_string('migrationcoursecreateerror', 'turnitintooltwo', $tiicourseid), "REQUEST");
            return 0;
        }
    }

    /**
     * Create the course in Turnitin
     *
     * @global type $DB
     * @param object $course The course object
     * @param int $ownerid The owner of the course
     * @return object the turnitin course if created
     */
    public function create_tii_course($course, $ownerid) {
        global $DB;

        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $class = new TiiClass();
        $tiititle = $this->truncate_title( $course->fullname, TURNITIN_COURSE_TITLE_LIMIT );
        $class->setTitle( $tiititle );

        try {
            $response = $turnitincall->createClass($class);
            $newclass = $response->getClass();

            $turnitincourse = new stdClass();
            $turnitincourse->courseid = $course->id;
            $turnitincourse->ownerid = $ownerid;
            $turnitincourse->turnitin_cid = $newclass->getClassId();
            $turnitincourse->turnitin_ctl = $course->fullname . " (Moodle TT)";
            $turnitincourse->course_type = "TT";

            if (empty($course->tii_rel_id)) {
                $method = "insert_record";
            } else {
                $method = "update_record";
                $turnitincourse->id = $course->tii_rel_id;
            }

            if (!$insertid = $DB->$method('turnitintooltwo_courses', $turnitincourse)) {
                turnitintooltwo_print_error('classupdateerror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
                exit();
            }

            if (empty($turnitincourse->id)) {
                $turnitincourse->id = $insertid;
            }

            $coursetype = "TT";
            $workflowcontext = "site";
            turnitintooltwo_activitylog("Class created - ".$turnitincourse->courseid." | ".$turnitincourse->turnitin_cid.
                                        " | ".$course->fullname . " (Moodle TT)" , "REQUEST");

            return $turnitincourse;
        } catch (Exception $e) {
            $toscreen = true;
            if ($workflowcontext == "cron") {
                mtrace(get_string('pp_classcreationerror', 'turnitintooltwo'));
                $toscreen = false;
            }
            $turnitincomms->handle_exceptions($e, 'classcreationerror', $toscreen);
        }
    }

    /**
     * Edit the course title in Turnitin
     *
     * @global type $DB
     * @param var $course The course object
     * @param string $coursetype whether the course is TT (Turnitintool) or PP (Plagiarism Plugin)
     */
    public function edit_tii_course($course, $coursetype = "TT") {
        global $DB;

        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $class = new TiiClass();
        $class->setClassId($course->turnitin_cid);
        $title = $this->truncate_title( $course->fullname, TURNITIN_COURSE_TITLE_LIMIT, $coursetype );
        $class->setTitle( $title );
        // If a course end date is specified in Moodle then we set this in Turnitin with an additional month to
        // account for the Turnitin viewer becoming read-only once the class end date passes.
        if (!empty($course->enddate)) {
            // The course end date must not be before the start date.
            // Change the course end date if it is set earlier than today.
            if ($course->enddate < strtotime('today')) {
                $course->enddate = strtotime('today');
            }
            $enddate = strtotime('+1 month', $course->enddate);
            $class->setEndDate(gmdate("Y-m-d\TH:i:s\Z", $enddate));
        }

        try {
            $turnitincall->updateClass($class);

            $turnitincourse = new stdClass();

            $turnitintooltwocourse = $DB->get_record("turnitintooltwo_courses",
                                array("courseid" => $course->id, "course_type" => $coursetype));
            $turnitincourse->id = $turnitintooltwocourse->id;
            $turnitincourse->courseid = $course->id;
            $turnitincourse->ownerid = $turnitintooltwocourse->ownerid;
            $turnitincourse->turnitin_cid = $course->turnitin_cid;
            $turnitincourse->turnitin_ctl = $course->fullname . " (Moodle ".$coursetype.")";
            $turnitincourse->course_type = $coursetype;

            if (!$insertid = $DB->update_record('turnitintooltwo_courses', $turnitincourse) && $coursetype != "PP") {
                turnitintooltwo_print_error('classupdateerror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
                exit();
            } else {
                turnitintooltwo_activitylog("Class edited - ".$turnitincourse->turnitin_ctl.
                                                " (".$turnitincourse->id.")", "REQUEST");
            }
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'classupdateerror');
        }
    }

    /**
     * Truncate the course and assignment titles to match Turnitin requirements and add a coursetype suffix on the end.
     *
     * @param string $title The course id on Turnitin
     * @param int $limit The course title on Turnitin
     * @param string $coursetype whether the course is TT (Turnitintooltwo) or PP (Plagiarism Plugin)
     */
    public static function truncate_title($title, $limit, $coursetype = 'TT') {
        $suffix = " (Moodle " . $coursetype . ")";
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
     * Edit the course end date in Turnitin
     *
     * @global type $DB
     * @param int $tiicourseid The course id on Turnitin
     * @param int $tiicoursetitle The course title on Turnitin
     * @param date $courseenddate The new course end date to be set on Turnitin
     */
    public static function edit_tii_course_end_date($tiicourseid, $tiicoursetitle, $courseenddate) {
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $class = new TiiClass();
        $class->setTitle($tiicoursetitle);
        $class->setClassId($tiicourseid);
        $class->setEndDate(gmdate("Y-m-d\TH:i:s\Z", $courseenddate));

        try {
            $turnitincall->updateClass($class);

            return true;
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'classupdateerror');
        }
    }

    /**
     * Get the users from Turnitin for the specified role, gets the memberships
     * and loops through them to get each user
     *
     * @param string $role the user has in the class
     * @param string $idkey whether to use moodle or turnitin ids as the array key
     * @return array $users
     */
    public function get_tii_users_by_role($role = "Learner", $idkey = "tii") {
        global $DB;

        $users = array();
        // Get Moodle Course Object.
        $coursetype = turnitintooltwo_get_course_type($this->turnitintooltwo->legacy);
        $course = $this->get_course_data($this->turnitintooltwo->course, $coursetype);
        $classmembers = $this->get_class_memberships($course->turnitin_cid);

        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $membership = new TiiMembership();
        $membership->setMembershipIds($classmembers);

        // Get Enrolled users from Turnitin.
        try {
            $response = $turnitincall->readMemberships($membership);
            $readmemberships = $response->getMemberships();
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'membercheckerror');
        }

        // Add each user to an array.
        foreach ($readmemberships as $readmembership) {
            if ($readmembership->getRole() == $role) {

                // Check user is a Moodle user. Otherwise we have to go to Turnitin for their name.
                $moodleuserid = turnitintooltwo_user::get_moodle_user_id($readmembership->getUserId());
                if (!empty($moodleuserid)) {
                    $user = $DB->get_record('user', array('id' => $moodleuserid));
                    $arraykey = ($idkey == "mdl") ? $user->id : $readmembership->getUserId();
                    $users[$arraykey]["firstname"] = $user->firstname;
                    $users[$arraykey]["lastname"] = $user->lastname;
                    $users[$arraykey]["membership_id"] = $readmembership->getMembershipId();
                } else {
                    $user = new TiiUser();
                    $user->setUserId($readmembership->getUserId());

                    try {
                        $response = $turnitincall->readUser($user);
                        $readuser = $response->getUser();

                        $users[$readmembership->getUserId()]["firstname"] = $readuser->getFirstName();
                        $users[$readmembership->getUserId()]["lastname"] = $readuser->getLastName();
                        $users[$readmembership->getUserId()]["membership_id"] = $readmembership->getMembershipId();

                    } catch (Exception $e) {
                        // A read user exception occurs when users are inactive. Re-enrol user to make them active.
                        $membership = new TiiMembership();
                        $membership->setClassId($course->turnitin_cid);
                        $membership->setUserId($readmembership->getUserId());
                        $membership->setRole($role);

                        try {
                            $turnitincall->createMembership($membership);
                        } catch ( InnerException $e ) {
                            $turnitincomms->handle_exceptions($e, 'userjoinerror');
                        }

                        try {
                            $user = new TiiUser();
                            $user->setUserId($readmembership->getUserId());

                            $response = $turnitincall->readUser($user);
                            $readuser = $response->getUser();

                            $users[$readmembership->getUserId()]["firstname"] = $readuser->getFirstName();
                            $users[$readmembership->getUserId()]["lastname"] = $readuser->getLastName();
                            $users[$readmembership->getUserId()]["membership_id"] = $readmembership->getMembershipId();
                        } catch ( InnerException $e ) {
                            $turnitincomms->handle_exceptions($e, 'tiiusergeterror');
                        }
                    }
                }
            }
        }

        return $users;
    }

    /**
     * Enrol All students on a course, checks to see if all the Moodle students
     * are already enrolled and then enrols the students who aren't
     *
     * @param object $cm the course module
     * @return boolean
     */
    public function enrol_all_students($cm) {
        // Get Moodle Course Object.
        $coursetype = turnitintooltwo_get_course_type($this->turnitintooltwo->legacy);
        $course = $this->get_course_data($this->turnitintooltwo->course, $coursetype);
        $context = context_module::instance($cm->id);

        // Get local course members.
        $students = get_enrolled_users($context,
                                'mod/turnitintooltwo:submit', groups_get_activity_group($cm), 'u.id');

        // Get the user ids of who is already enrolled and remove them from the students array.
        $tiiclassmemberships = $this->get_class_memberships($course->turnitin_cid);
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $membership = new TiiMembership();
        $membership->setMembershipIds($tiiclassmemberships);

        try {
            $response = $turnitincall->readMemberships($membership);
            $readmemberships = $response->getMemberships();
            foreach ($readmemberships as $readmembership) {
                if ($readmembership->getRole() == "Learner") {
                    $moodleuserid = turnitintooltwo_user::get_moodle_user_id($readmembership->getUserId());
                    unset($students[$moodleuserid]);
                }
            }
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'membercheckerror');
        }

        // Get the suspended users.
        $suspendedusers = get_suspended_userids($context, true);

        // Enrol remaining unenrolled users to the course.
        $members = array_keys($students);
        foreach ($members as $member) {
            // Don't include user if they are suspended.
            $user = new turnitintooltwo_user($member, "Learner");
            if (isset($suspendedusers[$user->id])) {
                continue;
            }
            $user->join_user_to_class($course->turnitin_cid);
        }
        return true;
    }

    /**
     * Get all the membership ids for a particular class
     *
     * @param int $tiicourseid the course id on Turnitin
     * @return array $tiiclassmembers all the membership Ids for the class if successful
     */
    private function get_class_memberships($tiicourseid) {

        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $membership = new TiiMembership();
        $membership->setClassId($tiicourseid);

        try {
            $response = $turnitincall->findMemberships($membership);
            $findmembership = $response->getMembership();
            $tiiclassmembers = $findmembership->getMembershipIds();

            return $tiiclassmembers;
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'membercheckerror');
        }
    }

    /**
     * Create assignment on Moodle, creates each individual part (and the relevant event)
     * and in turn creates them on Turnitin
     *
     * @global type $USER
     * @global type $DB
     * @return int turnitintooltwo id
     */
    public function create_moodle_assignment() {
        global $USER, $DB;

        $config = turnitintooltwo_admin_config();

        // Get Moodle Course Object.
        $course = $this->get_course_data($this->turnitintooltwo->course);

        // If PHP UNIT tests are running and account/secretkey/apiurl are empty, just create basic object and return.
        if ((defined('PHPUNIT_TEST') && PHPUNIT_TEST) &&
            (empty($config->accountid) || empty($config->secretkey) || empty($config->apiurl))) {

            $turnitintooltwo = new stdClass();
            $turnitintooltwo->timecreated = time();
            $turnitintooltwo->timemodified = time();
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

            return $turnitintooltwo->id;
        }

        // Get the Turnitin owner of this this Course or make user the owner if none.
        $ownerid = $this->get_tii_owner($course->id);
        if (!empty($ownerid)) {
            $owner = new turnitintooltwo_user($ownerid, 'Instructor');
        } else {
            $owner = new turnitintooltwo_user($USER->id, 'Instructor');
        }

        // Setup or edit course in Turnitin.
        if ($course->turnitin_cid == 0) {
            $tiicoursedata = $this->create_tii_course($course, $owner->id);
            $course->turnitin_cid = $tiicoursedata->turnitin_cid;
            $course->turnitin_ctl = $tiicoursedata->turnitin_ctl;
        } else {
            $this->edit_tii_course($course);
            $course->turnitin_ctl = $course->fullname . " (Moodle TT)";
        }
        $owner->join_user_to_class($course->turnitin_cid);

        // Insert the default options for the assignment.
        $this->turnitintooltwo->timecreated = time();
        $this->turnitintooltwo->dateformat = "d/m/Y";
        $this->turnitintooltwo->commentedittime = 1800;
        $this->turnitintooltwo->commentmaxsize = 800;
        $this->turnitintooltwo->autosubmission = 1;
        $this->turnitintooltwo->gradedisplay = 2;
        $this->turnitintooltwo->shownonsubmission = 1;
        $this->turnitintooltwo->timemodified = time();
        $this->turnitintooltwo->courseid = $course->id;
        $this->turnitintooltwo->usegrademark = $config->usegrademark;

        $toolid = $DB->insert_record("turnitintooltwo", $this->turnitintooltwo);
        turnitintooltwo_activitylog("Turnitintool created (".$toolid.") - ".$this->turnitintooltwo->name , "REQUEST");

        // Do the multiple Assignment creation on turnitin for each part.
        for ($i = 1; $i <= $this->turnitintooltwo->numparts; $i++) {
            // Set the assignment details to pass to the API.
            $assignment = new TiiAssignment();
            $assignment->setClassId($course->turnitin_cid);

            $attribute = "partname".$i;
            $tiititle = $this->turnitintooltwo->name." ".$this->turnitintooltwo->$attribute;
            $tiititle = $this->truncate_title( $tiititle, TURNITIN_ASSIGNMENT_TITLE_LIMIT, 'TT' );
            $assignment->setTitle( $tiititle );

            $attribute = "dtstart".$i;
            $assignment->setStartDate(gmdate("Y-m-d\TH:i:s\Z", $this->turnitintooltwo->$attribute));
            $attribute = "dtdue".$i;
            $assignment->setDueDate(gmdate("Y-m-d\TH:i:s\Z", $this->turnitintooltwo->$attribute));
            $attribute = "dtpost".$i;
            $assignment->setFeedbackReleaseDate(gmdate("Y-m-d\TH:i:s\Z", $this->turnitintooltwo->$attribute));
            $assignment->setInstructions(strip_tags($this->turnitintooltwo->intro));
            $assignment->setAuthorOriginalityAccess($this->turnitintooltwo->studentreports);
            $assignment->setRubricId((!empty($this->turnitintooltwo->rubric)) ? $this->turnitintooltwo->rubric : '');
            $assignment->setSubmitPapersTo($this->turnitintooltwo->submitpapersto);
            $assignment->setResubmissionRule($this->turnitintooltwo->reportgenspeed);
            $assignment->setBibliographyExcluded($this->turnitintooltwo->excludebiblio);
            $assignment->setQuotedExcluded($this->turnitintooltwo->excludequoted);
            $assignment->setSmallMatchExclusionType($this->turnitintooltwo->excludetype);
            $assignment->setSmallMatchExclusionThreshold((int)$this->turnitintooltwo->excludevalue);
            if ($config->useanon) {
                $assignment->setAnonymousMarking($this->turnitintooltwo->anon);
            }
            $assignment->setAllowNonOrSubmissions($this->turnitintooltwo->allownonor);
            $assignment->setLateSubmissionsAllowed($this->turnitintooltwo->allowlate);
            if ($config->repositoryoption == ADMIN_REPOSITORY_OPTION_EXPANDED ||
                $config->repositoryoption == ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL) {
                $institutioncheck = (isset($this->turnitintooltwo->institution_check)) ? $this->turnitintooltwo->institution_check : 0;
                $assignment->setInstitutionCheck($institutioncheck);
            }

            $attribute = "maxmarks".$i;
            $assignment->setMaxGrade((isset($this->turnitintooltwo->$attribute)) ? $this->turnitintooltwo->$attribute : 0);
            $assignment->setSubmittedDocumentsCheck($this->turnitintooltwo->spapercheck);
            $assignment->setInternetCheck($this->turnitintooltwo->internetcheck);
            $assignment->setPublicationsCheck($this->turnitintooltwo->journalcheck);

            $transmatch = (isset($this->turnitintooltwo->transmatch)) ? $this->turnitintooltwo->transmatch : 0;
            $assignment->setTranslatedMatching($transmatch);

            // Erater settings.
            $assignment->setErater((isset($this->turnitintooltwo->erater)) ? $this->turnitintooltwo->erater : 0);

            $eraterspelling = (isset($this->turnitintooltwo->erater_spelling)) ? $this->turnitintooltwo->erater_spelling : 0;
            $assignment->setEraterSpelling($eraterspelling);

            $eratergrammar = (isset($this->turnitintooltwo->erater_grammar)) ? $this->turnitintooltwo->erater_grammar : 0;
            $assignment->setEraterGrammar($eratergrammar);

            $eraterusage = (isset($this->turnitintooltwo->erater_usage)) ? $this->turnitintooltwo->erater_usage : 0;
            $assignment->setEraterUsage($eraterusage);

            $eratermechanics = (isset($this->turnitintooltwo->erater_mechanics)) ? $this->turnitintooltwo->erater_mechanics : 0;
            $assignment->setEraterMechanics($eratermechanics);

            $eraterstyle = (isset($this->turnitintooltwo->erater_style)) ? $this->turnitintooltwo->erater_style : 0;
            $assignment->setEraterStyle($eraterstyle);

            $eraterdictionary = (isset($this->turnitintooltwo->erater_dictionary)) ? $this->turnitintooltwo->erater_dictionary : 'en_US';
            $assignment->setEraterSpellingDictionary($eraterdictionary);

            $eraterhandbook = (isset($this->turnitintooltwo->erater_handbook)) ? $this->turnitintooltwo->erater_handbook : 0;
            $assignment->setEraterHandbook($eraterhandbook);

            // Create Assignment on Turnitin.
            $newassignmentid = $this->create_tii_assignment($assignment, $toolid, $i);

            // Save Part details.
            $part = new stdClass();
            $part->turnitintooltwoid = $toolid;
            $attribute = "partname".$i;
            $part->partname = $this->turnitintooltwo->$attribute;
            $part->tiiassignid = $newassignmentid;
            $attribute = "dtstart".$i;
            $part->dtstart = $this->turnitintooltwo->$attribute;
            $attribute = "dtdue".$i;
            $part->dtdue = $this->turnitintooltwo->$attribute;
            $attribute = "dtpost".$i;
            $part->dtpost = $this->turnitintooltwo->$attribute;
            $attribute = "maxmarks".$i;
            $part->maxmarks = (empty($this->turnitintooltwo->$attribute)) ? 0 : $this->turnitintooltwo->$attribute;
            $part->deleted = 0;

            if (!$insert = $DB->insert_record('turnitintooltwo_parts', $part)) {
                $DB->delete_records('turnitintooltwo', array('id' => $toolid));
                turnitintooltwo_print_error('partdberror', 'turnitintooltwo', null, $i, __FILE__, __LINE__);
            } else {
                turnitintooltwo_activitylog("Moodle Assignment part created (".$insert.") - ".$part->tiiassignid , "REQUEST");
            }

            $this->create_event($toolid, $part->partname, $part->dtdue);
        }

        // Define grade settings.
        $this->turnitintooltwo->id = $toolid;
        turnitintooltwo_grade_item_update($this->turnitintooltwo);

        return $this->turnitintooltwo->id;
    }

    /**
     * Create event in Moodle for each part
     *
     * @global type $CFG
     * @param int id of turnitintooltwo
     * @param text name of part
     * @param date part due date
     */
    public function create_event($toolid, $partname, $duedate) {
        global $CFG;

        $properties = new stdClass();
        $properties->name = $this->turnitintooltwo->name . ' - ' . $partname;
        $intro = strip_pluginfile_content($this->turnitintooltwo->intro);
        $intro = preg_replace("/<img[^>]+\>/i", "", $intro);
        $properties->description = ($intro == null) ? '' : $intro;
        $properties->courseid = $this->turnitintooltwo->course;
        $properties->groupid = 0;
        $properties->userid = 0;
        $properties->modulename = 'turnitintooltwo';
        $properties->instance = $toolid;
        $properties->eventtype = 'due';
        $properties->timestart = $duedate;
        $properties->timeduration = 0;

        require_once($CFG->dirroot.'/calendar/lib.php');

        // Required parameters to support Moodle 3.3+ course overview block.
        if ($CFG->branch >= 33) {
            $properties->timesort = $duedate;
            $properties->type = CALENDAR_EVENT_TYPE_ACTION;
        }

        $event = new calendar_event($properties);

        return $event->update($properties, false);
    }

    /**
     * Create Assignment on Turnitin and return id, delete the instance if it fails
     *
     * @global type $DB
     * @param object $assignment add assignment instance
     * @param var $toolid turnitintooltwo id
     */
    public static function create_tii_assignment($assignment, $toolid, $partnumber,
                                                $usecontext = "turnitintooltwo", $workflowcontext = "site") {
        global $DB;
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        try {
            $response = $turnitincall->createAssignment($assignment);
            $newassignment = $response->getAssignment();

            turnitintooltwo_activitylog("Part created as Turnitin Assignment (".$newassignment->getAssignmentId().
                                        ") - Tool Id: (".$toolid.") - Part num: (".$partnumber.")", "REQUEST");

            if ($usecontext == "turnitintooltwo") {
                $_SESSION["assignment_updated"][$toolid] = time();
            }

            return $newassignment->getAssignmentId();
        } catch (Exception $e) {
            if ($partnumber == 1 && $usecontext == "turnitintooltwo") {
                $DB->delete_records('turnitintooltwo', array('id' => $toolid));
            }
            $toscreen = true;
            if ($workflowcontext == "cron") {
                mtrace(get_string('ppassignmentcreateerror', 'turnitintooltwo'));
                $toscreen = false;
            }
            $turnitincomms->handle_exceptions($e, 'createassignmenterror', $toscreen);
        }
    }

    /**
     * Edit Assignment on Turnitin
     *
     * @param object $assignment edit assignment instance
     */
    public function edit_tii_assignment($assignment, $workflowcontext = "site") {
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        try {
            $turnitincall->updateAssignment($assignment);

            $_SESSION["assignment_updated"][$assignment->getAssignmentId()] = time();

            turnitintooltwo_activitylog("Turnitin Assignment part updated - id: ".$assignment->getAssignmentId(), "REQUEST");

            return array('success' => true, 'tiiassignmentid' => $assignment->getAssignmentId());

        } catch (Exception $e) {
            $toscreen = true;

            // Separate error handling for the Plagiarism plugin.
            if ($workflowcontext == "cron") {

                $error = new stdClass();
                $error->title = $assignment->getTitle();
                $error->assignmentid = $assignment->getAssignmentId();
                $errorstr = get_string('ppassignmentediterror', 'turnitintooltwo', $error);

                mtrace($errorstr);
                $toscreen = false;
            }

            $turnitincomms->handle_exceptions($e, 'editassignmenterror', $toscreen);

            // Return error string as we use this in the plagiarism plugin.
            if ($workflowcontext == "cron") {
                return array('success' => false, 'error' => $errorstr,
                            'tiiassignmentid' => $assignment->getAssignmentId());
            } else {
                return array('success' => false, 'error' => get_string('editassignmenterror', 'turnitintooltwo'));
            }
        }
    }

    /**
     * Delete the assignment, parts and associated event from Moodle (not from Turnitin)
     *
     * @global type $CFG
     * @global type $DB
     * @param int $id instanced id of the turnitin assignment
     * @return boolean
     */
    public function delete_moodle_assignment($id) {
        global $CFG, $DB;
        $result = true;

        // Get the Moodle Turnitintool (Assignment) Object.
        if (!$turnitintooltwo = $DB->get_record("turnitintooltwo", array("id" => $id))) {
            return true;
        }

        // Get Current Moodle Turnitin Tool parts and delete them.
        $parts = $this->get_parts($id);

        foreach ($parts as $part) {
            $this->delete_moodle_assignment_part($id, $part->id);
            turnitintooltwo_activitylog("Deleted assignment part - id (".$part->id." - ".$part->tiiassignid.")", "REQUEST");
        }

        // Delete events for this assignment / part.
        $dbselect = " modulename = ? AND instance = ? ";

        $DB->delete_records_select('event', $dbselect, array('turnitintooltwo', $id));
        if (!$DB->delete_records("turnitintooltwo", array("id" => $id))) {
            $result = false;
        }

        turnitintooltwo_activitylog("Deleted tool instance - id (".$id.")", "REQUEST");

        // General Clean Up.
        if ($oldcourses = $DB->get_records("turnitintooltwo_courses")) {
            foreach ($oldcourses as $oldcourse) {
                // Delete the Turnitin Classes data if the Moodle courses no longer exists.
                if (!$DB->count_records("course", array("id" => $oldcourse->courseid)) > 0) {
                    $DB->delete_records("turnitintooltwo_courses", array("courseid" => $oldcourse->courseid));
                    turnitintooltwo_activitylog("Old Moodle Course deleted - id (".$oldcourse->courseid." - ".
                        $oldcourse->turnitin_cid.")", "REQUEST");
                }
                // Delete the Turnitin Class data if no more turnitin assignments exist in it.
                if (!$DB->count_records("turnitintooltwo", array("course" => $oldcourse->courseid)) > 0) {
                    $DB->delete_records("turnitintooltwo_courses", array("courseid" => $oldcourse->courseid,
                                                        "course_type" => "TT"));
                    turnitintooltwo_activitylog("Old Moodle Course deleted - id (".$oldcourse->courseid." - ".
                        $oldcourse->turnitin_cid.")", "REQUEST");
                }
            }
        }

        // Define grade settings.
        @include_once($CFG->libdir . "/gradelib.php");
        $params = array('deleted' => 1);
        grade_update('mod/turnitintooltwo', $turnitintooltwo->course, 'mod', 'turnitintooltwo', $id, 0, null, $params);

        return $result;
    }

    /**
     * Delete Assignment part and associated event from Moodle
     *
     * @param int $toolid
     * @param int $partid
     * @return boolean
     */
    public function delete_moodle_assignment_part($toolid, $partid) {
        global $DB;

        // Delete submissions.
        $DB->delete_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $toolid, 'submission_part' => $partid));

        // Get part details so we have part name for deleting event.
        $part = $this->get_part_details($partid);

        // Delete Part.
        if (!$DB->delete_records("turnitintooltwo_parts", array("id" => $partid))) {
            turnitintooltwo_print_error('partdeleteerror', 'turnitintooltwo', null, $partid, __FILE__, __LINE__);
            exit;
        }

        // Delete event.
        $turnitintooltwonow = $DB->get_record("turnitintooltwo", array("id" => $toolid));
        $dbselect = " modulename = ? AND instance = ? AND name LIKE ? ";
        $DB->delete_records_select('event', $dbselect,
                        array('turnitintooltwo', $toolid, $turnitintooltwonow->name.' - '.$part->partname));

        // Update number of parts for the turnitintooltwo.
        $turnitintooltwo = new stdClass();
        $turnitintooltwo->id = $toolid;
        $turnitintooltwo->numparts = $turnitintooltwonow->numparts - 1;
        $turnitintooltwo->needs_updating = 1;
        $DB->update_record("turnitintooltwo", $turnitintooltwo);
        return true;
    }

    /**
     * Get the start date for the specified part
     *
     * @global type $DB
     * @param int $partid
     * @return date the start date of the part
     */
    public function get_start_date($partid = 0) {
        global $DB;

        $sqlarray = array('turnitintooltwoid' => $this->id);
        if ($partid != 0) {
            $sqlarray["id"] = $partid;
        }

        $part = $DB->get_record('turnitintooltwo_parts', $sqlarray, 'MIN(dtstart) AS dtstart');
        return $part->dtstart;
    }

    /**
     * Get the assignment parts for this assignment
     *
     * @global type $DB
     * @param bool $peermarks get peer mark assignments
     * @return array Returns the parts or empty array if no parts are found
     */
    public function get_parts($peermarks = true) {
        global $DB;
        if ($parts = $DB->get_records("turnitintooltwo_parts",
                                        array("turnitintooltwoid" => $this->turnitintooltwo->id), 'id ASC')) {
            if ($peermarks) {
                foreach ($parts as $part) {
                    $parts[$part->id]->peermark_assignments = $this->get_peermark_assignments($part->tiiassignid);
                }
            }
            return $parts;
        } else {
            return array();
        }
    }

    /**
     * Get the part details from the part id
     *
     * @param var $partid The Part ID of the part in turnitintooltwo_parts
     * @return array Returns the part details or empty array if the part is not found
     */
    public function get_part_details($partid, $idtype = "moodle") {
        global $DB;

        $idfield = ($idtype == 'turnitin') ? 'tiiassignid' : 'id';
        if ($part = $DB->get_record('turnitintooltwo_parts', array($idfield => $partid))) {
            $part->peermark_assignments = $this->get_peermark_assignments($part->tiiassignid);
            return $part;
        } else {
            return array();
        }
    }

    /**
     * Check for parts that have been duplicated on a restore
     *
     * @global type $DB
     * @param int $parttiiid the id on Turnitin for the assignment part
     * @param int $turnitintooltwoid the id of the turnitintooltwo in Moodle
     * @return array containing any duplicate parts
     */
    public function get_duplicate_parts($parttiiid, $turnitintooltwoid) {
        global $DB;
        $dupparts = $DB->get_records_select('turnitintooltwo_parts', " tiiassignid = ? AND turnitintooltwoid != ? ",
                                                array($parttiiid, $turnitintooltwoid));

        $dups = array();
        foreach ($dupparts as $duppart) {
            $duptii = $DB->get_record('turnitintooltwo', array('id' => $duppart->turnitintooltwoid));
            $dupcm = get_coursemodule_from_instance('turnitintooltwo', $duptii->id);
            $dupcourse = $DB->get_record('course', array('id' => $duptii->course));

            $dups[$duppart->id] = $duppart;
            $dups[$duppart->id]->course_name = $dupcourse->fullname;
            $dups[$duppart->id]->course_shortname = $dupcourse->shortname;
            $dups[$duppart->id]->cm_id = $dupcm->id;
            $dups[$duppart->id]->tool_name = $duptii->name;
        }

        return $dups;
    }

    /**
     * Edit and validate an individual part field
     *
     * @global type $DB
     * @global type $USER
     * @param int $partid the id for the assignment part
     * @param string $fieldname
     * @param mixed $fieldvalue
     * @return array containing a status and an error message if applicable
     */
    public function edit_part_field($partid, $fieldname, $fieldvalue) {
        global $DB;
        $return = array();
        $return["success"] = true;
        $partdetails = $this->get_part_details($partid);
        $return["partid"] = $partid;

        // Update Turnitin Assignment.
        $assignment = new TiiAssignment();
        $assignment->setAssignmentId($partdetails->tiiassignid);
        $return["field"] = $fieldname;
        switch ($fieldname) {
            case "partname":
                $fieldvalue = trim($fieldvalue);
                $partnames = $DB->get_records_select('turnitintooltwo_parts',
                                                    ' turnitintooltwoid = ? AND id != ? ',
                                                    array($partdetails->turnitintooltwoid, $partid), '', 'partname');

                $names = array();
                foreach ($partnames as $part) {
                    $names[] = strtolower($part->partname);
                }

                $origtiititle = $this->turnitintooltwo->name." ".$fieldvalue;
                $tiititle = $this->truncate_title( $origtiititle, TURNITIN_ASSIGNMENT_TITLE_LIMIT, 'TT' );

                if (empty($fieldvalue) || ctype_space($fieldvalue)) {
                    $return['success'] = false;
                    $return['msg'] = get_string('partnameerror', 'turnitintooltwo');
                } else if (in_array(trim(strtolower($fieldvalue)), $names)) {
                    $return['success'] = false;
                    $return['msg'] = get_string('uniquepartname', 'turnitintooltwo');
                } else if (strpos($tiititle, $origtiititle) === false) {
                    $return['success'] = false;
                    $return['msg'] = get_string('partnametoolarge', 'turnitintooltwo');
                } else {
                    $assignment->setTitle( $tiititle );
                }
                break;

            case "maxmarks":
                if (!is_numeric($fieldvalue) || $fieldvalue < 0 || $fieldvalue > 100) {
                    $return["success"] = false;
                    $return["msg"] = get_string('maxmarkserror', 'turnitintooltwo');
                } else {
                    $assignment->setMaxGrade($fieldvalue);
                }
                break;

            case "dtstart":
                if ($fieldvalue <= strtotime('1 year ago')) {
                    $return['success'] = false;
                    $return['msg'] = get_string('startdatenotyearago', 'turnitintooltwo');
                } else if ($fieldvalue >= $partdetails->dtdue) {
                    $return['success'] = false;
                    $return['msg'] = get_string('partdueerror', 'turnitintooltwo');
                } else if ($fieldvalue > $partdetails->dtpost) {
                    $return['success'] = false;
                    $return['msg'] = get_string('partposterror', 'turnitintooltwo');
                }

                $assignment->setStartDate(gmdate("Y-m-d\TH:i:s\Z", $fieldvalue));
                break;

            case "dtdue":
                if ($fieldvalue <= $partdetails->dtstart) {
                    $return['success'] = false;
                    $return['msg'] = get_string('partdueerror', 'turnitintooltwo');
                }

                $assignment->setDueDate(gmdate("Y-m-d\TH:i:s\Z", $fieldvalue));
                break;

            case "dtpost":
                if ($fieldvalue < $partdetails->dtstart) {
                    $return['success'] = false;
                    $return['msg'] = get_string('partposterror', 'turnitintooltwo');
                }

                // Disable anonymous marking in Moodle if the post date has passed.
                if ($this->turnitintooltwo->anon && $partdetails->submitted == 1 && $fieldvalue < time()) {
                    $partdetails->unanon = 1;
                }

                // If post date is moved beyond the current time, reset anon gradebook flag.
                if ($fieldvalue > time()) {
                    $updateassignment = new stdClass();
                    $updateassignment->id = $partdetails->turnitintooltwoid;
                    $updateassignment->anongradebook = 0;
                    $DB->update_record("turnitintooltwo", $updateassignment);
                }

                $assignment->setFeedbackReleaseDate(gmdate("Y-m-d\TH:i:s\Z", $fieldvalue));
                break;
        }

        if ($return["success"] != false) {
            $this->edit_tii_assignment($assignment);
        } else {
            $return['msg'] = str_replace('<br />', '', $return['msg']);
            return $return;
        }
        $currenteventname = $this->turnitintooltwo->name." - ".$partdetails->partname;

        // Edit Part in Moodle.
        switch ($fieldname) {
            case "partname":
                $partdetails->partname = $fieldvalue;
                break;
            case "maxmarks":
                $partdetails->maxmarks = $assignment->getMaxGrade();
                break;
            case "dtstart":
            case "dtdue":
            case "dtpost":
                $partdetails->$fieldname = $fieldvalue;
                break;
        }

        if (!$dbpart = $DB->update_record('turnitintooltwo_parts', $partdetails)) {
            turnitintooltwo_print_error('partupdateerror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
            exit();
        }

        // Update existing events for this assignment part if title or due date changed.
        if ($fieldname == "partname" || $fieldname == "dtdue") {
            turnitintooltwo_update_event($this->turnitintooltwo, $partdetails);
        }

        // Update grade settings.
        turnitintooltwo_grade_item_update($this->turnitintooltwo);

        return $return;
    }

    /**
     * Edit the assignment in Moodle then on Turnitin
     *
     * @global type $USER
     * @global type $DB
     * @param boolean $createevent - setting to determine whether to create a calendar event.
     * @return boolean
     */
    public function edit_moodle_assignment($createevent = true, $restore = false) {
        global $DB;

        $config = turnitintooltwo_admin_config();

        $this->turnitintooltwo->id = $this->id;
        $this->turnitintooltwo->timemodified = time();

        // Get Current Moodle Turnitin Tool data (Assignment).
        if (!$turnitintooltwonow = $DB->get_record("turnitintooltwo", array("id" => $this->id))) {
            turnitintooltwo_print_error('turnitintooltwogeterror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
            exit();
        }

        // Get Moodle Course Object.
        $legacy = (!empty($turnitintooltwonow->legacy)) ? $turnitintooltwonow->legacy : 0;
        $coursetype = turnitintooltwo_get_course_type($legacy);
        $course = $this->get_course_data($this->turnitintooltwo->course, $coursetype);

        // Edit course in Turnitin.
        $this->edit_tii_course($course, $coursetype);
        $course->turnitin_ctl = $course->fullname . " (Moodle TT)";

        // Get Current Moodle Turnitin Tool Parts Object.
        if (!$parts = $DB->get_records_select("turnitintooltwo_parts", " turnitintooltwoid = ? ", array($this->id), 'id ASC')) {
            turnitintooltwo_print_error('partgeterror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
            exit();
        }
        $partids = array_keys($parts);

        // Override submitpapersto if necessary when admin is forcing repository setting.
        $this->turnitintooltwo->submitpapersto = turnitintooltwo_override_repository($this->turnitintooltwo->submitpapersto);

        // Update GradeMark setting depending on config setting.
        $this->turnitintooltwo->usegrademark = $config->usegrademark;

        // Set the checkbox fields.
        $chkboxfields = array('erater_spelling', 'erater_grammar', 'erater_usage', 'erater_mechanics', 'erater_style', 'transmatch', 'institution_check');
        foreach ($chkboxfields as $field) {
            $this->set_checkbox_field($field, 0);
        }

        // Update each individual part.
        for ($i = 1; $i <= $this->turnitintooltwo->numparts; $i++) {
            // Update Turnitin Assignment.
            $assignment = new TiiAssignment();
            $assignment->setClassId($course->turnitin_cid);
            $assignment->setAuthorOriginalityAccess($this->turnitintooltwo->studentreports);

            $assignment->setRubricId((!empty($this->turnitintooltwo->rubric)) ? $this->turnitintooltwo->rubric : '');
            $assignment->setSubmitPapersTo($this->turnitintooltwo->submitpapersto);
            $assignment->setResubmissionRule($this->turnitintooltwo->reportgenspeed);
            $assignment->setBibliographyExcluded($this->turnitintooltwo->excludebiblio);
            $assignment->setQuotedExcluded($this->turnitintooltwo->excludequoted);
            $assignment->setSmallMatchExclusionType($this->turnitintooltwo->excludetype);
            $assignment->setSmallMatchExclusionThreshold((int) $this->turnitintooltwo->excludevalue);
            $assignment->setLateSubmissionsAllowed($this->turnitintooltwo->allowlate);
            if ($config->repositoryoption == ADMIN_REPOSITORY_OPTION_EXPANDED ||
                $config->repositoryoption == ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL) {
                $institutioncheck = (isset($this->turnitintooltwo->institution_check)) ? $this->turnitintooltwo->institution_check : 0;
                $assignment->setInstitutionCheck($institutioncheck);
            }

            $attribute = "maxmarks".$i;
            $assignment->setMaxGrade((isset($this->turnitintooltwo->$attribute)) ? $this->turnitintooltwo->$attribute : 0);
            $assignment->setSubmittedDocumentsCheck($this->turnitintooltwo->spapercheck);
            $assignment->setInternetCheck($this->turnitintooltwo->internetcheck);
            $assignment->setPublicationsCheck($this->turnitintooltwo->journalcheck);
            $assignment->setTranslatedMatching($this->turnitintooltwo->transmatch);
            $assignment->setAllowNonOrSubmissions($this->turnitintooltwo->allownonor);

            // Erater settings.
            $assignment->setErater((isset($this->turnitintooltwo->erater)) ? $this->turnitintooltwo->erater : 0);
            $assignment->setEraterSpelling($this->turnitintooltwo->erater_spelling);
            $assignment->setEraterGrammar($this->turnitintooltwo->erater_grammar);
            $assignment->setEraterUsage($this->turnitintooltwo->erater_usage);
            $assignment->setEraterMechanics($this->turnitintooltwo->erater_mechanics);
            $assignment->setEraterStyle($this->turnitintooltwo->erater_style);
            $eraterdictionary = 'en_US';
            if (isset($this->turnitintooltwo->erater_dictionary)) {
                $eraterdictionary = $this->turnitintooltwo->erater_dictionary;
            }
            $assignment->setEraterSpellingDictionary($eraterdictionary);
            $eraterhandbook = 0;
            if (isset($this->turnitintooltwo->erater_handbook)) {
                $eraterhandbook = $this->turnitintooltwo->erater_handbook;
            }
            $assignment->setEraterHandbook($eraterhandbook);

            $attribute = "dtstart".$i;
            if (($restore) && ($this->turnitintooltwo->$attribute < strtotime("-1 year"))) {
                $assignment->setStartDate(gmdate("Y-m-d\TH:i:s\Z", time()));
                $assignment->setDueDate(gmdate("Y-m-d\TH:i:s\Z", strtotime("+1 week")));
                $assignment->setFeedbackReleaseDate(gmdate("Y-m-d\TH:i:s\Z", strtotime("+1 week")));
            } else {
                $assignment->setStartDate(gmdate("Y-m-d\TH:i:s\Z", $this->turnitintooltwo->$attribute));
                $attribute = "dtdue".$i;
                $assignment->setDueDate(gmdate("Y-m-d\TH:i:s\Z", $this->turnitintooltwo->$attribute));
                $attribute = "dtpost".$i;
                $assignment->setFeedbackReleaseDate(gmdate("Y-m-d\TH:i:s\Z", $this->turnitintooltwo->$attribute));
            }

            $attribute = "partname".$i;
            $tiititle = $this->turnitintooltwo->name." ".$this->turnitintooltwo->$attribute;
            $tiititle = $this->truncate_title( $tiititle, TURNITIN_ASSIGNMENT_TITLE_LIMIT, 'TT' );
            $assignment->setTitle( $tiititle );

            // Initialise part.
            $part = new stdClass();
            $part->turnitintooltwoid = $this->id;
            $part->partname = $this->turnitintooltwo->$attribute;
            $part->deleted = 0;
            $part->maxmarks = (int)$assignment->getMaxGrade();
            $part->dtstart = strtotime($assignment->getStartDate());
            $part->dtdue = strtotime($assignment->getDueDate());
            $part->dtpost = strtotime($assignment->getFeedbackReleaseDate());

            $parttiiassignid = 0;
            if ($i <= count($partids) && !empty($partids[$i - 1])) {
                $partdetails = $this->get_part_details($partids[$i - 1]);
                $part->submitted = $partdetails->submitted;
                $part->unanon = $partdetails->unanon;
                // Set anonymous marking depending on whether part has been unanonymised.
                if ($config->useanon
                    && $partdetails->unanon != 1
                    && $partdetails->submitted != 1 ) {
                    $assignment->setAnonymousMarking($this->turnitintooltwo->anon);
                }
                $parttiiassignid = $partdetails->tiiassignid;
            }

            if ($parttiiassignid > 0) {
                $assignment->setAssignmentId($parttiiassignid);

                $this->edit_tii_assignment($assignment);
            } else {
                // Set anonymous marking if it is supposed to be enabled.
                if ($config->useanon) {
                    $assignment->setAnonymousMarking($this->turnitintooltwo->anon);
                }

                $parttiiassignid = $this->create_tii_assignment($assignment, $this->id, $i);
                $part->submitted = 0;
            }

            $part->tiiassignid = $parttiiassignid;

            // Unanonymise part if necessary.
            if ($part->dtpost < time() && $part->submitted == 1) {
                $part->unanon = 1;
            }

            if ($i <= count($partids) && !empty($partdetails->id)) {
                $part->id = $partids[$i - 1];
                // Get Current Moodle part data.
                if (!$partnow = $DB->get_record("turnitintooltwo_parts", array("id" => $part->id))) {
                    turnitintooltwo_print_error('partgeterror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
                    exit();
                }

                if (!$dbpart = $DB->update_record('turnitintooltwo_parts', $part)) {
                    turnitintooltwo_print_error('partupdateerror', 'turnitintooltwo', null, $i, __FILE__, __LINE__);
                    exit();
                } else {
                    turnitintooltwo_activitylog("Moodle Assignment part updated (".$part->id.") - ".$part->partname,
                                                        "REQUEST");
                }

                // Delete existing events for this assignment part.
                $eventname = $turnitintooltwonow->name." - ".$partnow->partname;
                $dbselect = " modulename = ? AND instance = ? AND name LIKE ? ";
                $DB->delete_records_select('event', $dbselect, array('turnitintooltwo', $this->id, $eventname));
            } else {
                if (!$dbpart = $DB->insert_record('turnitintooltwo_parts', $part)) {
                    turnitintooltwo_print_error('partdberror', 'turnitintooltwo', null, $i, __FILE__, __LINE__);
                    exit();
                }
            }

            if ($createevent == true) {
                $this->create_event($this->id, $part->partname, $part->dtdue);
            }
        }

        $this->turnitintooltwo->timemodified = time();
        $update = $DB->update_record("turnitintooltwo", $this->turnitintooltwo);
        turnitintooltwo_activitylog("Turnitintool updated (".$this->id.") - ".$this->turnitintooltwo->name, "REQUEST");

        // Define grade settings.
        if (!isset($_SESSION['tii_assignment_reset'])) {
            turnitintooltwo_grade_item_update($this->turnitintooltwo);
        }
        return $update;
    }

    /**
     * Initialise a checkbox value that may not have been set in the edit module form.
     */
    public function set_checkbox_field($field, $value = 0) {
        if (!isset($this->turnitintooltwo->$field)) {
            $this->turnitintooltwo->$field = $value;
        }
    }

    /**
     * Return an array with the parts still available to be submitted to
     *
     * @global type $DB
     * @param int $userid
     * @return array of parts or empty array if there are none
     */
    public function get_parts_available_to_submit($userid = 0, $istutor = null) {
        global $DB;

        if ($this->turnitintooltwo->allowlate == 1 || $istutor) {
            $partsavailable = $DB->get_records_select('turnitintooltwo_parts', " turnitintooltwoid = ? AND dtstart < ? ",
                                        array($this->turnitintooltwo->id, time()));
        } else {
            $partsavailable = $DB->get_records_select('turnitintooltwo_parts',
                                                " turnitintooltwoid = ? AND dtstart < ? AND dtdue > ? ",
                                                array($this->turnitintooltwo->id, time(), time()));
        }

        if ($userid != 0) {
            foreach ($partsavailable as $partavailable) {
                $submitted = (!$DB->get_records('turnitintooltwo_submissions', array('turnitintooltwoid' => $this->id,
                                'submission_part' => $partavailable->id, 'userid' => $userid))) ? 0 : 1;

                if ($submitted && $this->turnitintooltwo->reportgenspeed == 0) {
                    unset($partsavailable[$partavailable->id]);
                }
            }
        }

        if (!$partsavailable) {
            $partsavailable = array();
        }
        return $partsavailable;
    }

    /**
     * Gets the submissions made by a specified user, can include assignment id
     * or part id as well, if part id is specified then only 1 should be returned
     *
     * @global type $DB
     * @param int $userid
     * @param int $assignmentid
     * @param int $partid
     * @return array of user submissions
     */
    public function get_user_submissions($userid, $assignmentid = 0, $partid = 0) {
        global $DB;

        $fields = array('userid' => $userid);
        if ($assignmentid != 0) {
            $fields['turnitintooltwoid'] = $assignmentid;
        }
        if ($partid != 0) {
            $fields['submission_part'] = $partid;
        }

        $usersubmissions = $DB->get_records('turnitintooltwo_submissions', $fields);

        return $usersubmissions;
    }

    /**
     * Update all the assignment submissions with data from Turnitin
     *
     * @param object $part
     * @param object $start position in submissions array to get details from
     */
    private function update_submissions_from_tii($cm, $part, $start = 0, $save = false) {
        global $USER, $DB;

        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        // Only save data if the user is an instructor.
        $istutor = has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id));
        // Or if a submission belongs to the logged in user.
        $tiiuser = $DB->get_record("turnitintooltwo_users", array("userid" => $USER->id), "turnitin_uid");
        $tiiuserid = (isset($tiiuser->turnitin_uid)) ? $tiiuser->turnitin_uid : 0;

        try {
            $submission = new TiiSubmission();
            $submission->setSubmissionIds(array_slice($_SESSION["TiiSubmissions"][$part->id], $start,
                                                                        TURNITINTOOLTWO_SUBMISSION_GET_LIMIT));

            $response = $turnitincall->readSubmissions($submission);
            $readsubmissions = $response->getSubmissions();

            foreach ($readsubmissions as $readsubmission) {
                if ($readsubmission->getAuthorUserId() != "-1" && ($istutor || $tiiuserid == $readsubmission->getAuthorUserId())) {
                    $turnitintooltwosubmission = new turnitintooltwo_submission($readsubmission->getSubmissionId(),
                                                                                "turnitin", $this, $part->id);
                    $turnitintooltwosubmission->save_updated_submission_data($readsubmission, true, $save);
                }
            }

        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'tiisubmissiongeterror');
        }
    }

    /**
     * Get ids of submissions from Turnitin
     *
     * @param object $part the part to get submissions for
     */
    public function get_submission_ids_from_tii($part, $usetimestamp = true) {
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        try {
            $submission = new TiiSubmission();
            $submission->setAssignmentId($part->tiiassignid);

            // Only update submissions that have been modified since an hour before last update.
            if (!empty($part->gradesupdated) && $usetimestamp) {
                $submission->setDateFrom(gmdate("Y-m-d\TH:i:s\Z", $part->gradesupdated - (60 * 60)));
            }

            $response = $turnitincall->findSubmissions($submission);
            $findsubmission = $response->getSubmission();

            $_SESSION["TiiSubmissions"][$part->id] = $findsubmission->getSubmissionIds();

        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'tiisubmissionsgeterror', false);
        }
    }

    /**
     * Refresh submissions data stored locally by updating from Turnitin
     *
     * @param int $start array of assignment ids, if 0 then array is created inside
     */
    public function refresh_submissions($cm, $part, $start = 0, $save = false) {
        if (empty($_SESSION["TiiSubmissions"][$part->id])) {
            $_SESSION["TiiSubmissions"][$part->id] = array();
        }

        if ($start < count($_SESSION["TiiSubmissions"][$part->id])) {
            $this->update_submissions_from_tii($cm, $part, $start, $save);
        }
    }

    /**
     * Update assignment from Turnitin
     *
     * @global type $DB
     * @param array $assignmentids array of assignment ids, if 0 then array is created inside
     * @return object
     */
    public function update_assignment_from_tii($assignmentids = 0) {
        global $DB;

        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        if (!$assignmentids) {
            $parts = $this->get_parts();
            $assignments = array();
            foreach ($parts as $part) {
                $assignments[] = $part->tiiassignid;
                $partids[$part->tiiassignid] = $part->id;
            }
        } else {
            $assignments = $assignmentids;
        }

        $assignment = new TiiAssignment();

        try {
            if (count($assignments) == 1) {
                $assignment->setAssignmentId(current($assignments));
                $response = $turnitincall->readAssignment($assignment);
                $readassignments[0] = $response->getAssignment();
            } else {
                $assignment->setAssignmentIds($assignments);
                $response = $turnitincall->readAssignments($assignment);
                $readassignments = $response->getAssignments();
            }
            $assignmentdetails = "";
            $newparts = array();
            foreach ($readassignments as $readassignment) {
                $part = new stdClass();
                $part->dtstart = strtotime($readassignment->getStartDate());
                $part->dtdue = strtotime($readassignment->getDueDate());
                $part->dtpost = strtotime($readassignment->getFeedbackReleaseDate());
                $part->maxmarks = $readassignment->getMaxGrade();
                $part->tiiassignid = $readassignment->getAssignmentId();

                if ($assignmentids == 0) {
                    $part->id = $partids[$readassignment->getAssignmentId()];
                    if (!$dbpart = $DB->update_record('turnitintooltwo_parts', $part)) {
                        turnitintooltwo_activitylog(get_string('turnitintooltwoupdateerror', 'turnitintooltwo').
                                                    ' - ID: '.$this->turnitintooltwo->id.' - Part: '.$part->id, 'API_ERROR');
                    }
                } else {
                    $part->partname = $readassignment->getTitle();
                    $newparts[] = $part;
                }

                // Update main turnitintooltwo details but only once.
                if (empty($assignmentdetails)) {
                    $assignmentdetails = new stdClass();
                    if ($assignmentids == 0) {
                        $assignmentdetails->id = $this->turnitintooltwo->id;
                    } else {
                        $assignmentdetails->timecreated = time();
                        $assignmentdetails->dateformat = "d/m/Y";
                        $assignmentdetails->autoupdates = 1;
                        $assignmentdetails->commentedittime = 1800;
                        $assignmentdetails->commentmaxsize = 800;
                        $assignmentdetails->autosubmission = 1;
                        $assignmentdetails->timemodified = time();
                        $assignmentdetails->intro = $readassignment->getInstructions();
                    }
                    $assignmentdetails->allowlate = $readassignment->getLateSubmissionsAllowed();
                    $assignmentdetails->reportgenspeed = $readassignment->getResubmissionRule();
                    $assignmentdetails->submitpapersto = $readassignment->getSubmitPapersTo();
                    $assignmentdetails->spapercheck = $readassignment->getSubmittedDocumentsCheck();
                    $assignmentdetails->internetcheck = $readassignment->getInternetCheck();
                    $assignmentdetails->journalcheck = $readassignment->getPublicationsCheck();
                    $assignmentdetails->studentreports = $readassignment->getAuthorOriginalityAccess();
                    $assignmentdetails->rubric = $readassignment->getRubricId();
                    $assignmentdetails->excludebiblio = $readassignment->getBibliographyExcluded();
                    $assignmentdetails->excludequoted = $readassignment->getQuotedExcluded();
                    $assignmentdetails->excludetype = $readassignment->getSmallMatchExclusionType();
                    $assignmentdetails->excludevalue = $readassignment->getSmallMatchExclusionThreshold();
                    $assignmentdetails->erater = $readassignment->getErater();
                    $assignmentdetails->erater_handbook = $readassignment->getEraterHandbook();
                    $assignmentdetails->erater_dictionary = $readassignment->getEraterSpellingDictionary();
                    $assignmentdetails->erater_spelling = (int)$readassignment->getEraterSpelling();
                    $assignmentdetails->erater_grammar = (int)$readassignment->getEraterGrammar();
                    $assignmentdetails->erater_usage = (int)$readassignment->getEraterUsage();
                    $assignmentdetails->erater_mechanics = (int)$readassignment->getEraterMechanics();
                    $assignmentdetails->erater_style = (int)$readassignment->getEraterStyle();
                    $assignmentdetails->transmatch = (int)$readassignment->getTranslatedMatching();
                    $assignmentdetails->allownonor = (int)$readassignment->getAllowNonOrSubmissions();
                }

                // Get Peermark Assignments.
                $peermarkassignments = $readassignment->getPeermarkAssignments();
                if (count($peermarkassignments)) {
                    $peermarkids = array();

                    foreach ($peermarkassignments as $peermarkassignment) {
                        $peermark = new stdClass();
                        $peermark->tiiassignid = $peermarkassignment->getAssignmentId();
                        $peermark->parent_tii_assign_id = $part->tiiassignid;
                        $peermark->dtstart = strtotime($peermarkassignment->getStartDate());
                        $peermark->dtdue = strtotime($peermarkassignment->getDueDate());
                        $peermark->dtpost = strtotime($peermarkassignment->getFeedbackReleaseDate());
                        $peermark->maxmarks = (int)$peermarkassignment->getMaxGrade();
                        $peermark->title = $peermarkassignment->getTitle();
                        $peermark->instructions = $peermarkassignment->getInstructions();
                        $peermark->distributed_reviews = (int)$peermarkassignment->getDistributedReviews();
                        $peermark->selected_reviews = (int)$peermarkassignment->getSelectedReviews();
                        $peermark->self_review = (int)$peermarkassignment->getSelfReviewRequired();
                        $peermark->non_submitters_review = (int)$peermarkassignment->getNonSubmittersToReview();

                        $currentpeermark = $DB->get_record('turnitintooltwo_peermarks',
                                                array('tiiassignid' => $peermark->tiiassignid));

                        if ($currentpeermark) {
                            $peermark->id = $currentpeermark->id;
                            $DB->update_record('turnitintooltwo_peermarks', $peermark);
                        } else {
                            $peermark->id = $DB->insert_record('turnitintooltwo_peermarks', $peermark);
                        }

                        $peermarkids[] = $peermark->id;
                    }

                    list($notinsql, $notinparams) = $DB->get_in_or_equal($peermarkids, SQL_PARAMS_QM, 'param', false);
                    $DB->delete_records_select('turnitintooltwo_peermarks', " parent_tii_assign_id = ? AND id ".
                                                $notinsql, array_merge(array($part->tiiassignid), $notinparams));
                } else {
                    $DB->delete_records('turnitintooltwo_peermarks', array('parent_tii_assign_id' => $part->tiiassignid));
                }
            }

            if ($assignmentids == 0) {
                if (!$update = $DB->update_record('turnitintooltwo', $assignmentdetails)) {
                    turnitintooltwo_activitylog(get_string('turnitintooltwoupdateerror', 'turnitintooltwo').' - ID: '.
                                                    $this->turnitintooltwo->id, 'API_ERROR');
                }
                $_SESSION["assignment_updated"][$this->turnitintooltwo->id] = time();
            } else {
                return array("turnitintooltwo" => $assignmentdetails, "parts" => $newparts);
            }
        } catch (Exception $e) {
            // We will use the locally stored assignment data if we can't connect to Turnitin.
            $turnitincomms->handle_exceptions($e, 'tiiassignmentgeterror', false);
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
        if ($peermarks = $DB->get_records("turnitintooltwo_peermarks", array("parent_tii_assign_id" => $tiiassignid))) {
            return $peermarks;
        } else {
            return array();
        }
    }

    /**
     * Calculates and returns the overall grade for this activity.
     *
     * @param array $submissions array of user's submissions
     * @param $submissions
     * @param $cm
     * @return array
     */
    public function get_overall_grade($submissions, $cm = '') {
        global $DB;

        $overallgrade = null;
        $parts = $this->get_parts();

        if (empty($cm)) {
            $cm = get_coursemodule_from_instance("turnitintooltwo", $this->id, $this->turnitintooltwo->course);
        }
        $istutor = has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id));

        foreach ($parts as $part) {
            $weightarray[$part->id] = $part->maxmarks;
        }
        $overallweight = array_sum($weightarray);
        $maxgrade = $this->turnitintooltwo->grade;
        if ($this->turnitintooltwo->grade < 0) { // Scale in use.
            $scale = $DB->get_record('scale', array('id' => $this->turnitintooltwo->grade * -1));
            $maxgrade = count(explode(",", $scale->scale));
        }

        foreach ($submissions as $submission) {
            if (isset($submission->submission_grade) && !is_nan($submission->submission_grade)
                && (!empty($submission->submission_gmimaged) || $istutor)
                && !is_null($submission->submission_grade) && $weightarray[$submission->submission_part] != 0) {
                $weightedgrade = $submission->submission_grade / $weightarray[$submission->submission_part];
                $overallgrade += $weightedgrade * ($weightarray[$submission->submission_part] / $overallweight) * $maxgrade;
            }
        }

        if (!is_null($overallgrade) && $this->turnitintooltwo->grade < 0) {
            return ($overallgrade == 0) ? 1 : ceil($overallgrade);
        } else {
            if (is_null($overallgrade)) {
                return "--";
            }
            return (!is_nan($overallgrade) && !is_null($overallgrade)) ? number_format($overallgrade, 2) : '--';
        }
    }

    /**
     * Count the number of submissions for this assignment
     *
     * @param object $cm course module object
     * @param int $partid specific part id, includes all if 0
     * @return int $count
     */
    public function count_submissions($cm, $partid = 0) {
        $submissions = $this->get_submissions($cm, $partid, 0, 1);
        $count = 0;
        foreach ($submissions as $part) {
            $count += count($part);
        }
        return $count;
    }

    /**
     * Returns submissions by part (and unsubmitted users if appropriate)
     *
     * @global type $DB
     * @global type $USER
     * @param object $cm course module object
     * @param int $partid specific part id, includes all if 0
     * @param int $userid specific user id, includes all if 0
     * @param int $submissionsonly flag to include/remove non submitted students from results
     * @return array of submissions by part
     */
    public function get_submissions($cm, $partid = 0, $userid = 0, $submissionsonly = 0) {
        global $DB, $USER, $CFG;

        // If no part id is specified then get them all.
        $sql = " turnitintooltwoid = ? ";
        $sqlparams = array($this->id);
        if ($partid == 0) {
            $parts = $this->get_parts();
        } else {
            $part = $this->get_part_details($partid);
            $parts[$partid] = $part;
            $sql .= " AND submission_part = ? ";
            $sqlparams[] = $partid;
        }

        $context = context_module::instance($cm->id);
        $istutor = has_capability('mod/turnitintooltwo:grade', $context);

        // If logged in as instructor then get for all users.
        if ($CFG->branch >= 311) {
            $allnamefields = implode(', ', \core_user\fields::get_name_fields());
        } else {
            $allnamefields = implode(', ', get_all_user_name_fields());
        }

        if ($istutor && $userid == 0) {
            $users = get_enrolled_users($context, 'mod/turnitintooltwo:submit', groups_get_activity_group($cm),
                                        'u.id, ' . $allnamefields);
            $users = (!$users) ? array() : $users;
        } else if ($istutor) {
            $user = $DB->get_record('user', array('id' => $userid), 'id, ' . $allnamefields);
            $users = array($userid => $user);
            $sql .= " AND userid = ? ";
            $sqlparams[] = $userid;
        } else {
            $users = array($USER->id => $USER);
            $sql .= " AND userid = ? ";
            $sqlparams[] = $USER->id;
        }

        // Get the suspended users.
        $suspendedusers = get_suspended_userids($context, true);

        // Populate the submissions array to show all users for all parts.
        $submissions = array();
        foreach ($parts as $part) {
            $submissions[$part->id] = array();
            foreach ($users as $user) {
                // Don't include user if they are suspended.
                if (isset($suspendedusers[$user->id])) {
                    continue;
                }
                $emptysubmission = new stdClass();
                $emptysubmission->userid = $user->id;
                $emptysubmission->firstname = $user->firstname;
                $emptysubmission->lastname = $user->lastname;
                $emptysubmission->fullname = fullname($user);
                $emptysubmission->submission_unanon = 0;
                $emptysubmission->nmoodle = 0;
                if ($submissionsonly == 0) {
                    $submissions[$part->id][$user->id] = $emptysubmission;
                }
            }
        }

        // Get submissions that were made where a moodle userid is known.
        // Contains moodle users both enrolled or not enrolled.
        if ($submissionsdata = $DB->get_records_select("turnitintooltwo_submissions", " userid != 0 AND ".$sql, $sqlparams)) {

            foreach ($submissionsdata as $submission) {
                $user = new turnitintooltwo_user($submission->userid, 'Learner', false);
                $submission->firstname = $user->firstname;
                $submission->lastname = $user->lastname;
                $submission->fullname = $user->fullname;
                $submission->tiiuserid = $user->tiiuserid;
                $submission->nmoodle = 0;

                if (isset($users[$user->id])) {
                    // User is a moodle user ie in array from moodle user call above.
                    $submissions[$submission->submission_part][$user->id] = $submission;
                } else if (groups_get_activity_group($cm) == 0) {
                    // User is not a moodle user ie not in array from moodle user call above and group list is set to all users.
                    $submission->nmoodle = 1;
                    $submissions[$submission->submission_part][$user->id] = $submission;
                }
            }
        }

        // Now get submissions that were made by a non moodle students.
        // These are unknown to moodle possibly non-enrolled on turnitin.
        // Possibly real but not yet linked Turnitin users. If group list is set do not get these non group users.
        if ($submissionsdata = $DB->get_records_select("turnitintooltwo_submissions", " userid = 0 AND ".$sql, $sqlparams)
                AND groups_get_activity_group($cm) == 0) {

            foreach ($submissionsdata as $submission) {
                $submission->nmoodle = 1;
                $submission->userid = $submission->submission_nmuserid;
                $submission->firstname = $submission->submission_nmfirstname;
                $submission->lastname = $submission->submission_nmlastname;
                $submission->fullname = $submission->firstname.' '.$submission->lastname;

                $submissions[$submission->submission_part][$submission->userid] = $submission;
            }
        }

        return $submissions;
    }

    /**
     * Remove the link (id) to Turnitin for each part
     *
     * @global type $DB
     */
    public function unlink_assignment() {
        global $DB;

        $parts = $this->get_parts();

        foreach ($parts as $part) {
            $tiipart = new stdClass();
            $tiipart->id = $part->id;
            $tiipart->tiiassignid = 0;

            $DB->update_record("turnitintooltwo_parts", $tiipart);
        }
    }
}
