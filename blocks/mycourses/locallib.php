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
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function mycourses_get_my_completion($sort = 'coursefullname', $dir = 'ASC') {
    global $DB, $USER, $CFG;

    $companyid = iomad::get_my_companyid(context_system::instance(), false);

    // Do we need to tie courses down to only my company courses?
    $companycoursesql = "";
    if (!empty($companyid)) {
        $company = new company($companyid);
        $companycourses = $company->get_menu_courses(true);
        $companycoursesql = "AND cc.courseid IN (" . join(',', array_keys($companycourses)) . ")";
    } 

    // Check if there is a iomadcertificate module.
    if ($certmodule = $DB->get_record('modules', array('name' => 'iomadcertificate'))) {
        $hasiomadcertificate = true;
        require_once($CFG->dirroot.'/mod/iomadcertificate/lib.php');
    } else {
        $hasiomadcertificate = false;
    }

    $mycompletions = new stdclass();
    $myinprogress = $DB->get_records_sql("SELECT DISTINCT cc.id, cc.userid, cc.courseid as courseid, c.fullname as coursefullname, c.summary as coursesummary, c.visible, ic.hasgrade, cc.timestarted, cc.modifiedtime
                                          FROM {local_iomad_track} cc
                                          JOIN {course} c ON (c.id = cc.courseid)
                                          JOIN {user_enrolments} ue ON (ue.userid = cc.userid)
                                          JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = c.id)
                                          LEFT JOIN {iomad_courses} ic ON (c.id = ic.courseid AND cc.courseid = ic.courseid)
                                          WHERE cc.userid = :userid
                                          AND cc.companyid = :companyid
                                          $companycoursesql
                                          AND c.visible = 1
                                          AND cc.timecompleted IS NULL
                                          AND ue.timestart != 0",
                                          ['userid' => $USER->id,
                                           'companyid' => $companyid]);

    // We need to de-duplicate this list.
    $recs = [];
    $myrecs = [];
    foreach ($myinprogress as $rec) {
        $myrecs[$rec->courseid] = $rec;
    }
    $myinprogress = [];
    foreach ($myrecs as $rec) {
        $myinprogress[$rec->id] = $rec;
    }

    // We dont care about these.  If you have enrolled then you are started.
    $mynotstartedenrolled = array();
    $unsortedcourses = array();

    $mynotstartedlicense = $DB->get_records_sql("SELECT clu.id, clu.userid, clu.licensecourseid as courseid, c.fullname as coursefullname, c.summary as coursesummary, c.visible
                                          FROM {companylicense_users} clu
                                          JOIN {course} c ON (c.id = clu.licensecourseid)
                                          JOIN {companylicense} cl ON (clu.licenseid = cl.id)
                                          WHERE clu.userid = :userid
                                          AND cl.companyid = :companyid
                                          AND c.visible = 1
                                          AND clu.isusing = 0",
                                          ['userid' => $USER->id,
                                           'companyid' => $companyid]);

    // Get courses which are available as self sign up and assigned to the company.
    // First we discount everything else we have in progress.
    $myusedcourses = array();
    foreach ($myinprogress as $id => $inprogress) {
        $myinprogress[$id]->coursefullname = format_string($inprogress->coursefullname, true, ['context' => context_course::instance($inprogress->courseid)]);
        $myusedcourses[$inprogress->courseid] = $inprogress->courseid;
        if (empty($inprogress->hasgrade)) {
            $myinprogress[$id]->finalgrade = "";
        }
    }
    if (!empty($myusedcourses)) {
        $inprogresssql = "AND c.id NOT IN (" . join(',', array_keys($myusedcourses)) . ")";
    } else {
        $inprogresssql = "";
    }
    $myselfenrolcourses = array();
    $myavailablecourses = array();
    if (!empty($companyid)) {
        $companyselfenrolcourses = $DB->get_records_sql("SELECT e.id,e.courseid,c.fullname as coursefullname, c.summary as coursesummary
                                                         FROM {enrol} e
                                                         JOIN {course} c ON (e.courseid = c.id)
                                                         WHERE e.enrol = :enrol
                                                         AND e.status = 0
                                                         AND c.id IN (
                                                           SELECT courseid FROM {company_course}
                                                           WHERE companyid = :companyid)
                                                         AND c.visible = 1
                                                         $inprogresssql",
                                                         array('companyid' => $companyid,
                                                               'enrol' => 'self'));
        $sharedselfenrolcourses = $DB->get_records_sql("SELECT e.id,e.courseid,c.fullname as coursefullname, c.summary as coursesummary
                                                        FROM {enrol} e
                                                        JOIN {course} c ON (e.courseid = c.id)
                                                        WHERE e.enrol = :enrol
                                                         AND e.status = 0
                                                         AND c.id IN (
                                                           SELECT courseid FROM {iomad_courses}
                                                           WHERE shared = 1)
                                                         AND c.visible = 1
                                                        $inprogresssql",
                                                        array('enrol' => 'self'));
        foreach ($companyselfenrolcourses as $companyselfenrolcourse) {
            $companyselfenrolcourse->coursefullname = format_string($companyselfenrolcourse->coursefullname, true, ['context' => context_course::instance($companyselfenrolcourse->courseid)]);
            $myavailablecourses[$companyselfenrolcourse->coursefullname] = $companyselfenrolcourse;
        }
        foreach ($sharedselfenrolcourses as $sharedselfenrolcourse) {
            $sharedselfenrolcourse->coursefullname = format_string($sharedselfenrolcourse->coursefullname, true, ['context' => context_course::instance($sharedselfenrolcourse->courseid)]);
            $myavailablecourses[$sharedselfenrolcourse->coursefullname] = $sharedselfenrolcourse;
        }
        // Check if there are any courses from 'blanket' licenses.
        if ($blanketlicenses = $DB->get_records_sql("SELECT * FROM {companylicense}
                                                     WHERE companyid = :companyid
                                                     AND type = :type
                                                     AND startdate < :startdate
                                                     AND expirydate > :expirydate",
                                                    ['companyid' => $companyid, 'type' => 4, 'startdate' => time(), 'expirydate' => time()])) {
            $blanketcourses = [];
            foreach ($blanketlicenses as $blanketlicense) {
                $licensecourses = $DB->get_records_sql("SELECT c.id, c.id  as courseid,c.fullname as coursefullname,c.summary as coursesummary
                                                        FROM {course} c
                                                        JOIN {companylicense_courses} clc on (c.id = clc.courseid)
                                                        WHERE clc.licenseid = :licenseid",
                                                        ['licenseid' => $blanketlicense->id]);
                foreach ($licensecourses as $licensecourse) {
                    $blanketcourses[$licensecourse->id] = $licensecourse;
                }
            }
            foreach ($blanketcourses as $blanketcourse) {
                $blanketcourse->fullname = format_string($blanketcourse->coursefullname, true, ['context' => context_course::instance($blanketcourse->courseid)]);
                $myavailablecourses[$blanketcourse->coursefullname] = $blanketcourse;
            }
        }
    }
    foreach($mynotstartedlicense as $licensedcourse) {
        $licensedcourse->coursefullname = format_string($licensedcourse->coursefullname, true, ['context' => context_course::instance($licensedcourse->courseid)]);
        $myavailablecourses[$licensedcourse->coursefullname] = $licensedcourse;
    }

    // Put them into alpahbetical order.
    $myavailablecourses = mycourses_sort($myavailablecourses, 'coursefullname', $dir);
    $myinprogress = mycourses_sort($myinprogress, $sort, $dir);

    $mycompletions->myinprogress = $myinprogress;
    $mycompletions->mynotstartedenrolled = array();
    $mycompletions->mynotstartedlicense = $myavailablecourses;

    return $mycompletions;

}

