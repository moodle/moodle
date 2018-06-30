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
    $dayofweek = date('N', $runtime) + 1;

    // We only want the student role.
    $studentrole = $DB->get_record('role', array('shortname' => 'student'));


    mtrace("Running email report cron at ".date('D M Y h:m:s', $runtime));

    // Deal with courses which have completed by warnings
    // Generate the Temp table for storing the users.
    $tempcomptablename = uniqid('emailrep');

    $dbman = $DB->get_manager();

    // Define table user to be created.
    // We need, companyid, company name, departmentid, department name, userid, course id, course name, timeenrolled, lastrun.
    $table = new xmldb_table($tempcomptablename);
    $table->add_field('id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('departmentid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('notifyperiod', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('timeenrolled', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('companyname', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('departmentname', XMLDB_TYPE_CHAR, '255', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('coursename', XMLDB_TYPE_CHAR, '255', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('firstname', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('lastname', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('email', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('username', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    $dbman->create_temp_table($table);

    // Populate this table.
    $populatesql = "INSERT INTO {" . $tempcomptablename . "} (companyid, companyname, departmentid, departmentname, courseid,
                    coursename, notifyperiod, timeenrolled, userid, firstname, lastname, username, email)
                    SELECT co.id, co.name, d.id, d.name, c.id, c.fullname, ic.notifyperiod, cc.timeenrolled, u.id, u.firstname, u.lastname, u.username, u.email
                    FROM {iomad_courses} ic
                    JOIN {course_completions} cc
                    ON (ic.courseid = cc.course
                        AND cc.timecompleted IS NULL
                        AND ic.warncompletion > 0
                        AND cc.timeenrolled < " . $runtime . " - ic.warncompletion * 86400)
                    JOIN {company_users} cu
                    ON (cc.userid = cu.userid)
                    JOIN {company} co
                    ON (cu.companyid = co.id)
                    JOIN {department} d
                    ON (cu.departmentid = d.id)
                    JOIN {course} c
                    ON (ic.courseid = c.id)
                    JOIN {user} u
                    ON (cc.userid = u.id
                        AND u.deleted = 0
                        AND u.suspended = 0)";

    $DB->execute($populatesql);

    // Email all of the users.
    $allusers = $DB->get_records($tempcomptablename);

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
            continue;
        }
        if ($DB->get_records_sql("SELECT id FROM {email}
                                  WHERE userid = :userid
                                  AND courseid = :courseid
                                  AND templatename = :templatename
                                  AND (
                                     sent IS NULL
                                  OR sent > " . $runtime . " - " . $compuser->notifyperiod . " * 86400
                                  )",
                                  array('userid' => $compuser->userid,
                                        'courseid' => $compuser->courseid,
                                        'templatename' => 'completion_warn_user'))) {
            continue;
        }
        mtrace("Sending completion warning email to $user->email");

        EmailTemplate::send('completion_warn_user', array('course' => $course, 'user' => $user, 'company' => $company));

        // Send the supervisor email too.
        mtrace("Sending completion warning email to $user->email supervisor");
        company::send_supervisor_warning_email($user, $course);
    }

    // Email the managers
    // Get the companies from the list of users in the temp table.
    $companies = $DB->get_records_sql("SELECT DISTINCT companyid FROM {" . $tempcomptablename . "}");
    foreach ($companies as $company) {
        if (!$companyrec = $DB->get_record('company', array('id' => $company->companyid))) {
            continue;
        }

        if (!empty($companyrec->managernotify) && ($companyrec->managernotify == 1 || $companyrec->managernotify == 3)) {
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
                    if ($DB->get_records_sql("SELECT id FROM {company_users}
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
                                    "<td>" . $manageruser->departmentname . "</td>" .
                                    "<td>" . $manageruser->coursename . "</td>" .
                                    "<td>" . date($CFG->iomad_date_format, $manageruser->timeenrolled) . "</td></tr>";
                    }
                    $summary .= "</table>";
                    if ($foundusers && $user = $DB->get_record('user', array('id' => $manager->userid))) {
                        $course = new stdclass();
                        $course->reporttext = $summary;
                        $course->id = 0;
                        mtrace("Sending completion warning summary report to $user->email");
                        EmailTemplate::send('completion_warn_manager', array('user' => $user, 'course' => $course, 'company' => $companyrec));
                    }
                }
            }
        }
    }

    $dbman->drop_table($table);

    // Deal with courses which have expiry warnings
    $tempcomptablename = uniqid('emailrep');
    // Generate the Temp table for storing the users.

    $dbman = $DB->get_manager();

    // Define table user to be created.
    // We need, companyid, company name, departmentid, department name, userid, course id, course name, timeenrolled, lastrun.
    $table = new xmldb_table($tempcomptablename);
    $table->add_field('id', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('companyid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('departmentid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('notifyperiod', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('companyname', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('departmentname', XMLDB_TYPE_CHAR, '255', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('coursename', XMLDB_TYPE_CHAR, '255', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('firstname', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('lastname', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('email', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('username', XMLDB_TYPE_CHAR, '100', XMLDB_UNSIGNED, null, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    $dbman->create_temp_table($table);

    // Populate this table.
    $populatesql = "INSERT INTO {" . $tempcomptablename . "} (companyid, companyname, departmentid, departmentname, courseid,
                    coursename, notifyperiod, timecompleted, userid, firstname, lastname, username, email)
                    SELECT co.id, co.name, d.id, d.name, c.id, c.fullname, ic.notifyperiod, cc.timecompleted, u.id, u.firstname, u.lastname, u.username, u.email
                    FROM {iomad_courses} ic
                    JOIN {local_iomad_track} cc
                    ON (ic.courseid = cc.courseid
                        AND ic.validlength > 0
                        AND ic.warnexpire > 0
                        AND (cc.timecompleted + ic.validlength * 86400 - ic.warnexpire * 86400) < " . $runtime . ")
                    JOIN {company_users} cu
                    ON (cc.userid = cu.userid)
                    JOIN {company} co
                    ON (cu.companyid = co.id)
                    JOIN {department} d
                    ON (cu.departmentid = d.id)
                    JOIN {course} c
                    ON (ic.courseid = c.id)
                    JOIN {user} u
                    ON (cc.userid = u.id
                        AND u.deleted = 0
                        AND u.suspended = 0)
                    WHERE cc.id IN (
                        SELECT max(id) FROM {local_iomad_track}
                        GROUP BY userid,courseid)";

    $DB->execute($populatesql);

    // Email all of the users
    $allusers = $DB->get_records($tempcomptablename);

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
            continue;
        }
        if ($DB->get_records_sql("SELECT id FROM {email}
                                  WHERE userid = :userid
                                  AND courseid = :courseid
                                  AND templatename = :templatename
                                  AND sent IS NULL
                                  OR sent > " . $runtime . " - " . $compuser->notifyperiod . " * 86400",
                                  array('userid' => $compuser->userid,
                                        'courseid' => $compuser->courseid,
                                        'templatename' => 'expiry_warn_user'))) {
            continue;
        }
        mtrace("Sending expiry warning email to $user->email");
        $event = \block_iomad_company_admin\event\user_course_expired::create(array('context' => context_course::instance($course->id),
                                                                                    'courseid' => $course->id,
                                                                                    'objectid' => $course->id,
                                                                                    'userid' => $user->id));
        $event->trigger();
        EmailTemplate::send('expiry_warn_user', array('course' => $course, 'user' => $user, 'company' => $company));
        // Send the supervisor email too.
        mtrace("Sending supervisor warning email for $user->email");
        company::send_supervisor_expiry_warning_email($user, $course);
    }

    // Email the managers
    // Get the companies from the list of users in the temp table.
    $companies = $DB->get_records_sql("SELECT DISTINCT companyid FROM {" . $tempcomptablename ."}");
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
                    if ($DB->get_records_sql("SELECT id FROM {company_users}
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
                        EmailTemplate::send('expiry_warn_manager', array('user' => $user, 'course' => $course, 'company' => $companyrec));
                    }
                }
            }
        }
    }

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
                                    "<td>" . $manageruser->coursename . "</td>" .
                                    "<td>" . $datestring . "</td></tr>";
                    }
                    $summary .= "</table>";
                    
                    if ($foundusers && $user = $DB->get_record('user', array('id' => $manager->userid))) {
                        $course = new stdclass();
                        $course->reporttext = $summary;
                        $course->id = 0;
                        mtrace("Sending completion summary report to $user->email");
                        EmailTemplate::send('completion_digest_manager', array('user' => $user, 'course' => $course, 'company' => $company));
                    }
                }
            }
        }
    }

    // Drop the temp database table.
    $dbman->drop_table($table);
}
