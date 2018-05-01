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

// In case we ever want to switch back to ordinary certificates
define('CERTIFICATE', 'iomadcertificate');

require_once($CFG->dirroot . '/mod/' . CERTIFICATE . '/lib.php');

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
            $filename = clean_filename("$certname.pdf");

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

        // Get the enrolment time for the user on the course.
        $enrolrec = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                         JOIN {enrol} e ON (ue.enrolid = e.id)
                                         WHERE ue.userid = :userid
                                         AND e.courseid = :courseid
                                         AND e.status = 0",
                                         array('userid' => $userid,
                                               'courseid' => $courseid));

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

        $trackid = $DB->insert_record('local_iomad_track', $completion);

        // Debug
        if (!PHPUNIT_TEST) {
            mtrace('Iomad completion recorded for userid ' . $userid . ' in courseid ' . $courseid);
        }

        self::record_certificates($courseid, $userid, $trackid);

        return true;
    }
}
