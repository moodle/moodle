<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful, $
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/local/email/lib.php');

function emails_report_cron() {
    global $DB;

    $runtime = time();
    echo "Running email report cron at ".date('D M Y h:m:s', $runtime)."\n";

    // Generate automatic reports.
    // Training reaching lifetime/expired.
    if ($checkcourses = $DB->get_records_sql('SELECT * from {iomad_courses} where validlength!=0')) {
        // We have some courses which we need to check against.
        foreach ($checkcourses as $checkcourse) {
            $expiredtext = "";
            $expiringtext = "";
            $latetext = "";
            echo "Get completion information for $checkcourse->courseid \n";
            if ($coursecompletions = $DB->get_records('course_completions', array('course' => $checkcourse->courseid))) {
                // Get the course information.
                $course = $DB->get_record('course', array('id' => $checkcourse->courseid));
                // We have completion information.
                foreach ($coursecompletions as $completion) {
                    if (!empty($completion->timecompleted)) {
                        // Got a completed time.
                        if ($completion->timecompleted + $checkcourse->validlength * 86400 > $runtime) {
                            // Got someone overdue.
                            $user = $DB->get_record('user', array('id' => $completion->userid));
                            echo "Sending overdue email to $user->email \n";
                            EmailTemplate::send('expire', array('course' => $course, 'user' => $user));
                            $expiredtext .= $user->firstname.' '.$user->lastname.', '.$user->email.' - '.
                                            date('D M Y', $completion->timecompleted)."\n";
                        } else if ($completion->timecompleted + $checkcourse->validlength * 86400 +
                                   $checkcourse->warnexpire * 86400 > $runtime) {
                            // We got someone approaching expiry.
                            $user = $DB->get_record('user', array('id' => $completion->userid));
                            echo "Sending exiry email to $user->email \n";
                            EmailTemplate::send('expiry_warn_user', array('course' => $course, 'user' => $user));
                            $expiringtext .= $user->firstname.' '.$user->lastname.', '.$user->email.' - '.
                                             date('D M Y', $completion->timecompleted)."\n";
                        }
                    } else if (!empty($completion->timeenrolled)) {
                        if ($completion->timeenrolled + $checkcourse->warncompletion * 86400 > $runtime) {
                            // Go someone not completed in time.
                            $user = $DB->get_record('user', array('id' => $completion->userid));
                            echo "Sending completion warning email to $user->email \n";
                            EmailTemplate::send('completion_warn_user', array('course' => $course, 'user' => $user));
                            $latetext .= $user->firstname.' '.$user->lastname.', '.$user->email.' - '.
                                         date('D M Y', $completion->timeenrolled)."\n";
                        }
                    }
                }
                // Get the list of company managers.
                $companymanagers = $DB->get_records_sql("SELECT cm.userid FROM {companymanager} cm,
                                                         {companycourse} cc
                                                         WHERE cc.courseid = $checkcourse->courseid
                                                         AND cc.companyid = cm.companyid");
                $managers = array();
                $coursecontext = context_course::instance($course->id);
                foreach ($companymanagers as $companymanager) {
                    $user = $DB->get_record('user', array('id' => $companymanager->userid));
                    if (has_capability('moodle/course:view', $coursecontext, $user)) {
                        $managers[] = $user;
                    }
                }

                // Check if there are any managers on this course.
                foreach ($managers as $manager) {
                    if (!empty($expiredtext)) {
                        // Send the summary email.
                        $course->reporttext = $expiredtext;
                        EmailTemplate::send('expire_manager', array('course' => $course, 'user' => $manager));
                    }
                    if (!empty($expiringtext)) {
                        // Send the summary email.
                        $course->reporttext = $expiringtext;
                        EmailTemplate::send('expiry_warn_manager', array('course' => $course, 'user' => $manager));
                    }
                    if (!empty($latetext)) {
                        // Send the summary email.
                        $course->reporttext = $latetext;
                        EmailTemplate::send('completion_warn_manager', array('course' => $course, 'user' => $manager));
                    }
                }
            }
        }
    }
}
