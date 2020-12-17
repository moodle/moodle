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

function email_reports_cron() {
    global $DB, $CFG;

    // Set some defaults.
    $runtime = time();
    $courses = array();
    $dayofweek = date('w', $runtime) + 1;

    // We only want the student role.
    $studentrole = $DB->get_record('role', array('shortname' => 'student'));

    mtrace("Running email report cron at ".date('D M Y h:m:s', $runtime));

    // Deal with courses which have completed by warnings
    $notcompletedsql = "SELECT lit.*, c.name AS companyname, ic.notifyperiod, u.firstname,u.lastname,u.username,u.email,u.lang
                        FROM {local_iomad_track} lit
                        JOIN {company} c ON (lit.companyid = c.id)
                        JOIN {iomad_courses} ic ON (lit.courseid = ic.courseid)
                        JOIN {user} u ON (lit.userid = u.id)
                        WHERE ic.warncompletion > 0
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
                               WHERE ic.warncompletion > 0
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
                                              WHERE lit.companyid = :companyid
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
                        $course = new stdclass();
                        $course->reporttext = $summary;
                        $course->id = 0;
                        mtrace("Sending completion warning summary report to $user->email");
                        EmailTemplate::send('completion_warn_manager', array('user' => $user, 'course' => $course, 'company' => $companyobj));
                    }
                }
            }
        }
    }

    mtrace("sending course not started emails");

    // Deal with courses where users have not yet started.
    $warnnotstartedcourses = $DB->get_records_sql("SELECT * FROM {iomad_courses}
                                                   WHERE warnnotstarted != 0");
    foreach ($warnnotstartedcourses as $warnnotstartedcourse) {
        $checktime = time() - $warnnotstartedcourse->warnnotstarted * 60 * 60 *24;
        $warnnotstartedusers = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                                   WHERE courseid = :courseid
                                                   AND notstartedstop = 0
                                                   AND (
                                                       (timestarted = 0
                                                       AND timeenrolled < :time1
                                                       AND licenseallocated IS NULL)
                                                     ||
                                                       (timeenrolled IS NULL
                                                       AND licenseallocated < :time2
                                                       AND licenseallocated IS NOT NULL)
                                                   )",
                                                   array('time1' => $checktime, 'time2' => $checktime, 'courseid' => $warnnotstartedcourse->courseid));
        foreach ($warnnotstartedusers as $notstarteduser) {
            if ($userrec = $DB->get_record('user', array('id' => $notstarteduser->userid, 'suspended' => 0, 'deleted' => 0))) {
                if ($courserec = $DB->get_record('course', array('id' => $notstarteduser->courseid))) {
                    if ($companyrec = $DB->get_record('company', array('id' => $notstarteduser->companyid))) {
                        // Get the company template info.
                        // Check against per company template repeat instead.
                        if ($templateinfo = $DB->get_record('email_template', array('companyid' => $notstarteduser->companyid, 'lang' => $userrec->lang, 'name' => 'course_not_started_warning'))) {
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
                                $notifyperiod = " AND sent <  $notifytime";
                            }
                        } else {
                            // use the default notify period.
                            $notifytime = $runtime - $warnnotstartedcourse->notifyperiod * 86400;
                            $notifyperiod = " AND sent < $notifytime";
                        }

                        // Check if we have sent any emails and if they are within the period.
                        if ($DB->count_records('email', array('userid' => $notstarteduser->userid,
                                                              'courseid' => $notstarteduser->courseid,
                                                              'templatename' => 'course_not_started_warning')) > 0) {
                            if (!empty($notifyperiod)) {
                                if (!$DB->get_records_sql("SELECT id FROM {email}
                                                          WHERE userid = :userid
                                                          AND courseid = :courseid
                                                          AND templatename = :templatename
                                                          $notifyperiod
                                                          AND id IN (
                                                             SELECT MAX(id) FROM {emai}l
                                                             WHERE userid = :userid2
                                                             AND courseid = :courseid2
                                                             AND templatename = :templatename2)",
                                                          array('userid' => $notstarteduser->userid,
                                                                'courseid' => $notstarteduser->courseid,
                                                                'templatename' => 'course_not_started_warning',
                                                                'userid2' => $notstarteduser->userid,
                                                                'courseid2' => $notstarteduser->courseid,
                                                                'templatename2' => 'course_not_started_warning'))) {
                                    continue;
                                }
                            }
                        }

                        // Passed all checks, send the email.
                        mtrace("Sending not started warning email to $userrec->email");
                        EmailTemplate::send('course_not_started_warning', array('user' => $userrec, 'course' => $courserec, 'company' => new company($companyrec->id)));

                        // Send the supervisor email too.
                        mtrace("Sending not started warning email to $userrec->email supervisor");
                        company::send_supervisor_not_started_warning_email($userrec, $courserec);

                        // Do we have a value for the template repeat?
                        if (!empty($templateinfo->repeatvalue)) {
                            $sentcount = $DB->count_records_sql("SELECT count(id) FROM {email}
                                                                 WHERE userid =:userid
                                                                 AND courseid = :courseid
                                                                 AND templatename = :templatename
                                                                 AND modifiedtime > :timesent",
                                                                 array('userid' => $notstarteduser->userid,
                                                                       'courseid' => $notstarteduser->courseid,
                                                                       'templatename' => $templateinfo->name,
                                                                       'timesent' => $notstarteduser->timeenrolled));
                            if ($sentcount >= $templateinfo->repeatvalue) {
                                $notstarteduser->notstartedstop = 1;
                                $notstarteduser->modifiedtime = $runtime;
                                $DB->update_record('local_iomad_track', $notstarteduser);
                            }
                        }
                        if (empty($templateinfo->repeatperiod)) {
                            // Set to never so mark it to stop.
                            $notstarteduser->notstartedstop = 1;
                            $notstarteduser->modifiedtime = $runtime;
                            $DB->update_record('local_iomad_track', $notstarteduser);
                        }
                    }
                }
            }
        }
    }

    mtrace("sending expiry warning courses");

    // Deal with courses which have expiry warnings
    $notcompletedsql = "SELECT lit.*, c.name AS companyname, ic.notifyperiod, u.firstname,u.lastname,u.username,u.email,u.lang
                        FROM {local_iomad_track} lit
                        JOIN {company} c ON (lit.companyid = c.id)
                        JOIN {iomad_courses} ic ON (lit.courseid = ic.courseid)
                        JOIN {user} u ON (lit.userid = u.id)
                        WHERE ic.validlength > 0
                        AND ic.warnexpire > 0
                        AND (lit.timecompleted + ic.validlength * 86400 - ic.warnexpire * 86400) < " . $runtime . "
                        AND u.deleted = 0
                        AND u.suspended = 0
                        AND lit.expiredstop = 0
                        AND lit.id IN (
                            SELECT max(id) FROM {local_iomad_track}
                            GROUP BY userid,courseid)";

    mtrace("sending to users");

    // Email all of the users
    $allusers = $DB->get_records_sql($notcompletedsql);

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
            $event = \block_iomad_company_admin\event\user_course_expired::create(array('context' => context_course::instance($course->id),
                                                                                        'courseid' => $course->id,
                                                                                        'objectid' => $course->id,
                                                                                        'userid' => $user->id));
            $event->trigger();
        }

        // Get the company template info.
        // Check against per company template repeat instead.
        if ($templateinfo = $DB->get_record('email_template', array('companyid' => $notstarteduser->companyid, 'lang' => $userrec->lang, 'name' => 'expiry_warn_user'))) {
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
            $notifytime = $runtime - $warnnotstartedcourse->notifyperiod * 86400;
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

    mtrace("sending to managers");
    // Email the managers
    // Get the companies from the list of users in the temp table.
    $companysql = "SELECT DISTINCT lit.companyid 
                        FROM {local_iomad_track} lit
                        JOIN {iomad_courses} ic ON (lit.courseid = ic.courseid)
                        JOIN {user} u ON (lit.userid = u.id)
                        WHERE ic.validlength > 0
                        AND ic.warnexpire > 0
                        AND (lit.timecompleted + ic.validlength * 86400 - ic.warnexpire * 86400) < " . $runtime . "
                        AND u.deleted = 0
                        AND u.suspended = 0
                        AND lit.expiredstop = 0
                        AND lit.id IN (
                            SELECT max(id) FROM {local_iomad_track})";

    $companies = $DB->get_records_sql($companysql);
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
                    // Deparment managers dont get reports on company manager users.
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

    mtrace("getting expiry courses");
    // Deal with users who have passed the expired threshold.
    $completionexpirycourses = $DB->get_records_sql("SELECT * FROM {iomad_courses}
                                                     WHERE expireafter > 0");
    foreach ($completionexpirycourses as $completionexpirecourse) {
        // Get all of the users who have a time completed time > this time.
        $expiretime = 24 * 60 * 60 * $completionexpirecourse->expireafter;
        $userlist = $DB->get_records_sql("SELECT lit.* FROM
                                          {local_iomad_track} lit
                                          JOIN {user_enrolments} ue ON (lit.userid = ue.userid)
                                          JOIN {enrol} e ON (lit.courseid = e.courseid AND ue.enrolid = e.id)
                                          WHERE lit.courseid = :courseid
                                          AND lit.timecompleted + :expiretime < :runtime",
                                          array('courseid' => $completionexpirecourse->courseid,
                                                'expiretime' => $expiretime,
                                                'runtime' => $runtime));

        //  Cycle through any found users.
        foreach ($userlist as $founduser) {
            if (!$DB->get_record('local_iomad_track', array('userid' => $founduser->userid, 'courseid' => $founduser->courseid, 'timecompleted' => null))) {
                mtrace("expiring user $founduser->userid from course $founduser->courseid");
                // Expire the user from the course.
                $event = \block_iomad_company_admin\event\user_course_expired::create(array('context' => context_course::instance($founduser->courseid),
                                                                                            'courseid' => $founduser->courseid,
                                                                                            'objectid' => $founduser->courseid,
                                                                                            'userid' => $founduser->userid));
                $event->trigger();
            }
        }
    }

    mtrace("sending manager completion digests");
    // Deal with manager completion digests.
    // Get the companies from the list of users in the temp table.
    $companies = $DB->get_records_sql("SELECT id FROM {company}
                                       WHERE managerdigestday = :dayofweek
                                       AND managernotify in (2,3)",
                                       array('dayofweek' => $dayofweek));
    foreach ($companies as $company) {

        // Deal with parent companies as we only want manager of this company.
        $companyobj = new company($company->id);
        if ($parentslist = $companyobj->get_parent_companies_recursive()) {
            $companyusql = " AND u.id NOT IN (
                            SELECT userid FROM {company_users}
                            WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
            $companysql = " AND userid NOT IN (
                            SELECT userid FROM {company_users}
                            WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
        } else {
            $companyusql = "";
            $companysql = "";
        }

        $managers = $DB->get_records_sql("SELECT * FROM {company_users}
                                          WHERE companyid = :companyid
                                          AND managertype != 0
                                          $companysql", array('companyid' => $company->id));
        foreach ($managers as $manager) {
            // Deparment managers dont get reports on company manager users.
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
            $managerusers = $DB->get_records_sql("SELECT u.id AS userid, u.firstname, u.lastname, u.email, c.id AS courseid, c.fullname, cc.timecompleted, d.name AS departmentname
                                                  FROM {course_completions} cc
                                                  JOIN {user} u ON (cc.userid = u.id)
                                                  JOIN {course} c ON (cc.course = c.id)
                                                  JOIN {company_users} cu ON (u.id = cu.userid)
                                                  JOIN {department} d ON (cu.departmentid = d.id)
                                                  WHERE cc.userid IN (" . $departmentids . ")
                                                  AND cc.userid != :managerid
                                                  $companysql
                                                  AND cc.timecompleted > :weekago",
                                                  array('managerid' => $manager->userid, 'weekago' => $runtime - (60 * 60 * 24 * 7)));
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
                if ($departmentmanager && $DB->get_record('company_users', array('companyid' => $company->id, 'managertype' => 1, 'userid' => $manageruser->userid))) {
                    continue;
                }

                $summary = "<table><tr><th>" . get_string('firstname') . "</th>" .
                           "<th>" . get_string('lastname') . "</th>" .
                           "<th>" . get_string('email') . "</th>" .
                           "<th>" . get_string('department', 'block_iomad_company_admin') ."</th>";
                           "<th>" . get_string('course') . "</th>" .
                           "<th>" . get_string('completed', 'local_report_completion') ."</th></tr>";
                if ($managerusers = $DB->get_records_sql("SELECT u.firstname, u.lastname, u.email, c.fullname, cc.timecompleted
                                                          FROM {course_completions} cc
                                                          JOIN {user} u ON (cc.userid = u.id)
                                                          JOIN {course} c ON (cc.course = c.id)
                                                          WHERE cc.userid IN (" . $departmentids . ")
                                                          AND cc.timecompleted > :weekago",
                                                          array('weekago' => $timenow - (60 * 60 * 24 * 7)))) {
                    foreach ($managerusers as $manageruser) {
                        $datestring = date($CFG->iomad_date_format, $manageruser->timecompleted) . "\n";

                        $summary .= "<tr><td>" . $manageruser->firstname . "</td>" .
                                    "<td>" . $manageruser->lastname . "</td>" .
                                    "<td>" . $manageruser->email . "</td>" .
                                    "<td>" . $manageruser->departmentname . "</td>" .
                                    "<td>" . $manageruser->fullname . "</td>" .
                                    "<td>" . $datestring . "</td></tr>";
                    }
                    $summary .= "</table>";

                    if ($foundusers && $user = $DB->get_record('user', array('id' => $manager->userid))) {
                        $course = new stdclass();
                        $course->reporttext = $summary;
                        $course->id = 0;
                        mtrace("Sending completion summary report to $user->email");
                        EmailTemplate::send('completion_digest_manager', array('user' => $user, 'course' => $course, 'company' => $companyobj));
                    }
                }
            }
        }
    }

    mtrace("email reporting cron completed at " . date('D M Y h:m:s', time()));
}
