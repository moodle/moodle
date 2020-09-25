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

namespace local_iomad_track;

defined('MOODLE_INTERNAL') || die();

// In case we ever want to switch back to ordinary certificates
define('CERTIFICATE', 'iomadcertificate');

require_once($CFG->dirroot . '/mod/' . CERTIFICATE . '/lib.php');
require_once($CFG->dirroot . '/mod/' . CERTIFICATE . '/locallib.php');

class observer {

    /**
     * Get certificate modules
     * @param int courseid
     * @return array of certificate modules
     */
    private static function get_certificatemods($courseid) {
        global $DB;

        $mods = $DB->get_records(CERTIFICATE, array('course' => $courseid));

        return $mods;
    }

    /**
     * Create a new certificate using certificate module template
     * @param object $certificate certificate instance
     * @param object $user completing user
     * @param object $cm course module (in completing course)
     * @param object $course completing course
     * @param object $certissue certificate issue instance
     * @return string pdf content
     */
    private static function create_certificate($certificate, $user, $cm, $course, $certissue) {
        global $CFG;

        // load pdf library
        require_once("$CFG->libdir/pdflib.php");

        // some name changes (as used in cert template)
        $certuser = $user;
        $certificate_name = CERTIFICATE;
        $$certificate_name = $certificate;
        $certrecord = $certissue;

        // Load certificate template (magically creates $pdf variable. Grrrrrr)
        // Assumes a whole bunch of stuff exists without being explicitly required (double grrrrr)
        $typefield = CERTIFICATE . 'type';
        require("$CFG->dirroot/mod/" . CERTIFICATE . "/type/{$certificate->$typefield}/certificate.php");

        // Create the certificate content. 'S' means return as string
        return $pdf->Output('', 'S');
    }

    /**
     * Store the certificate in file area for local_iomad_track
     * Note: if there is more than one ceritificate in the same course, we rely on them having
     * different names (which they should).
     * @param int $contextid Context (id) of completed course
     * @param string $filename Filename of original certificate issue
     * @param int $trackid id of completion in local_iomad_track table
     * @param string $content the pdf data
     */
    private static function store_certificate($contextid, $filename, $trackid, $certificate, $content) {

        $fs = get_file_storage();

        // Prepare file record object
        $component = 'local_iomad_track';
        $filearea = 'issue';
        $filepath = '/';

        $fileinfo = array(
            'contextid' => $contextid,
            'component' => $component,
            'filearea' => $filearea,
            'itemid' => $trackid,
            'filepath' => $filepath,
            'filename' => $filename,
        );
        $fs->create_file_from_string($fileinfo, $content);
    }

    /**
     * Record certificate in db table
     * @param int $trackid id in local_iomad_track table
     * @param string $filename of certificate
     */
    private static function save_certificate($trackid, $filename) {
        global $DB;

        $trackcert = new \stdClass();
        $trackcert->trackid = $trackid;
        $trackcert->filename = $filename;
        $DB->insert_record('local_iomad_track_certs', $trackcert);
    }

    /**
     * Process (any) certificates in the course
     */
    private static function record_certificates($courseid, $userid, $trackid) {
        global $DB;

        // Get course.
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        // Get context
        $context = \context_course::instance($courseid);

        // Get user
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        // Get the certificate activities in given course
        if (!$certificates = self::get_certificatemods($courseid)) {
            return false;
        }

        // Iterate over to find certs for given user
        foreach ($certificates as $certificate) {

            // $cm contains checks for conditional activities et al
            $cm = get_coursemodule_from_instance(CERTIFICATE, $certificate->id, $courseid);
            $modinfo = get_fast_modinfo($course);
            $cm = $modinfo->get_cm($cm->id);

            // Uservisible determines if the user would have been able to access the certificate.
            // If they can't see it (e.g. did not meet its completion requirements) then skip
            if (!$cm->uservisible) {
                continue;
            }

            // Find certificate issue record or create it (in cert lib.php)
            $certissue_function = CERTIFICATE . '_get_issue';
            $certissue = $certissue_function($course, $user, $certificate, $cm);

            // Generate correct filename (same as certificate mod's view.php does)
            $certname = rtrim($certificate->name, '.');
            $filename = clean_filename(format_string($certname) . ".pdf");

            // Create the certificate content (always create new so it's up to date)
            $content = self::create_certificate($certificate, $user, $cm, $course, $certissue);

            // Store certificate
            self::store_certificate($context->id, $filename, $trackid, $certificate, $content);

            // Record all of above in local db table
            self::save_certificate($trackid, $filename);

            // Debugging
            mtrace('local_iomad_track: certificate recorded for ' . $user->username . ' in course ' . $courseid . ' filename "' . $filename . '"');
        }
    }

