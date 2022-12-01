<?php
//
// This file is part of Moodle - http://moodle.org/
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

class course_expiry_warning_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('course_expiry_warning_task', 'local_email_reports');
    }

    /**
     * Run email course_expiry_warning_task.
     */
    public function execute() {
        global $DB, $CFG;

        // Set some defaults.
        $runtime = time();
        $courses = array();
        $dayofweek = date('w', $runtime) + 1;

        // We only want the student role.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        mtrace("Running email report course expiry warning task at ".date('D M Y h:m:s', $runtime));

        // Getting courses which have expiry settings.
        $expirycourses = $DB->get_records_sql("SELECT * FROM {iomad_courses}
                                               WHERE validlength > 0");


        // Email the managers
        mtrace("sending to managers");

        // Deal with courses which have expiry warnings
        $companies = [];
        foreach ($expirycourses as $expirycourse) {
            $targettime = $runtime - ($expirycourse->validlength * 86400) - ($expirycourse->warnexpire * 86400);

            // Get the companies from the list of users in the temp table.
            $companysql = "SELECT DISTINCT lit.companyid
                                FROM {local_iomad_track} lit
                                JOIN {user} u ON (lit.userid = u.id)
                                JOIN {course} co ON (lit.courseid = co.id)
                                WHERE co.visible = 1
                                AND co.id = :expirycourseid
                                AND u.deleted = 0
                                AND u.suspended = 0
                                AND lit.timecompleted < :targettime
                                AND lit.expiredstop = 0
                                AND lit.id IN (
                                    SELECT max(id) FROM {local_iomad_track})";

            $foundcompanies = $DB->get_records_sql($companysql, ['expirycourseid' => $expirycourse->id, 'targettime' => $targettime]);
            foreach ($foundcompanies as $id => $foundcompany) {
                $companies[$id] = $foundcompany;
            }
        }

        foreach ($companies as $company) {
            if (!$companyrec = $DB->get_record('company', array('id' => $company->companyid))) {
                continue;
            }
            if ($companyrec->managernotify == 1 || $companyrec->managernotify == 3) {
                if ($dayofweek == $companyrec->managerdigestday || empty($companyrec->managerdigestday)) {

                    // Deal with parent companies as we only want manager of this company.
                    $companyobj = new company($company->companyid);
                    if ($parentslist = $companyobj->get_parent_companies_recursive()) {
                        $companysql = " AND userid NOT IN (
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
                        $managerusers = $DB->get_records_sql("SELECT * FROM {" . $tempcomptablename . "}
                                                              WHERE userid IN (" . $departmentids . ")
                                                              AND userid != :managerid
                                                              $companysql",
                                                              array('managerid' => $manager->userid));
                        $summary = "<table><tr><th>" . get_string('firstname') . "</th>" .
                                   "<th>" . get_string('lastname') . "</th>" .
                                   "<th>" . get_string('email') . "</th>" .
                                   "<th>" . get_string('department', 'block_iomad_company_admin') ."</th>";
                                   "<th>" . get_string('course') . "</th>" .
                                   "<th>" . get_string('completed', 'local_report_completion') ."</th></tr>";
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

                            $managerusers = $DB->get_records_sql("SELECT * FROM {" . $tempcomptablename . "}
                                                                  WHERE userid IN (" . $departmentids . ")");
                            $foundusers = false;
                            foreach ($managerusers as $manageruser) {
                                if (!$user = $DB->get_record('user', array('id' => $manageruser->userid))) {
                                    continue;
                                }
                                if (!$course = $DB->get_record('course', array('id' => $manageruser->courseid))) {
                                    continue;
                                }
                                $foundusers = true;
                                if ($manageruser->timecompleted == 0) {
                                    $datestring = get_string('never') . "\n";
                                } else {
                                    $datestring = date($CFG->iomad_date_format, $manageruser->timecompleted) . "\n";
                                }

                                $summary .= "<tr><td>" . $manageruser->firstname . "</td>" .
                                            "<td>" . $manageruser->lastname . "</td>" .
                                            "<td>" . $manageruser->email . "</td>" .
                                            "<td>" . $manageruser->departmentname . "</td>" .
                                            "<td>" . $manageruser->coursename . "</td>" .
                                            "<td>" . $datestring . "</td></tr>";
                            }
                            $summary .= "</table>";
                        }
                        if ($foundusers && $user = $DB->get_record('user', array('id' => $manager->userid))) {
                            $course = new stdclass();
                            $course->reporttext = $summary;
                            $course->id = 0;
                            mtrace("Sending expiry summary report to $user->email");
                            EmailTemplate::send('expiry_warn_manager', array('user' => $user, 'course' => $course, 'company' => $companyobj));
                        }
                    }
                }
            }
        }

        // Deal with users.
        foreach ($expirycourses as $expirycourse) {
            mtrace("Dealing with course id $expirycourse->courseid");
            $targettime = $runtime - ($expirycourse->validlength * 86400) - ($expirycourse->warnexpire * 86400);
            $expiredsql = "SELECT lit.*, c.name AS companyname, u.firstname,u.lastname,u.username,u.email,u.lang
                           FROM {local_iomad_track} lit
                           JOIN {company} c ON (lit.companyid = c.id)
                           JOIN {user} u ON (lit.userid = u.id)
                           JOIN {course} co ON (lit.courseid = co.id)
                           WHERE co.visible = 1
                           AND co.id = :expirycourseid
                           AND lit.timecompleted < :targettime
                           AND u.deleted = 0
                           AND u.suspended = 0
                           AND lit.expiredstop = 0
                           AND lit.id IN (
                               SELECT max(id) FROM {local_iomad_track}
                           GROUP BY userid,courseid)";

            // Email all of the users
            mtrace("getting expired users");
            $allusers = $DB->get_records_sql($expiredsql, ['expirycourseid' => $expirycourse->courseid, 'targettime' => $targettime]);

            foreach ($allusers as $compuser) {
                mtrace("Dealing with user id $compuser->userid");
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

                if ($DB->get_record_sql("SELECT ra.id FROM
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

                    // Expire the user from the course.
                    mtrace("Expiring $user->id from course id $course->id as a student");
                    $event = \block_iomad_company_admin\event\user_course_expired::create(array('context' => context_course::instance($course->id),
                                                                                                'courseid' => $course->id,
                                                                                                'objectid' => $course->id,
                                                                                                'userid' => $user->id));
                    $event->trigger();
                }

                // Get the company template info.
                // Check against per company template repeat instead.
                if ($templateinfo = $DB->get_record('email_template', array('companyid' => $company->id, 'lang' => $user->lang, 'name' => 'expiry_warn_user'))) {
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
                    $notifytime = $runtime - $expirycourse->notifyperiod * 86400;
                    $notifyperiod = "AND sent < $notifytime";
                }

                // Check if we have sent any emails and if they are within the period.
                if ($DB->count_records('email', array('userid' => $compuser->userid,
                                                      'courseid' => $compuser->courseid,
                                                      'templatename' => 'expiry_warn_user')) > 0) {
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
                                                        'templatename' => 'expiry_warn_user',
                                                        'userid2' => $compuser->userid,
                                                        'courseid2' => $compuser->courseid,
                                                        'templatename2' => 'expiry_warn_user'))) {
                            continue;
                        }
                    }
                }

                mtrace("Sending expiry warning email to $user->email");
                EmailTemplate::send('expiry_warn_user', array('course' => $course, 'user' => $user, 'company' => $companyobj));

                // Send the supervisor email too.
                mtrace("Sending supervisor warning email for $user->email");
                company::send_supervisor_expiry_warning_email($user, $course);

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
                                                               'timesent' => $compuser->timecompleted));
                    if ($sentcount >= $templateinfo->repeatvalue) {
                        $compuser->expiredstop = 1;
                        $compuser->modifiedtime = $runtime;
                        $DB->update_record('local_iomad_track', $compuser);
                    }
                }
                if (empty($templateinfo->repeatperiod)) {
                    // Set to never so mark it to stop.
                    $compuser->expiredstop = 1;
                    $compuser->modifiedtime = $runtime;
                    $DB->update_record('local_iomad_track', $compuser);
                }
            }
        }

        // Deal with users who have passed the expired threshold.
        mtrace("getting expiry courses");
        $completionexpirycourses = $DB->get_records_sql("SELECT * FROM {iomad_courses}
                                                         WHERE expireafter > 0");
        foreach ($completionexpirycourses as $completionexpirecourse) {
            // Get all of the users who have a time completed time > this time.
            $expiretime = 24 * 60 * 60 * $completionexpirecourse->expireafter;
            $userlist = $DB->get_records_sql("SELECT lit.* FROM
                                              {local_iomad_track} lit
                                              JOIN {user_enrolments} ue ON (lit.userid = ue.userid)
                                              JOIN {enrol} e ON (lit.courseid = e.courseid AND ue.enrolid = e.id)
                                              JOIN {course} co ON (lit.courseid = co.id AND e.courseid = co.id)
                                              WHERE co.visible = 1
                                              AND lit.courseid = :courseid
                                              AND lit.timecompleted + :expiretime < :runtime",
                                              array('courseid' => $completionexpirecourse->courseid,
                                                    'expiretime' => $expiretime,
                                                    'runtime' => $runtime));

            //  Cycle through any found users.
            foreach ($userlist as $founduser) {
                if (!$DB->get_record('local_iomad_track', array('userid' => $founduser->userid, 'courseid' => $founduser->courseid, 'timecompleted' => null))) {
                    // Expire the user from the course.
                    mtrace("expiring user $founduser->userid from course $founduser->courseid");
                    $event = \block_iomad_company_admin\event\user_course_expired::create(array('context' => context_course::instance($founduser->courseid),
                                                                                                'courseid' => $founduser->courseid,
                                                                                                'objectid' => $founduser->courseid,
                                                                                                'userid' => $founduser->userid));
                    $event->trigger();
                }
            }
        }

        mtrace("email reporting course expiry warning task completed at " . date('D M Y h:m:s', time()));
    }

}
