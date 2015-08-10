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
     * See if this certificate already exists
     */
    private static function find_stored_certificate($contextid, $certrecordid, $filename, $userid) {

        $fs = get_file_storage();

        // Prepare file record object
        $component = 'mod_' . CERTIFICATE;
        $filearea = 'issue';
        $filepath = '/';
        $fileinfo = array(
            'contextid' => $contextid,   // ID of context
            'component' => $component,   // usually = table name
            'filearea'  => $filearea,     // usually = table name
            'itemid'    => $certrecordid,  // usually = ID of row in table
            'filepath'  => $filepath,     // any path beginning and ending in /
            'filename'  => $filename,    // any filename
            'mimetype'  => 'application/pdf',    // any filename
            'userid'    => $userid);

        if ($fs->file_exists($contextid, $component, $filearea, $certrecordid, $filepath, $filename)) {

        } else {
            return false;
        }
    }

    /**
     * Process (any) certificates in the course
     */
    private static function record_certificates($courseid, $userid) {
        global $DB;

        // Get course.
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        // Get context
        $context = context_course::instance($courseid);

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
            if (!$cm->uservisible) {
                continue;
            }

            // Find certificate issue record or create it (in cert lib.php)
            $certissue_function = CERTIFICATE . '_get_issue';
            $certisue = $certissue_function($course, $user, $certificate, $cm);

            // Filename

            // Find existing stored certificate or create new one
            find_stored_certificate($context->id, $certissue->id, $filename, $userid);

            // Copy to local storage

            // Record all of above in local db table
        
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

        // Record the completion event.
        $completion = new \StdClass();
        $completion->courseid = $data['courseid'];
        $completion->userid = $data['relateduserid'];
        $completion->timecompleted = $data['timecreated'];
        $DB->insert_record('local_iomad_track', $completion);

        // Debug
        mtrace('Iomad completion recorded for userid ' . $data['relateduserid'] . ' in courseid ' . $data['courseid']);

        self::record_certificates($data['courseid'], $data['relateduserid']);
    }
}