    /**
     * Consume course_completed event
     * @param object $event the event object
     */
    public static function course_completed($event) {
        global $DB;

        // Get the relevant event date (course_completed event).
        $data = $event->get_data();
        $userid = $data['relateduserid'];
        $courseid = $data['courseid'];

        // Get the full completion information.
        $comprec = $DB->get_record('course_completions', array('userid' => $userid,
                                                               'course' => $courseid));

        // Does this course have a valid length?
        $offset = 0;
        if ($iomadrec = $DB->get_record('iomad_courses', array('courseid' => $courseid))) {
            if ($iomadrec->validlength > 0) {
                $offset = $iomadrec->validlength * 24 * 60 * 60;
            }
        }

        // Get the enrolment record as sometime the completion record isn't fully formed after a completion reset.
        if (!$enrolrec = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                         JOIN {enrol} e ON (ue.enrolid = e.id)
                                         WHERE ue.userid = :userid
                                         AND e.courseid = :courseid
                                         AND e.status = 0",
                                         array('userid' => $userid,
                                               'courseid' => $courseid))) {
            // User isn't enrolled. Not sure why we got this.
            return true;
        }

        // Is this a duplicate event?
        if (!empty($enrolrec->timestart) &&
             $DB->get_record_sql("SELECT id FROM {local_iomad_track}
                                 WHERE userid = :userid
                                 AND courseid = :courseid
                                 AND timeenrolled = :timeenrolled
                                 AND timecompleted IS NOT NULL",
                                 array('userid' => $userid, 'courseid' => $courseid, 'timeenrolled' => $enrolrec->timestart))) {

            // It is so we don't record it.
            return true;
        }

