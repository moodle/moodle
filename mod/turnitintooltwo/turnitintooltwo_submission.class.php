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

require_once(__DIR__."/lib.php");
require_once(__DIR__.'/classes/digitalreceipt/receipt_message.php');
require_once(__DIR__.'/classes/digitalreceipt/instructor_message.php');

class turnitintooltwo_submission {

    public $id;
    public $userid;
    public $firstname;
    public $lastname;
    public $fullname;
    private $turnitintooltwoid;
    public $submission_part;
    public $submission_title;
    private $submission_type;
    private $submission_filename;
    public $submission_objectid;
    public $submission_score;
    public $submission_grade;
    public $submission_gmimaged;
    public $submission_attempts;
    public $submission_modified;
    private $submission_parent;
    public $nmoodle;
    public $submission_nmuserid;
    public $submission_nmfirstname;
    public $submission_nmlastname;
    public $submission_unanon;
    private $submission_unanonreason;
    public $submission_transmatch;
    private $submission_instructors;
    public $submission_orcapable;
    public $submission_acceptnothing;
    public $overallgrade;
    private $receipt;

    public function __construct($id = 0, $idtype = "moodle", $turnitintooltwoassignment = "", $partid = "") {
        $this->receipt = new receipt_message();
        $this->instructor_receipt = new instructor_message();

        if ($idtype == "turnitin") {
            $this->submission_objectid = $id;
            $this->submission_part = $partid;
        } else {
            $this->id = $id;
        }
        if (!empty($turnitintooltwoassignment)) {
            $this->turnitintooltwoid = $turnitintooltwoassignment->turnitintooltwo->id;
        }

        if ($id != 0) {
            $this->get_submission_details($idtype, $turnitintooltwoassignment);
        }
    }

    /**
     * Create new submission object in database
     *
     * @param mixed $data to use for object
     * @return object
     */
    public function create_submission($data) {
        $this->userid = $data['studentsname'];
        $this->submission_part = $data['submissionpart'];
        $this->submission_title = $data['submissiontitle'];
        $this->submission_objectid = null;
        $this->submissionunanon = 0;

        $submission = new stdClass();
        $submission->userid = $data['studentsname'];
        $submission->turnitintooltwoid = $this->turnitintooltwoid;
        $submission->submission_part = $data['submissionpart'];
        $submission->submission_title = $data['submissiontitle'];
        $submission->submission_type = $data['submissiontype'];
        $submission->submission_objectid = null;
        $submission->submission_unanon = 0;
        $submission->submission_grade = null;
        $submission->submission_gmimaged = 0;
        $submission->submission_hash = $submission->userid.'_'.$submission->turnitintooltwoid.'_'.$submission->submission_part;

        $response = $this->insert_submission($submission);

        if ($response) {
            $this->reset_submission($data);

            return true;
        }

        return false;
    }