function mycourses_get_my_archive($sort = 'coursefullname', $dir = 'ASC') {
    global $DB, $USER, $CFG;

    $companyid = iomad::get_my_companyid(context_system::instance(), false);

    // Check if there is a iomadcertificate module.
    if ($certmodule = $DB->get_record('modules', array('name' => 'iomadcertificate'))) {
        $hasiomadcertificate = true;
        require_once($CFG->dirroot.'/mod/iomadcertificate/lib.php');
    } else {
        $hasiomadcertificate = false;
    }

    $mycompletions = new stdclass();
    $myarchive = $DB->get_records_sql("SELECT cc.id, cc.userid, cc.courseid as courseid, cc.finalscore as finalgrade, cc.timecompleted, cc.timestarted, c.fullname as coursefullname, c.summary as coursesummary
                                       FROM {local_iomad_track} cc
                                       JOIN {course} c ON (c.id = cc.courseid)
                                       WHERE cc.userid = :userid
                                       AND cc.companyid = :companyid
                                       AND c.visible = 1
                                       AND cc.timecompleted > 0",
                                       ['userid' => $USER->id,
                                        'companyid' => $companyid]);

    // Deal with completed course scores and links for certificates.
    foreach ($myarchive as $id => $archive) {
       $myarchive[$id]->coursefullname = format_string($archive->coursefullname, true, ['context' => context_course::instance($archive->courseid)]);
       $certstring = '';

        // Deal with the iomadcertificate info.
        if ($hasiomadcertificate) {
            if ($iomadcertificateinfo = $DB->get_record('iomadcertificate',
                                                         array('course' => $archive->courseid))) {
                // Get the certificate from the download files thing.
                if ($traccertrec = $DB->get_record('local_iomad_track_certs', array('trackid' => $id))) {
                    // create the file download link.
                    $coursecontext = context_course::instance($archive->courseid);
                    $certstring = moodle_url::make_file_url('/pluginfile.php', '/'.$coursecontext->id.'/local_iomad_track/issue/'.$traccertrec->trackid.'/'.$traccertrec->filename);
                }
            } else {
                $certstring = '';
            }
        } else {
            $certstring = '';
        }

        $myarchive[$id]->certificate = $certstring;

    }

    $myarchive = mycourses_sort($myarchive, $sort, $dir);
    $mycompletions->mycompleted = $myarchive;

    return $mycompletions;
}

function mycourses_sort($courselist, $sorton = 'timestarted', $direction = "ASC") {
    $namedcourses = [];
    foreach ($courselist as $id => $course) {
        $namedcourses[$course->$sorton . $id] = $courselist[$id];
    }
    if ($direction == "ASC") {
        ksort($namedcourses, SORT_NATURAL | SORT_FLAG_CASE);
    } else {
        krsort($namedcourses, SORT_NATURAL | SORT_FLAG_CASE);
    }

    return $namedcourses;
}
