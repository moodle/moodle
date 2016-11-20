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
 * Helper functions for mycourses block
 *
 * @package    block_mycourses
 * @copyright  2015 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function mycourses_get_my_completion($datefrom = 0) {
    global $DB, $USER, $CFG;

    // Check if there is a iomadcertificate module.
    if ($certmodule = $DB->get_record('modules', array('name' => 'iomadcertificate'))) {
        $hasiomadcertificate = true;
        require_once($CFG->dirroot.'/mod/iomadcertificate/lib.php');
    } else {
        $hasiomadcertificate = false;
    }

    $mycompletions = new stdclass();
    $mycompleted = $DB->get_records_sql("SELECT cc.id, cc.userid, cc.courseid as courseid, cc.finalscore as finalgrade, c.fullname as coursefullname, c.summary as coursesummary
                                         FROM {local_iomad_track} cc
                                         JOIN {course} c ON (c.id = cc.courseid)
                                         WHERE cc.userid = :userid
                                         AND c.visible = 1
                                         AND cc.timecompleted > :datefrom",
                                         array('userid' => $USER->id, 'datefrom' => $datefrom));
    $myinprogress = $DB->get_records_sql("SELECT cc.id, cc.userid, cc.course as courseid, c.fullname as coursefullname, c.summary as coursesummary
                                          FROM {course_completions} cc
                                          JOIN {course} c ON (c.id = cc.course)
                                          JOIN {user_enrolments} ue ON (ue.userid = cc.userid)
                                          JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = c.id)
                                          WHERE cc.userid = :userid
                                          AND c.visible = 1
                                          AND cc.timecompleted IS NULL
                                          AND ue.timestart != 0",
                                          array('userid' => $USER->id));

    // We dont care about these.  If you have enrolled then you are started.
    $mynotstartedenrolled = array();

    $mynotstartedlicense = $DB->get_records_sql("SELECT clu.id, clu.userid, clu.licensecourseid as courseid, c.fullname as coursefullname, c.summary as coursesummary
                                          FROM {companylicense_users} clu
                                          JOIN {course} c ON (c.id = clu.licensecourseid)
                                          WHERE clu.userid = :userid
                                          AND c.visible = 1
                                          AND clu.isusing = 0",
                                          array('userid' => $USER->id));

    // Deal with completed course scores and links for certificates.
    foreach ($mycompleted as $id => $completed) {
        // Deal with the iomadcertificate info.
        if ($hasiomadcertificate) {
            if ($iomadcertificateinfo = $DB->get_record('iomadcertificate',
                                                         array('course' => $completed->courseid))) {
                $certcminfo = $DB->get_record('course_modules',
                                               array('course' => $completed->courseid,
                                                     'instance' => $iomadcertificateinfo->id,
                                                     'module' => $certmodule->id));
                $certstring = "<a href='".$CFG->wwwroot."/mod/iomadcertificate/view.php?id=".
                              $certcminfo->id."&action=get&userid=".$USER->id."&sesskey=".
                              sesskey()."'>".get_string('downloadcert', 'block_mycourses').
                              "</a>";
            } else {
                $certstring = get_string('nocerttodownload', 'block_mycourses');
            }
        } else {
            $certstring = get_string('nocerttodownload', 'block_mycourses');
        }
        $mycompleted[$id]->certificate = $certstring;

    }

    $mycompletions->mycompleted = $mycompleted;
    $mycompletions->myinprogress = $myinprogress;
    $mycompletions->mynotstartedenrolled = array();
    $mycompletions->mynotstartedlicense = $mynotstartedlicense;

    return $mycompletions;

}

function mycourses_get_my_archive($dateto = 0) {
    global $DB, $USER, $CFG;

    // Check if there is a iomadcertificate module.
    if ($certmodule = $DB->get_record('modules', array('name' => 'iomadcertificate'))) {
        $hasiomadcertificate = true;
        require_once($CFG->dirroot.'/mod/iomadcertificate/lib.php');
    } else {
        $hasiomadcertificate = false;
    }

    $mycompletions = new stdclass();
    $myarchive = $DB->get_records_sql("SELECT cc.id, cc.userid, cc.courseid as courseid, cc.finalscore as finalgrade, c.fullname as coursefullname, c.summary as coursesummary
                                       FROM {local_iomad_track} cc
                                       JOIN {course} c ON (c.id = cc.courseid)
                                       WHERE cc.userid = :userid
                                       AND c.visible = 1
                                       AND cc.timecompleted <= :dateto",
                                       array('userid' => $USER->id, 'dateto' => $dateto));

    // Deal with completed course scores and links for certificates.
    foreach ($myarchive as $id => $archive) {
        // Deal with the iomadcertificate info.
        if ($hasiomadcertificate) {
            if ($iomadcertificateinfo = $DB->get_record('iomadcertificate',
                                                         array('course' => $archive->courseid))) {
                // Get the certificate from the download files thing.
                if ($traccertrec = $DB->get_record('local_iomad_track_certs', array('trackid' => $id))) {
                    // create the file download link.
                    $coursecontext = context_course::instance($archive->courseid);
                    $certstring = "<a class=\"btn btn-info\" href='".
                                   moodle_url::make_file_url('/pluginfile.php', '/'.$coursecontext->id.'/local_iomad_track/issue/'.$traccertrec->trackid.'/'.$traccertrec->filename) .
                                  "'>" . get_string('downloadcert', 'block_mycourses').
                                  "</a>";
                }
            } else {
                $certstring = get_string('nocerttodownload', 'block_mycourses');
            }
        } else {
            $certstring = get_string('nocerttodownload', 'block_mycourses');
        }

        $myarchive[$id]->certificate = $certstring;

    }

    $mycompletions->myarchive = $myarchive;

    return $mycompletions;
}