    /**
     * Reset submission data when resubmitting
     *
     * @global type $DB
     * @param object $submission data to insert.
     * @return boolean
     */
    public function insert_submission($submission) {
        global $DB;

        try {
            $this->id = $DB->insert_record('turnitintooltwo_submissions', $submission);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Reset submission data when resubmitting
     *
     * @global type $DB
     * @param mixed $post data to use for submission object
     * @param object $submission
     * @return type
     */
    public function reset_submission($post) {
        $this->submission_type = $post['submissiontype'];
        $this->submission_filename = "";
        $this->submission_attempts = 0;
        $this->submission_gmimaged = 0;
        $this->submission_modified = time();
        $this->submission_title = $post['submissiontitle'];
        $this->submission_score = null;
        $this->submission_grade = null;
        $this->submission_parent = 0;
        $this->submission_nmuserid = 0;
        $this->submission_nmfirstname = null;
        $this->submission_nmlastname = null;
        $this->submission_unanon = 0;
        $this->submission_unanonreason = null;
        $this->submission_transmatch = 0;
    }

    /**
     * Get full submission details and work out overall grade
     *
     * @global type $DB
     * @param type $turnitintooltwoassignment
     * @param type $submission_id
     * @param type $id_type
     * @return type
     */
    private function get_submission_details($idtype = "moodle", $turnitintooltwoassignment = "") {
        global $DB, $CFG;

        if ($idtype == "moodle") {
            $condition = array("id" => $this->id);
        } else {
            $condition = array("submission_objectid" => $this->submission_objectid);
        }

        if ($submission = $DB->get_record('turnitintooltwo_submissions',
                                            $condition, '*', IGNORE_MULTIPLE)) {
            if (empty($turnitintooltwoassignment)) {
                $turnitintooltwoassignment = new turnitintooltwo_assignment($submission->turnitintooltwoid);
                $this->turnitintooltwoid = $turnitintooltwoassignment->turnitintooltwo->id;
            }

            if (count($turnitintooltwoassignment->get_parts()) > 1) {
                if ($submission->userid != 0) {
                    $usersubmissions = $turnitintooltwoassignment->get_user_submissions($submission->userid,
                                                            $submission->turnitintooltwoid);
                    $useroverallgrade = $turnitintooltwoassignment->get_overall_grade($usersubmissions);

                    if ($turnitintooltwoassignment->turnitintooltwo->grade == 0 OR $useroverallgrade === '--') {
                        $this->overallgrade = '--';
                    } else if ($turnitintooltwoassignment->turnitintooltwo->grade < 0) { // Scale.
                        $scale = $DB->get_record('scale', array('id' => $turnitintooltwoassignment->turnitintooltwo->grade * -1));
                        $scalearray = explode(",", $scale->scale);
                        // Array is zero indexed, Scale positions are from 1 upward.
                        $index = $useroverallgrade - 1;
                        $this->overallgrade = $scalearray[$index];
                    } else {
                        $usergrade = round($useroverallgrade / $turnitintooltwoassignment->turnitintooltwo->grade * 100, 1);
                        $this->overallgrade = $usergrade.'%';
                    }
                } else {
                    $this->overallgrade = '--';
                }
            }

            foreach ($submission as $field => $value) {
                $this->$field = $value;
            }

            if ($submission->userid > 0) {
                if ($CFG->branch >= 311) {
                    $allnamefields = implode(', ', \core_user\fields::get_name_fields());
                } else {
                    $allnamefields = implode(', ', get_all_user_name_fields());
                }

                $user = $DB->get_record('user', array('id' => $submission->userid), 'id, ' . $allnamefields);
                $this->firstname = $user->firstname;
                $this->lastname = $user->lastname;
                $this->fullname = fullname($user);
                $this->nmoodle = 0;
            } else {
                $this->firstname = $submission->submission_nmfirstname;
                $this->lastname = $submission->submission_nmlastname;

                $tmpuser = new stdClass();
                $tmpuser->firstname = $submission->submission_nmfirstname;
                $tmpuser->lastname = $submission->submission_nmlastname;
                $this->fullname = fullname($tmpuser);

                $this->nmoodle = 1;
            }

        } else if ($idtype == "moodle") {
            turnitintooltwo_print_error('submissiongeterror', 'turnitintooltwo', null, null, __FILE__, __LINE__);
        }
    }

    /**
     * Upload file for submission
     *
     * @param object $cm the course module object
     * @return array result and message
     */
    public function do_file_upload($cm, $uploadoptions) {
        $context = context_module::instance($cm->id);

        // Get draft item id and save the files in the draft area.
        $draftitemid = file_get_submitted_draft_itemid('submissionfile');
        file_prepare_draft_area($draftitemid, $context->id,
                'mod_turnitintooltwo', 'submissions', $this->id, $uploadoptions);

        file_save_draft_area_files($draftitemid, $context->id, 'mod_turnitintooltwo', 'submissions',
                   $this->id, $uploadoptions);

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_turnitintooltwo', 'submissions', $this->id, "timecreated", false);

        // This should only return 1 result.
        $return = array();
        if (count($files) == 0) {
            $return['result'] = false;
            $return['message'] = get_string('submissionfileerror', 'turnitintooltwo');
        } else {
            $return['result'] = true;
        }

        return $return;
    }

    /**
     * Copy text from submission to a local temporary file for submitting to Turnitin
     *
     * @global type $USER
     * @param object $cm the course module object
     * @param object $post data to use for file submission
     * @return boolean
     */
    public function prepare_text_submission($cm, $post) {
        global $USER;

        $context = context_module::instance($cm->id);

        // Prepare file record object.
        $fileinfo = array(
            'contextid' => $context->id,
            'userid' => $USER->id,
            'component' => 'mod_turnitintooltwo',
            'filearea' => 'submissions',
            'itemid' => $this->id,
            'filepath' => '/',
            'filename' => $this->userid."_".$this->turnitintooltwoid."_".$this->submission_part."_".time().'.txt');

        // Create file containing text.
        $fs = get_file_storage();
        $fs->create_file_from_string($fileinfo, $post["submissiontext"]);

        return true;
    }

    /**
     * Delete submission from Moodle and Turnitin
     *
     * @global type $DB
     * @param int $submission_id
     * @return type
     */
    public function delete_submission($action = 'delete') {
        global $CFG, $DB;
        $notice = array();
        $partid = $this->submission_part;

        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $turnitintooltwoassignment = new turnitintooltwo_assignment($this->turnitintooltwoid);
        $cm = get_coursemodule_from_instance("turnitintooltwo", $turnitintooltwoassignment->turnitintooltwo->id,
            $turnitintooltwoassignment->turnitintooltwo->course);

        $istutor = has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id));

        // Delete Moodle submission first.
        if (!$DB->delete_records('turnitintooltwo_submissions', array('id' => $this->id))) {
            $notice["type"] = "danger";
            $notice["message"] = get_string('submissiondeleteerror', 'turnitintooltwo');
            return $notice;
        }

        // Log deleted submission with Moodle.
        if ($action == 'delete') {
            turnitintooltwo_add_to_log(
                $turnitintooltwoassignment->turnitintooltwo->course,
                "delete submission",
                'view.php?id='.$cm->id,
                get_string('deletesubmissiondesc', 'turnitintooltwo') . " '$this->submission_title'",
                $cm->id,
                $this->userid
            );
        }

        // Update grade in Gradecenter.
        $grades = new stdClass();

        // Only add to gradebook if author has been unanonymised or assignment doesn't have anonymous marking.
        if ($submissions = $DB->get_records('turnitintooltwo_submissions',
                                    array('turnitintooltwoid' => $turnitintooltwoassignment->turnitintooltwo->id,
                                                    'userid' => $this->userid, 'submission_unanon' => 1))) {
            $overallgrade = $turnitintooltwoassignment->get_overall_grade($submissions);
            if ($turnitintooltwoassignment->turnitintooltwo->grade < 0) {
                // Using a scale.
                $grades->rawgrade = ($overallgrade == '--') ? null : $overallgrade;
            } else {
                $grades->rawgrade = ($overallgrade == '--') ? null : number_format($overallgrade, 2);
            }

        } else {
            $grades->rawgrade = null;
        }
        $grades->userid = $this->userid;
        $params['idnumber'] = $cm->idnumber;

        // If this is the only submission and anon marking is being used then unlock this part.
        $numpartsubs = $DB->count_records('turnitintooltwo_submissions',
                                            array('submission_part' => $this->submission_part));

        if ($numpartsubs == 0) {
            $unlockpart = new stdClass();
            $unlockpart->id = $this->submission_part;
            $unlockpart->unanon = 0;
            $DB->update_record('turnitintooltwo_parts', $unlockpart);
        }

        @include_once($CFG->libdir."/gradelib.php");
        grade_update('mod/turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo->course, 'mod',
                        'turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo->id, 0, $grades, $params);

        // If we have a Turnitin Id then delete submission.
        if ((!empty($this->submission_objectid)) && ($istutor) && $action == 'delete') {
            $submission = new TiiSubmission();
            $submission->setSubmissionId($this->submission_objectid);

            try {
                $response = $turnitincall->deleteSubmission($submission);

                turnitintooltwo_add_to_log(
                    $turnitintooltwoassignment->turnitintooltwo->course,
                    "delete submission",
                    'view.php?id='.$cm->id,
                    get_string('deletesubmissiontiidesc', 'turnitintooltwo') . " '$this->submission_title'",
                    $cm->id,
                    $this->userid
                );

                $notice["type"] = "danger";
                $notice["message"] = get_string('submissiondeleted', 'turnitintooltwo').
                                        ' ('.get_string('turnitinid', 'turnitintooltwo').
                                            ': '.$this->submission_objectid.')';

                // If we have no submissions to this part then reset submitted and unanon flag.
                $numsubs = count($DB->get_records('turnitintooltwo_submissions',
                                            array('submission_part' => $partid), 'id'));

                return $notice;
            } catch (Exception $e) {
                $turnitincomms->handle_exceptions($e, 'turnitindeletionerror');
            }
        }
    }

    /**
     * Make a nothing submission to Turnitin (marking template)
     *
     * @global type $DB
     * @param object $cm the course module object
     * @param object $$turnitintooltwoassignment the turnitintooltwossignment object
     *
     * @return integer $submissionid the submissions id for the submission
     */
    public function do_tii_nothing_submission($cm, $turnitintooltwoassignment, $partid, $userid) {
        global $DB, $USER;

        // Check if user is a member of class, if not then join them to it.
        $coursetype = turnitintooltwo_get_course_type($turnitintooltwoassignment->turnitintooltwo->legacy);
        $course = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);
        $user = new turnitintooltwo_user($userid, 'Learner');
        $user->join_user_to_class($course->turnitin_cid);
        $user->edit_tii_user();

        $part = $turnitintooltwoassignment->get_part_details($partid);

        // Create Submission object to send to Turnitin.
        $newsubmission = new TiiSubmission();
        $newsubmission->setAssignmentId($part->tiiassignid);
        $newsubmission->setAuthorUserId($user->tiiuserid);
        $instructor = new turnitintooltwo_user($USER->id, 'Instructor');
        $instructor->edit_tii_user();

        $newsubmission->setSubmitterUserId($instructor->tiiuserid);
        $newsubmission->setRole('Instructor');

        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        try {
            $response = $turnitincall->createNothingSubmission($newsubmission);
            $newsubmission = $response->getSubmission();

            $submission = new stdClass();
            $submission->userid = $userid;
            $submission->turnitintooltwoid = $turnitintooltwoassignment->turnitintooltwo->id;
            $submission->submission_part = $partid;
            $submission->submission_title = get_string('gradingtemplate', 'turnitintooltwo');
            $submission->submission_type = 1; // Always a file type.
            $submission->submission_objectid = $newsubmission->getSubmissionId();
            $submission->submission_unanon = 0;
            $submission->submission_grade = null;
            $submission->submission_gmimaged = 0;
            $submission->submission_acceptnothing = 1;
            $submission->submission_orcapable = 0;
            $submission->submission_hash = $submission->userid.'_'.$submission->turnitintooltwoid.'_'.$submission->submission_part;

            // Add entry to log.
            turnitintooltwo_add_to_log($turnitintooltwoassignment->turnitintooltwo->course, "add submission",
                    'view.php?id='.$cm->id, get_string('gradenosubmission', 'turnitintooltwo') . ": $userid", $cm->id, $userid);

            if (!$this->id = $DB->insert_record('turnitintooltwo_submissions', $submission)) {
                return get_string('submissionupdateerror', 'turnitintooltwo');
            } else {
                $assignment = new stdClass();
                $assignment->id = $turnitintooltwoassignment->turnitintooltwo->id;
                $assignment->submitted = 1;
                $DB->update_record('turnitintooltwo', $assignment);

                $partdata = new stdClass();
                $partdata->id = $partid;
                $partdata->submitted = 1;

                // Disable anonymous marking if post date has passed.
                if ($part->dtpost <= time()) {
                    $partdata->unanon = 1;
                }

                $DB->update_record('turnitintooltwo_parts', $partdata);

                return array( "submission_id" => $newsubmission->getSubmissionId() );
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $data;
    }

    /**
     * Make submission to Turnitin
     *
     * @global type $DB
     * @param object $cm the course module object
     * @return string $message to display to user
     */
    public function do_tii_submission($cm, $turnitintooltwoassignment) {
        global $DB, $USER;

        $config = turnitintooltwo_admin_config();
        $notice = array("success" => false);
        $context = context_module::instance($cm->id);

        // Check if user is a member of class, if not then join them to it.
        $coursetype = turnitintooltwo_get_course_type($turnitintooltwoassignment->turnitintooltwo->legacy);
        $course = $turnitintooltwoassignment->get_course_data($turnitintooltwoassignment->turnitintooltwo->course, $coursetype);
        $user = new turnitintooltwo_user($this->userid, 'Learner');
        $user->join_user_to_class($course->turnitin_cid);
        $user->edit_tii_user();

        // Get the stored file and read it into a temp file for submitting to Turnitin.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_turnitintooltwo', 'submissions', $this->id, "timecreated", false);
        $tempfile = "";

        foreach ($files as $file) {

            $filename = array(
                $this->submission_title,
                $cm->id
            );

            if ( !$turnitintooltwoassignment->turnitintooltwo->anon && empty($config->enablepseudo) ) {
                $userdetails = array(
                    $this->userid,
                    $user->firstname,
                    $user->lastname
                );

                $filename = array_merge($userdetails, $filename);
            }

            $suffix = $file->get_filename();

            $tempfile = turnitintooltwo_tempfile($filename, $suffix);

            $fh = fopen($tempfile, "w");
            fwrite($fh, $file->get_content());
            fclose($fh);
        }

        if (!empty($tempfile)) {
            $part = $turnitintooltwoassignment->get_part_details($this->submission_part);

            // Initialise Comms Object.
            $turnitincomms = new turnitintooltwo_comms();
            $turnitincall = $turnitincomms->initialise_api();

            // Create Submission object to send to Turnitin.
            $newsubmission = new TiiSubmission();
            $newsubmission->setAssignmentId($part->tiiassignid);
            if (!is_null($this->submission_objectid)) {
                $newsubmission->setSubmissionId($this->submission_objectid);
                $apimethod = "replaceSubmission";
            } else {
                $apimethod = "createSubmission";
            }
            $newsubmission->setTitle($this->submission_title);
            $newsubmission->setAuthorUserId($user->tiiuserid);
            if ($user->id == $USER->id) {
                $newsubmission->setSubmitterUserId($user->tiiuserid);
                $newsubmission->setRole('Learner');
            } else {
                $instructor = new turnitintooltwo_user($USER->id, 'Instructor');
                $instructor->edit_tii_user();

                $newsubmission->setSubmitterUserId($instructor->tiiuserid);
                $newsubmission->setRole('Instructor');
            }

            $newsubmission->setSubmissionDataPath($tempfile);

            try {
                $response = $turnitincall->$apimethod($newsubmission);
                $newsubmission = $response->getSubmission();

                // Save the submission.
                $submission = new stdClass();
                $submission->id = $this->id;
                $submission->userid = $this->userid;
                $submission->turnitintooltwoid = $this->turnitintooltwoid;
                $submission->submission_part = $this->submission_part;
                $submission->submission_title = $this->submission_title;
                $submission->submission_type = $this->submission_type;
                $submission->submission_filename = $this->submission_filename;
                $submission->submission_objectid = $newsubmission->getSubmissionId();
                $submission->submission_score = $this->submission_score;
                $submission->submission_grade = $this->submission_grade;
                $submission->submission_gmimaged = $this->submission_gmimaged;
                $submission->submission_attempts = $this->submission_attempts;
                $submission->submission_modified = time();
                $submission->submission_nmuserid = $this->submission_nmuserid;
                $submission->submission_nmfirstname = $this->submission_nmfirstname;
                $submission->submission_nmlastname = $this->submission_nmlastname;
                $submission->submission_unanon = $this->submission_unanon;
                $submission->submission_unanonreason = $this->submission_unanonreason;
                $submission->submission_transmatch = $this->submission_transmatch;
                $submission->submission_acceptnothing = 0;
                $submission->submission_hash = $submission->userid.'_'.$submission->turnitintooltwoid.'_'.$submission->submission_part;

                $DB->update_record('turnitintooltwo_submissions', $submission);

                // Delete the tempfile.
                if (!is_null($tempfile)) {
                    unlink($tempfile);
                }

                $notice["success"] = true;
                $notice["message"] = get_string('submissionuploadsuccess', 'turnitintooltwo');
                $notice["extract"] = htmlspecialchars($newsubmission->getTextExtract());
                $notice["tii_submission_id"] = $submission->submission_objectid;

                // Send a message to the user's Moodle inbox with the digital receipt.
                $input = array(
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'submission_title' => $this->submission_title,
                    'assignment_name' => $turnitintooltwoassignment->turnitintooltwo->name,
                    'assignment_part' => $turnitintooltwoassignment->get_part_details($this->submission_part)->partname,
                    'course_fullname' => $course->fullname,
                    'submission_date' => date('d-M-Y h:iA'),
                    'submission_id' => $submission->submission_objectid
                );

                // Student digital receipt.
                $message = $this->receipt->build_message($input);
                $this->receipt->send_message($this->userid, $message, $course->id);

                // Instructor digital receipt.
                $this->submission_instructors = get_enrolled_users($context, 'mod/turnitintooltwo:grade', 0, 'u.id');
                if (!empty($this->submission_instructors)) {
                    $message = $this->instructor_receipt->build_instructor_message($input);
                    $this->instructor_receipt->send_instructor_message($this->submission_instructors, $message, $course->id);
                }

                // Create a log entry for submission going to Turnitin.
                $logstring = ($apimethod == "replaceSubmission") ? 'addresubmissiontiidesc' : 'addsubmissiontiidesc';

                turnitintooltwo_add_to_log(
                    $turnitintooltwoassignment->turnitintooltwo->course,
                    "add submission",
                    'view.php?id='.$cm->id,
                    get_string($logstring, 'turnitintooltwo') . " '" . $this->submission_title . "'",
                    $cm->id,
                    $user->id
                );

                // Add to activity log.
                $logstring = "Action: Submission | Id: ".$this->turnitintooltwoid." | Part: ".$submission->submission_part;
                $logstring .= " | User ID: ".$user->id." (".$user->tiiuserid.") Submission title: ".$submission->submission_title;
                turnitintooltwo_activitylog($logstring, "REQUEST");
            } catch (Exception $e) {
                $errorstring = (!is_null($this->submission_objectid)) ? "updatesubmissionerror" : "createsubmissionerror";
                $error = $turnitincomms->handle_exceptions($e, $errorstring, false, true);

                $notice["message"] = $error;
                $notice["success"] = false;
            }
        } else {
            $notice["success"] = false;
            $notice["message"] = get_string('emptycreatedfile', 'turnitintooltwo');
        }

        return $notice;
    }

    /**
     * Update and save an individual submission from Turnitin
     *
     * @param type $save - save in db regardless of changes
     */
    public function update_submission_from_tii($save = false) {
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $submission = new TiiSubmission();
        $submission->setSubmissionId($this->submission_objectid);

        try {
            $response = $turnitincall->readSubmission($submission);
            $readsubmission = $response->getSubmission();

            $this->save_updated_submission_data($readsubmission, false, $save);
            $this->get_submission_details();
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'tiisubmissiongeterror', false);
        }
    }

    /**
     * Save updated submission data from Turnitin to the database
     *
     * @global type $DB
     * @param type $tiisubmissiondata
     * @param type $bulk
     * @param type $save - save in db regardless of changes
     * @return type
     */
    public function save_updated_submission_data($tiisubmissiondata, $bulk = false, $save = false) {
        global $DB;

        static $part;
        static $tiiassignid;
        if ($tiiassignid != $tiisubmissiondata->getAssignmentId() || empty($part)) {
            $part = $DB->get_record("turnitintooltwo_parts", array("tiiassignid" => $tiisubmissiondata->getAssignmentId()));
        }
        $turnitintooltwoassignment = new turnitintooltwo_assignment($part->turnitintooltwoid);

        $sub = new stdClass();
        $sub->submission_title = $tiisubmissiondata->getTitle();
        $sub->submission_part = $part->id;
        $sub->submission_objectid = $tiisubmissiondata->getSubmissionId();
        $sub->turnitintooltwoid = $turnitintooltwoassignment->turnitintooltwo->id;

        if (is_numeric($tiisubmissiondata->getOverallSimilarity())) {
            $sub->submission_score = $tiisubmissiondata->getOverallSimilarity();
        } else {
            $sub->submission_score = null;
        }
        $sub->submission_transmatch = 0;

        if ($turnitintooltwoassignment->turnitintooltwo->transmatch == 1 &&
            is_int($tiisubmissiondata->getTranslatedOverallSimilarity())) {
            if (($tiisubmissiondata->getTranslatedOverallSimilarity() > $tiisubmissiondata->getOverallSimilarity())) {
                $sub->submission_score = $tiisubmissiondata->getTranslatedOverallSimilarity();
                $sub->submission_transmatch = 1;
            }
        }

        $sub->submission_grade = ($tiisubmissiondata->getGrade() == '') ? null : $tiisubmissiondata->getGrade();
        $sub->submission_gmimaged = $tiisubmissiondata->getFeedbackExists();
        $sub->submission_unanon = ($tiisubmissiondata->getAnonymous() == 1) ? 0 : 1;
        $sub->submission_orcapable = ($tiisubmissiondata->getOriginalityReportCapable() == 1) ? 1 : 0;
        $sub->submission_acceptnothing = ($tiisubmissiondata->getAcceptNothingSubmission() == 1) ? 1 : 0;

        if ($sub->submission_unanon == 1) {
            $sub->submission_unanonreason = urldecode($tiisubmissiondata->getAnonymousRevealReason());
        } else {
            $sub->submission_unanonreason = null;
        }

        $sub->submission_modified = strtotime($tiisubmissiondata->getDate());
        $sub->translated_overall_similarity = $tiisubmissiondata->getTranslatedOverallSimilarity();
        if ($tiisubmissiondata->getAuthorLastViewedFeedback() > 0) {
            $sub->submission_attempts = strtotime($tiisubmissiondata->getAuthorLastViewedFeedback());
        } else {
            $sub->submission_attempts = 0;
        }

        // If save not passed in then only update if certain items have changed to save on database load.
        if ($this->submission_grade != $sub->submission_grade || $this->submission_score != $sub->submission_score ||
            $this->submission_modified != $sub->submission_modified || $this->submission_attempts != $sub->submission_attempts ||
            $this->submission_unanon != $sub->submission_unanon || $this->submission_part != $sub->submission_part ||
            $this->submission_gmimaged != $sub->submission_gmimaged || $this->submission_objectid != $sub->submission_objectid) {
            $save = true;
        }

        if ($save) {
            // If the user is not a moodle user then get their name from Tii - only do this on initial save.
            $sub->userid = turnitintooltwo_user::get_moodle_user_id($tiisubmissiondata->getAuthorUserId());

            // If we have no user ID get it from the Moodle database by using their Turnitin e-mail address.
            if ($sub->userid == 0) {
                $tmpuser = new turnitintooltwo_user(0);
                $tmpuser->tiiuserid = $tiisubmissiondata->getAuthorUserId();
                $tiiuser = $tmpuser->set_user_values_from_tii();
                if ($userrecord = $DB->get_record('user', array('email' => $tiiuser["email"]))) {
                    $sub->userid = $userrecord->id;
                }
            }

            if ($sub->userid == 0 && empty($this->id)) {
                if ($tiisubmissiondata->getAuthorUserId() > 0) {
                    $sub->submission_nmuserid = $tiisubmissiondata->getAuthorUserId();
                    $tmpuser = new turnitintooltwo_user(0);
                    $tmpuser->tiiuserid = $tiisubmissiondata->getAuthorUserId();
                    $tiiuser = $tmpuser->set_user_values_from_tii();

                    $sub->submission_nmfirstname = $tiiuser["firstname"];
                    $sub->submission_nmlastname = $tiiuser["lastname"];
                } else {
                    $sub->submission_nmuserid = "nm-".$tiisubmissiondata->getAuthorUserId();
                    $sub->submission_nmfirstname = '';
                    $sub->submission_nmlastname = get_string('nonmoodleuser', 'turnitintooltwo');
                }
            }

            // Create our submission hash to prevent duplication.
            $sub->submission_hash = $sub->userid.'_'.$sub->turnitintooltwoid.'_'.$sub->submission_part;
            // Check submission hash doesn't exist already
            $checksub = $DB->get_record('turnitintooltwo_submissions',
                                            array("submission_hash" => $sub->submission_hash), 'id', IGNORE_MULTIPLE);

            if ($checksub) {
                $this->id = $checksub->id;
            }

            if (!empty($this->id)) {
                $sub->id = $this->id;
                $DB->update_record("turnitintooltwo_submissions", $sub, $bulk);
            } else {
                $sub->id = $DB->insert_record("turnitintooltwo_submissions", $sub, true, $bulk);
            }

            // Update the Moodle gradebook.
            $this->update_gradebook($sub, $turnitintooltwoassignment);
        }
    }

    /**
     * Update the Moodle gradebook.
     *
     * @param type $sub
     * @return type $turnitintooltwoassignment
     */
    public function update_gradebook($sub, $turnitintooltwoassignment) {
        global $DB, $CFG;

        // Update gradebook.
        @include_once($CFG->libdir."/gradelib.php");
        if ($sub->userid > 0 && $sub->submission_unanon) {
            $user = new turnitintooltwo_user($sub->userid, "Learner");
            $cm = get_coursemodule_from_instance("turnitintooltwo", $turnitintooltwoassignment->turnitintooltwo->id,
                                                            $turnitintooltwoassignment->turnitintooltwo->course);
            $grades = new stdClass();

            // Only add to gradebook if author has been unanonymised or assignment doesn't have anonymous marking.
            if ($submissions = $DB->get_records('turnitintooltwo_submissions',
                                            array('turnitintooltwoid' => $turnitintooltwoassignment->turnitintooltwo->id,
                                                        'userid' => $user->id, 'submission_unanon' => 1))) {
                $overallgrade = $turnitintooltwoassignment->get_overall_grade($submissions);
                if ($turnitintooltwoassignment->turnitintooltwo->grade < 0) {
                    // Using a scale.
                    $grades->rawgrade = ($overallgrade == '--') ? null : $overallgrade;
                } else {
                    $grades->rawgrade = ($overallgrade == '--') ? null : number_format($overallgrade, 2);
                }

            }
            $grades->userid = $user->id;
            $params['idnumber'] = $cm->idnumber;

            grade_update('mod/turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo->course, 'mod',
                            'turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo->id, 0, $grades, $params);
        }
    }

    /**
     * Edit the submission in Turnitin so a tutor can see the identity of the student
     *
     * @param string $reason
     * @return boolean
     */
    public function unanonymise_submission($reason) {
        global $USER;

        // Get user and part details.
        $turnitintooltwoassignment = new turnitintooltwo_assignment($this->turnitintooltwoid);
        $partdetails = $turnitintooltwoassignment->get_part_details($this->submission_part);
        $user = new turnitintooltwo_user($USER->id);

        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $submission = new TiiSubmission();
        $submission->setSubmissionId($this->submission_objectid);
        $submission->setAssignmentId($partdetails->tiiassignid);
        $reason = urldecode($reason);
        if (strlen($reason) < 5) {
            $reason = "No specified reason: ".$reason;
        }
        $submission->setAnonymousRevealReason($reason);
        $submission->setAnonymousRevealUser($user->tiiuserid);
        $submission->setAnonymousRevealDateTime(date("c"));

        try {
            $turnitincall->updateSubmission($submission);
            return true;
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, "unanonymiseerror", false);
            return false;
        }
    }

    /**
     * Checking if grade exists for assignment
     *
     * @param string $turnitintooltwoid
     * @return int
     */
    public function count_graded_submissions($turnitintooltwoid) {
        global $DB;
        return $DB->count_records_select("turnitintooltwo_submissions", "turnitintooltwoid = :turnitintooltwoid AND submission_grade > 0",
            array("turnitintooltwoid" => $turnitintooltwoid));
    }
}