        // Get the final grade for the course.
        if ($graderec = $DB->get_record_sql("SELECT gg.* FROM {grade_grades} gg
                                         JOIN {grade_items} gi ON (gg.itemid = gi.id
                                                                   AND gi.itemtype = 'course'
                                                                   AND gi.courseid = :courseid)
                                         WHERE gg.userid = :userid", array('courseid' => $courseid,
                                                                           'userid' => $userid))) {
            $finalgrade = $graderec->finalgrade;
        } else {
            $finalgrade = 0;
        }

        // Is the record broken?
        $broken = false;
        if (empty($comprec->timeenrolled)) {
            $broken = true;
            $comprec->timeenrolled = $enrolrec->timestart;
        }

        if (empty($comprec->timestarted)) {
            $broken = true;
            $comprec->timestarted = $enrolrec->timestart;
        }

        if ($broken) {
            // Update the completion record.
            $DB->update_record('course_completions', $comprec);
        }

        if (!$current = $DB->get_record('local_iomad_track', array('courseid' => $courseid, 'userid' => $userid, 'timecompleted' => null))) {
            // For some reason we don't already have a record.
            // Get the rest of the data.
            $usercompany = \company::by_userid($userid);
            $companyrec = $DB->get_record('company', array('id' => $usercompany->id));
            $userrec = $DB->get_record('user', array('id' => $userid));
            $department = $DB->get_record_sql("SELECT d.* FROM {department} d JOIN {company_users} cu ON (d.id = cu.departmentid) WHERE cu.userid = :userid AND cu.companyid = :companyid", array('userid' => $userid, 'companyid' => $companyrec->id));
            $courserec = $DB->get_record('course', array('id' => $courseid));
            if ($DB->get_record('iomad_courses', array('courseid' => $courseid, 'licensed' => 1))) {
                // Its a licensed course, get the last license.
                $licenserecs = $DB->get_records_sql("SELECT * FROM {companylicense_users}
                                                     WHERE userid = :userid AND licensecourseid = :licensecourseid AND issuedate < :issuedate
                                                     AND licenseid IN (SELECT id from {companylicense} WHERE companyid = :companyid)
                                                     ORDER BY issuedate DESC",
                                                     array('licensecourseid' => $courseid, 'userid' => $userid, 'companyid' => $companyrec->id, 'issuedate' => $comprec->timecompleted),
                                                     0,1);
                $licenserec = array_pop($licenserecs);
                if ($license = $DB->get_record('companylicense', array('id' => $licenserec->licenseid))) {
                    $licenseid = $license->id;
                    $licensename = $license->name;
                } else {
                    $licenseid = 0;
                    $licensename = '';
                }
            } else {
                $licenseid = 0;
                $licensename = '';
            }

            // Record the completion event.
            $completion = new \StdClass();
            $completion->courseid = $courseid;
            $completion->userid = $userid;
            $completion->timeenrolled = $enrolrec->timestart;
            $completion->timestarted = $comprec->timestarted;
            $completion->timecompleted = $comprec->timecompleted;
            if (!empty($graderec->finalgrade)) {
                $completion->finalscore = $graderec->finalgrade;
            } else {
                $completion->finalscore = 0;
            }
            $completion->coursename = $courserec->fullname;
            $completion->companyid = $companyrec->id;
            $completion->companyname = $companyrec->name;
            $completion->departmentid = $department->id;
            $completion->departmentname = $department->name;
            $completion->firstname = $userrec->firstname;
            $completion->lastname = $userrec->lastname;
            $completion->licenseid = $licenseid;
            $completion->licensename = $licensename;
            $completion->modifiedtime = time();

            // Deal with completion valid length.
            if (!empty($offset)) {
                $completion->timeexpires = $completion->timecompleted + $offset;
            }

            $trackid = $DB->insert_record('local_iomad_track', $completion);
        } else {
            $current->timecompleted = $comprec->timecompleted;
            if (!empty($graderec->finalgrade)) {
                $current->finalscore = $graderec->finalgrade;
            } else {
                $current->finalscore = 0;
            }
            $broken = false;
            if (empty($current->timeenrolled)) {
                if (empty($comprec->timeenrolled)) {
                    $broken = true;
                    // Need to get it from the enrolment record.
                    $enrolrec = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                     JOIN {enrol} e ON (ue.enrolid = e.id)
                                                     WHERE ue.userid = :userid
                                                     AND e.courseid = :courseid
                                                     AND e.status = 0",
                                                     array('userid' => $userid,
                                                           'courseid' => $courseid));
                    $comprec->timeenrolled = $enrolrec->starttime;
                }
                $current->timeenrolled = $comprec->timeenrolled;
            }

            if (empty($current->timestarted)) {
                if (empty($comprec->timestarted)) {
                    $broken = true;
                    if (empty($enrolrec)) {
                        // Need to get it from the enrolment record.
                        $enrolrec = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                         JOIN {enrol} e ON (ue.enrolid = e.id)
                                                         WHERE ue.userid = :userid
                                                         AND e.courseid = :courseid
                                                         AND e.status = 0",
                                                         array('userid' => $userid,
                                                               'courseid' => $courseid));
                    }
                    $comprec->timestarted = $enrolrec->starttime;
                }
                $current->timestarted = $comprec->timestarted;
            }

            if ($broken) {
                // Update the completion record.
                $DB->update_record('course_completions', $comprec);
            }

            // Deal with completion valid length.
            if (!empty($offset)) {
                $current->timeexpires = $current->timecompleted + $offset;
            }

            $current->modifiedtime = time();
            $DB->update_record('local_iomad_track', $current);
            $trackid = $current->id;
        }

        // Debug
        if (!PHPUNIT_TEST) {
            mtrace('Iomad completion recorded for userid ' . $userid . ' in courseid ' . $courseid);
        }

        self::record_certificates($courseid, $userid, $trackid);

        return true;
    }

    /**
     * Consume course updated event
     * @param object $event the event object
     */
    public static function course_updated($event) {
        global $DB;

        $courseid = $event->courseid;
        $modifiedtime = $event->timecreated;

        if ($courserec = $DB->get_record('course', array('id' => $courseid))) {
            $entries = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                             WHERE courseid = :courseid
                                             AND coursename != :coursename",
                                             array('courseid' => $courseid,
                                                   'coursename' => $courserec->fullname));
            foreach ($entries as $entry) {
                $DB->set_field('local_iomad_track', 'coursename', $courserec->fullname, array('id' => $entry->id));
                $DB->set_field('local_iomad_track', 'modifiedtime', $modifiedtime, array('id' => $entry->id));
            }
        }

        return true;
    }

    /**
     * Consume course updated event
     * @param object $event the event object
     */
    public static function company_license_updated($event) {
        global $DB;

        $licenseid = $event->other['licenseid'];
        $modifiedtime = $event->timecreated;

        if ($licenserec = $DB->get_record('companylicense', array('id' => $licenseid))) {
            $entries = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                             WHERE licenseid = :licenseid
                                             AND licensename != :licensename",
                                             array('licenseid' => $licenseid,
                                                   'licensename' => $licenserec->name));
            foreach ($entries as $entry) {
                $DB->set_field('local_iomad_track', 'licensename', $licenserec->name, array('id' => $entry->id));
                $DB->set_field('local_iomad_track', 'modifiedtime', $modifiedtime, array('id' => $entry->id));
            }
        }

        return true;
    }

    /**
     * Consume user license assigned event
     * @param object $event the event object
     */
    public static function user_license_assigned($event) {
        global $DB;

        $userid = $event->userid;
        $courseid = $event->courseid;
        $licenseid = $event->other['licenseid'];
        $issuedate = $event->other['issuedate'];
        $modifiedtime = $event->timecreated;
        $expirysent = null;
        $notstartedstop = 0;
        $completedstop = 0;
        $expiredstop = 0;

        // Check if there is already an entry for this.
        if ($entry = $DB->get_record('local_iomad_track', array('userid' => $userid,
                                                                'courseid' => $courseid,
                                                                'licenseid' => $licenseid,
                                                                'timecompleted' => null))) {
            $licenserec = $DB->get_record('companylicense', array('id' => $licenseid));

            // Is this an educator license?
            if ($licenserec->type == 2 || $licenserec->type == 3) {
                $entry->expirysent = $modifiedtime;
                $entry->notstartedstop = 1;
                $entry->completedstop = 1;
                $entry->expiredstop = 1;
            }

            // We already have an entry.  Change the issue time.
            $entry->licenseallocated = $issuedate;
            $entry->modifiedtime = time();
            $DB->update_record('local_iomad_track', $entry);
        } else {
            // Create one.
            if ($courserec = $DB->get_record('course', array('id' => $courseid))) {
                $licenserec = $DB->get_record('companylicense', array('id' => $licenseid));

                // Is this an educator license?
                if ($licenserec->type == 2 || $licenserec->type == 3) {
                    $expirysent = $modifiedtime;
                    $notstartedstop = 1;
                    $completedstop = 1;
                    $expiredstop = 1;
                }
                $entry = array('userid' => $userid,
                               'courseid' => $courseid,
                               'coursename' => $courserec->fullname,
                               'companyid' => $licenserec->companyid,
                               'licenseid' => $licenseid,
                               'licensename' => $licenserec->name,
                               'licenseallocated' => $issuedate,
                               'expirysent' => $expirysent,
                               'notstartedstop' => $notstartedstop,
                               'completedstop' => $completedstop,
                               'expiredstop' => $expiredstop,
                               'modifiedtime' => $modifiedtime
                               );
                $DB->insert_record('local_iomad_track', $entry);
            }
        }

        return true;
    }

    /**
     * Consume user license unassigned event
     * @param object $event the event object
     */
    public static function user_license_unassigned($event) {
        global $DB;
        $userid = $event->userid;
        $courseid = $event->courseid;
        $licenseid = $event->other['licenseid'];

        // Check if there is already an entry for this.
        if ($entry = $DB->get_record('local_iomad_track', array('userid' => $userid,
                                                                'courseid' => $courseid,
                                                                'licenseid' => $licenseid,
                                                                'timeenrolled' => null))) {
            // We already have an entry.  Remove it.
            $DB->delete_records('local_iomad_track', array('id' => $entry->id));
        }

        return true;
    }

    /**
     * Consume user enrolment created event
     * @param object $event the event object
     */
    public static function user_enrolment_created($event) {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;
        $timeenrolled = $event->timecreated;
        $modifiedtime = $event->timecreated;

        // Is this course a license course?
        if ($DB->get_record('iomad_courses', array('courseid' => $courseid, 'licensed' => 1))) {
            // Ignore it we capture a different event for those.
            return true;
        }

        // Check if there is already an entry for this.
        if ($entry = $DB->get_record('local_iomad_track', array('userid' => $userid,
                                                                'courseid' => $courseid,
                                                                'timecompleted' => null))) {
            // We already have an entry.  Change the issue time.
            $entry->timeenrolled = $timeenrolled;
            $entry->modifiedtime = $modifiedtime;
            $DB->update_record('local_iomad_track', $entry);
        } else {
            // Create one.
            if ($courserec = $DB->get_record('course', array('id' => $courseid))) {
                if ($companies = $DB->get_records_sql("SELECT cu.* FROM {company_users} cu
                                                      JOIN {company_course} cc on (cu.companyid = cc.companyid)
                                                      WHERE cu.userid = :userid
                                                      AND cc.courseid = :courseid
                                                      ORDER BY cu.id DESC",
                                                      array('userid' => $userid,
                                                            'courseid' => $courseid))) {

                    // Searching by company and allocated course.
                    $company = array_shift($companies);
                    $companyid = $company->companyid;
                } else if ($companies = $DB->get_records_sql("SELECT cu.* FROM {company_users} cu
                                                      WHERE cu.userid = :userid
                                                      ORDER BY cu.id DESC",
                                                      array('userid' => $userid))) {

                    // Searching by company only as could be open shared course.
                    $company = array_shift($companies);
                    $companyid = $company->companyid;
                } else {
                    // Need a default.
                    $companyid = 0;
                }
                $entry = array('userid' => $userid,
                               'courseid' => $courseid,
                               'coursename' => $courserec->fullname,
                               'companyid' => $companyid,
                               'timeenrolled' => $timeenrolled,
                               'timestarted' => $timeenrolled,
                               'modifiedtime' => $modifiedtime
                               );
                $DB->insert_record('local_iomad_track', $entry);
            }
        }

        return true;
    }

    /**
     * Consume user license used event
     * @param object $event the event object
     */
    public static function user_license_used($event) {
        global $DB;

        $userid = $event->userid;
        $courseid = $event->courseid;
        $licenseid = $event->other['licenseid'];
        $licenserecordid = $event->objectid;
        $timeenrolled = $event->timecreated;
        $modifiedtime = $event->timecreated;

        // Check if there is already an entry for this.
        if ($entry = $DB->get_record('local_iomad_track', array('userid' => $userid,
                                                                'courseid' => $courseid,
                                                                'licenseid' => $licenseid,
                                                                'timecompleted' => null))) {
            // We already have an entry.  Change the issue time.
            $entry->timeenrolled = $timeenrolled;
            $entry->timestarted = $timeenrolled;
            $entry->modifiedtime = $modifiedtime;
            $DB->update_record('local_iomad_track', $entry);
        } else {
            // Create one.
            if ($courserec = $DB->get_record('course', array('id' => $courseid))) {
                if ($companies = $DB->get_records_sql("SELECT cu.* FROM {company_users} cu
                                                      JOIN {company_course} cc on (cu.companyid = cc.companyid)
                                                      WHERE cu.userid = :userid
                                                      AND cc.courseid = :courseid
                                                      ORDER BY cu.id DESC",
                                                      array('userid' => $userid,
                                                            'courseid' => $courseid))) {

                    // Searching by company and allocated course.
                    $company = array_shift($companies);
                    $companyid = $company->companyid;
                } else if ($companies = $DB->get_records_sql("SELECT cu.* FROM {company_users} cu
                                                      WHERE cu.userid = :userid
                                                      ORDER BY cu.id DESC",
                                                      array('userid' => $userid))) {

                    // Searching by company only as could be open shared course.
                    $company = array_shift($companies);
                    $companyid = $company->companyid;
                } else {
                    // Need a default.
                    $companyid = 0;
                }
                $licenserec = $DB->get_record('companylicense', array('id' => $licenseid));
                $userlicenserec = $DB->get_record('companylicense_users', array('id' => $licenserecordid));
                $entry = array('userid' => $userid,
                               'courseid' => $courseid,
                               'coursename' => $courserec->fullname,
                               'companyid' => $companyid,
                               'licenseid' => $licenseid,
                               'licenseallocated' => $userlicenserec->issuedate,
                               'licensename' => $licenserec->name,
                               'timeenrolled' => $timeenrolled,
                               'timestarted' => $timeenrolled,
                               'modifiedtime' => $modifiedtime
                               );
                $DB->insert_record('local_iomad_track', $entry);
            }
        }

        return true;
    }

    /**
     * Consume user enrolment deleted event
     * @param object $event the event object
     */
    public static function user_enrolment_deleted($event) {
        global $DB;

        // Do nothing for now.

        return true;
    }

    /**
     * Consume user graded event
     * @param object $event the event object
     */
    public static function user_graded($event) {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;
        $itemid = $event->other['itemid'];
        $finalgrade = $event->other['finalgrade'];

        // If this isn't a course, we don't care.
        if (!$DB->get_record('grade_items', array('id' => $itemid, 'itemtype' => 'course'))) {
            return true;
        }

        // In case we get a null.
        if (empty($finalgrade)) {
            $finalgrade = 0;
        }

        // Check if there is already an entry for this.
        if ($entry = $DB->get_record('local_iomad_track', array('userid' => $userid,
                                                                'courseid' => $courseid,
                                                                'timecompleted' => null))) {
            // We already have an entry.  Remove it.
            $DB->set_field('local_iomad_track', 'finalscore', $finalgrade, array('id' => $entry->id));
            $DB->set_field('local_iomad_track', 'modifiedtime', $event->timecreated, array('id' => $entry->id));
        }

        return true;
    }

    /**
     * Consume company user assigned event
     * @param object $event the event object
     */
    public static function company_user_assigned($event) {
        global $DB;

        // Check if there are any courses recorded for this user where the companyid == 0.

        if ($DB->get_records('local_iomad_track', array('userid' => $event->relateduserid, 'companyid' => 0))) {
            $DB->set_field('local_iomad_track', 'companyid', $event->objectid, array('userid' => $event->relateduserid, 'companyid' => 0));
        }

        return true;
    }

    /**
     * Consume company course updated event
     * @param object $event the event object
     */
    public static function company_course_updated($event) {
        global $DB;

        $courseid = $event->objectid;
        $original = $event->other['iomadcourse'];

        // Check if the validlength has changed.
        if ($current = $DB->get_record('iomad_courses', array('courseid' => $courseid))) {
            if ($current->validlength != $original['validlength']) {
                $offset = $current->validlength * 24 *60 * 60;
                $DB->execute("UPDATE {local_iomad_track}
                              SET timeexpires = timecompleted + :offset
                              WHERE courseid = :courseid
                              AND timecompleted > 0",
                              array('offset' => $offset,
                                     'courseid' => $courseid));
            }
        }

        return true;
    }
}
