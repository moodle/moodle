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
 * @package    local_email
 * @copyright  2022 Derick Turner
 * @author    Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_email_reports\task;

use \EmailTemplate;
use \company;
use \context_course;

class course_not_completed_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('course_not_completed_task', 'local_email_reports');
    }

    /**
     * Run email cron.
     */
    public function execute() {
        global $DB, $CFG;

        // Set some defaults.
        $runtime = time();
        $courses = array();
        $dayofweek = date('w', $runtime) + 1;

        // We only want the student role.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        mtrace("Running email report course not completed task at ".date('D M Y h:m:s', $runtime));

        // Deal with courses which have completed by warnings
        $notcompletedsql = "SELECT lit.*, c.name AS companyname, ic.notifyperiod, u.firstname,u.lastname,u.username,u.email,u.lang
                            FROM {local_iomad_track} lit
                            JOIN {company} c ON (lit.companyid = c.id)
                            JOIN {iomad_courses} ic ON (lit.courseid = ic.courseid)
                            JOIN {user} u ON (lit.userid = u.id)
                            JOIN {course} co ON (lit.courseid = co.id AND ic.courseid = co.id)
                            WHERE co.visible = 1
                            AND ic.warncompletion > 0
                            AND lit.timecompleted IS NULL
                            AND lit.timeenrolled < " . $runtime . " - (ic.warncompletion * 86400)
                            AND u.deleted = 0
                            AND u.suspended = 0
                            AND lit.completedstop = 0";

        mtrace("sending user completion warning emails");

        // Email all of the users.
        $allusers = $DB->get_records_sql($notcompletedsql);

        $periods = array(1 => " day",
                         2 => " week",
                         3 => " fortnight",
                         4 => " month");
        foreach ($allusers as $compuser) {
            if (!$user = $DB->get_record('user', array('id' => $compuser->userid))) {
                continue;
            }
            if (!$course = $DB->get_record('course', array('id' => $compuser->courseid))) {
                continue;
            }
            if (!$company = $DB->get_record('company', array('id' => $compuser->companyid))) {
                continue;
            }

            // Deal with parent companies as we only want users in this company.
            $companyobj = new company($company->id);
            if ($parentslist = $companyobj->get_parent_companies_recursive()) {
                if ($DB->get_records_sql("SELECT userid FROM {company_users}
                                          WHERE companyid IN (" . implode(',', array_keys($parentslist)) .")
                                          AND userid = :userid",
                                          array('userid' => $compuser->userid))) {
                    continue;

                }
            }

            // Needs to be a student and enrolled.
            if (!$DB->get_record_sql("SELECT ra.id FROM
                                     {user_enrolments} ue
                                     INNER JOIN {enrol} e ON (ue.enrolid = e.id AND e.status=0)
                                     JOIN {role_assignments} ra ON (ue.userid = ra.userid)
                                     JOIN {context} c ON (ra.contextid = c.id AND c.instanceid = e.courseid)
                                     WHERE c.contextlevel = 50
                                     AND ue.userid = :userid
                                     AND e.courseid = :courseid
                                     AND ra.roleid = :studentrole",
                                     array('courseid' => $compuser->courseid,
                                           'userid' => $compuser->userid,
                                           'studentrole' => $studentrole->id))) {

                // We want to remove them from the future list.
                $compuser->completedstop = 1;
                $compuser->modifiedtime = $runtime;
                $DB->update_record('local_iomad_track', $compuser);
                continue;
            }

            // get the company template info.
            // Check against per company template repeat instead.
            if ($templateinfo = $DB->get_record('email_template', array('companyid' => $compuser->companyid, 'lang' => $compuser->lang, 'name' => 'completion_warn_user'))) {
                // Check if its the correct day, if not continue.
                if (!empty($templateinfo->repeatday) && $templateinfo->repeatday != 99 && $templateinfo->repeatday != $dayofweek - 1) {
                    continue;
                }

                // otherwise set the notifyperiod
                if ($templateinfo->repeatperiod == 0) {
                    $notifyperiod = "";
                } else if ($templateinfo->repeatperiod == 99) {
                    $notifyperiod = "";
                } else {
                    $notifytime = strtotime("- 1" . $periods[$templateinfo->repeatperiod], $runtime);
                    $notifyperiod = "AND sent < $notifytime";
                }
            } else {
                // use the default notify period.
                $notifytime = $runtime - $compuser->notifyperiod * 86400;
                $notifyperiod = "AND sent < $notifytime";
            }

            // Check if we have sent any emails and if they are within the period.
            if ($DB->count_records('email', array('userid' => $compuser->userid,
                                                  'courseid' => $compuser->courseid,
                                                  'templatename' => 'completion_warn_user')) > 0) {
                if (!empty($notifyperiod)) {
                    if (!$DB->get_records_sql("SELECT id FROM {email}
                                              WHERE userid = :userid
                                              AND courseid = :courseid
                                              AND templatename = :templatename
                                              $notifyperiod
                                              AND id IN (
                                                 SELECT MAX(id) FROM {email}
                                                 WHERE userid = :userid2
                                                 AND courseid = :courseid2
                                                 AND templatename = :templatename2)",
                                              array('userid' => $compuser->userid,
                                                    'courseid' => $compuser->courseid,
                                                    'templatename' => 'completion_warn_user',
                                                    'userid2' => $compuser->userid,
                                                    'courseid2' => $compuser->courseid,
                                                    'templatename2' => 'completion_warn_user'))) {
                        continue;
                    }
                }
            }
            mtrace("Sending completion warning email to $user->email");
            EmailTemplate::send('completion_warn_user', array('course' => $course, 'user' => $user, 'company' => $companyobj));

            // Send the supervisor email too.
            mtrace("Sending completion warning email to $user->email supervisor");
            company::send_supervisor_warning_email($user, $course);

            // Do we have a value for the template repeat?
            if (!empty($templateinfo->repeatvalue)) {
                $sentcount = $DB->count_records_sql("SELECT count(id) FROM {email}
                                                     WHERE userid =:userid
                                                     AND courseid = :courseid
                                                     AND templatename = :templatename
                                                     AND modifiedtime > :timesent",
                                                     array('userid' => $compuser->userid,
                                                           'courseid' => $compuser->courseid,
                                                           'templatename' => $templateinfo->name,
                                                           'timesent' => $compuser->timestarted));
                if ($sentcount >= $templateinfo->repeatvalue) {
                    $compuser->completedstop = 1;
                    $compuser->modifiedtime = $runtime;
                    $DB->update_record('local_iomad_track', $compuser);
                }
            }
            if (empty($templateinfo->repeatperiod)) {
                $compuser->completedstop = 1;
                $compuser->modifiedtime = $runtime;
                $DB->update_record('local_iomad_track', $compuser);
            }
        }

        mtrace("sending completion warning emails to the managers");
        // Email the managers
        // Get the companies from the list of users in the temp table.
        $notcompletedcompanysql = "SELECT DISTINCT lit.companyid
                                   FROM {local_iomad_track} lit
                                   JOIN {company} c ON (lit.companyid = c.id)
                                   JOIN {iomad_courses} ic ON (lit.courseid = ic.courseid)
                                   JOIN {user} u ON (lit.userid = u.id)
                                   JOIN {course} co ON (lit.courseid = co.id AND ic.courseid = co.id)
                                   WHERE co.visible = 1
                                   AND ic.warncompletion > 0
                                   AND lit.timecompleted IS NULL
                                   AND lit.timeenrolled < " . $runtime . " - (ic.warncompletion * 86400)
                                   AND u.deleted = 0
                                   AND u.suspended = 0
                                   AND lit.completedstop = 0";

        $companies = $DB->get_records_sql($notcompletedcompanysql);
        foreach ($companies as $company) {
            if (!$companyrec = $DB->get_record('company', array('id' => $company->companyid))) {
                continue;
            }

            if (!empty($companyrec->managernotify) && ($companyrec->managernotify == 1 || $companyrec->managernotify == 3)) {
                if ($dayofweek == $companyrec->managerdigestday || empty($companyrec->managerdigestday)) {

                    // Deal with parent companies as we only want manager of this company.
                    $companyobj = new company($company->companyid);
                    if ($parentslist = $companyobj->get_parent_companies_recursive()) {
                        $companysql = " AND lit.userid NOT IN (
                                        SELECT userid FROM {company_users}
                                        WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
                    } else {
                        $companysql = "";
                    }

                    // Get the managers.
                    $managers = $DB->get_records_sql("SELECT * FROM {company_users}
                                                      WHERE companyid = :companyid
                                                      AND managertype != 0
                                                      $companysql", array('companyid' => $company->companyid));
                    foreach ($managers as $manager) {
                        // Department managers dont get reports on company manager users.
                        if ($manager->managertype == 2) {
                            $departmentmanager = true;
                        } else {
                            $departmentmanager = false;
                        }

                        // If this is a manager of a parent company - skip them.
                        if (!empty($parentslist) &&
                            $DB->get_records_sql("SELECT id FROM {company_users}
                                                  WHERE userid = :userid
                                                  AND userid IN (
                                                  SELECT userid FROM {company_users}
                                                  WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))
                                                  ", array('userid' => $manager->userid))) {
                            continue;
                        }

                        // Get their users.
                        $departmentusers = company::get_recursive_department_users($manager->departmentid);
                        $departmentids = "";
                        foreach ($departmentusers as $departmentuser) {
                            if (!empty($departmentids)) {
                                $departmentids .= ",".$departmentuser->userid;
                            } else {
                                $departmentids .= $departmentuser->userid;
                            }
                        }
                        $notcompleteddigestsql = "SELECT lit.*, c.name AS companyname, ic.notifyperiod, u.firstname,u.lastname,u.username,u.email,u.lang
                                                  FROM {local_iomad_track} lit
                                                  JOIN {company} c ON (lit.companyid = c.id)
                                                  JOIN {iomad_courses} ic ON (lit.courseid = ic.courseid)
                                                  JOIN {user} u ON (lit.userid = u.id)
                                                  JOIN {course} co ON (lit.courseid = co.id AND ic.courseid = co.id)
                                                  WHERE co.visible = 1
                                                  AND lit.companyid = :companyid
                                                  AND lit.userid IN (" . $departmentids . ")
                                                  AND lit.userid != :managerid
                                                  $companysql
                                                  AND ic.warncompletion > 0
                                                  AND lit.timecompleted IS NULL
                                                  AND lit.timeenrolled < " . $runtime . " - (ic.warncompletion * 86400)
                                                  AND u.deleted = 0
                                                  AND u.suspended = 0
                                                  AND lit.completedstop = 0";

                        $managerusers = $DB->get_records_sql($notcompleteddigestsql,
                                                             array('managerid' => $manager->userid,
                                                                   'companyid' => $company->companyid));

                        $summary = "<table><tr><th>" . get_string('firstname') . "</th>" .
                                   "<th>" . get_string('lastname') . "</th>" .
                                   "<th>" . get_string('email') . "</th>" .
                                   "<th>" . get_string('department', 'block_iomad_company_admin') ."</th>";
                                   "<th>" . get_string('course') . "</th>" .
                                   "<th>" . get_string('timeenrolled', 'local_report_completion') ."</th></tr>";
                        $foundusers = false;
                        foreach ($managerusers as $manageruser) {
                            if (!$user = $DB->get_record('user', array('id' => $manageruser->userid))) {
                                continue;
                            }
                            if (!$course = $DB->get_record('course', array('id' => $manageruser->courseid))) {
                                continue;
                            }
                            if ($departmentmanager && $DB->get_record('company_users', array('companyid' => $company->companyid, 'managertype' => 1, 'userid' => $manageruser->userid))) {
                                continue;
                            }
                            if (!$DB->get_record_sql("SELECT ra.id FROM
                                                     {user_enrolments} ue
                                                     INNER JOIN {enrol} e ON (ue.enrolid = e.id AND e.status=0)
                                                     JOIN {role_assignments} ra ON (ue.userid = ra.userid)
                                                     JOIN {context} c ON (ra.contextid = c.id AND c.instanceid = e.courseid)
                                                     WHERE c.contextlevel = 50
                                                     AND ue.userid = :userid
                                                     AND e.courseid = :courseid
                                                     AND ra.roleid = :studentrole",
                                                     array('courseid' => $manageruser->courseid,
                                                           'userid' => $manageruser->userid,
                                                           'studentrole' => $studentrole->id))) {
                                continue;
                            }
                            $foundusers = true;
                            $summary .= "<tr><td>" . $manageruser->firstname . "</td>" .
                                        "<td>" . $manageruser->lastname . "</td>" .
                                        "<td>" . $manageruser->email . "</td>" .
                                        "<td>" . $manageruser->coursename . "</td>" .
                                        "<td>" . date($CFG->iomad_date_format, $manageruser->timeenrolled) . "</td></tr>";
                        }
                        $summary .= "</table>";
                        if ($foundusers && $user = $DB->get_record('user', array('id' => $manager->userid))) {
                            $course = (object) [];
                            $course->reporttext = $summary;
                            $course->id = 0;
                            mtrace("Sending completion warning summary report to $user->email");
                            EmailTemplate::send('completion_warn_manager', array('user' => $user, 'course' => $course, 'company' => $companyobj));
                        }
                    }
                }
            }
        }

        mtrace("email reporting course not completed warning task completed at " . date('D M Y h:m:s', time()));
    }

}