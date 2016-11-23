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
    global $DB;

    // Set some defaults.
    $runtime = time();
    $courses = array();

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
    $table->add_field('coursename', XMLDB_TYPE_CHAR, '254', XMLDB_UNSIGNED, null, null, null);
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
        if ($DB->get_records_sql("SELECT id FROM {email}
                                  WHERE userid = :userid
                                  AND courseid = :courseid
                                  AND templatename = :templatename
                                  AND SENT IS NULL
                                  OR sent > " . $runtime . " - " . $compuser->notifyperiod . " * 86400",
                                  array('userid' => $compuser->userid,
                                        'courseid' => $compuser->courseid,
                                        'templatename' => 'completion_warn_user'))) {
            continue;
        }
        mtrace("Sending completion warning email to $user->email");
        EmailTemplate::send('completion_warn_user', array('course' => $course, 'user' => $user, 'company' => $company));
    }

    // Email the managers
    // Get the companies from the list of users in the temp table.
    $companies = $DB->get_records_sql("SELECT DISTINCT companyid FROM {" . $tempcomptablename . "}");
    foreach ($companies as $company) {
        // Get the managers.
        $managers = $DB->get_records_sql("SELECT * FROM {company_users}
                                          WHERE companyid = :companyid
                                          AND managertype != 0", array('companyid' => $company->companyid));
        if (!$companyrec = $DB->get_record('company', array('id' => $company->companyid))) {
            continue;
        }
        foreach ($managers as $manager) {
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
                                                  WHERE userid IN (" . $departmentids . ")");
            $summary = get_string('firstname') . "," .
                       get_string('lastname') . "," .
                       get_string('email') . "," .
                       get_string('department', 'block_iomad_company_admin') ."\n";
                       get_string('course') . "," .
                       get_string('timeenrolled', 'local_report_completion') ."\n";
            $foundusers = false;
            foreach ($managerusers as $manageruser) {
                if (!$user = $DB->get_record('user', array('id' => $manageruser->userid))) {
                    continue;
                }
                if (!$course = $DB->get_record('course', array('id' => $manageruser->courseid))) {
                    continue;
                }
                if ($DB->get_records_sql("SELECT id FROM {email}
                                          WHERE userid = :userid
                                          AND courseid = :courseid
                                          AND templatename = :templatename
                                          AND sent > " . $runtime . " - " . $manageruser->notifyperiod . " * 86400",
                                          array('userid' => $manageruser->userid,
                                                'courseid' => $manageruser->courseid,
                                                'templatename' => 'completion_warn_user'))) {
                    continue;
                }
                $foundusers = true;
                $summary .= $manageruser->firstname . "," .
                            $manageruser->lastname . "," .
                            $manageruser->email . "," .
                            $manageruser->departmentname . "," .
                            $manageruser->coursename . "," .
                            date('d-m-y', $manageruser->timeenrolled) . "\n";
            }
            if ($foundusers && $user = $DB->get_record('user', array('id' => $manager->userid))) {
                $course = new stdclass();
                $course->reporttext = $summary;
                $course->id = 0;
                mtrace("Sending completion warning summary report to $user->email");
                EmailTemplate::send('completion_warn_manager', array('user' => $user, 'course' => $course, 'company' => $companyrec));
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
    $table->add_field('departmentname', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('coursename', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('firstname', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('lastname', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('email', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
    $table->add_field('username', XMLDB_TYPE_CHAR, '50', XMLDB_UNSIGNED, null, null, null);
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
        EmailTemplate::send('expiry_warn_user', array('course' => $course, 'user' => $user, 'company' => $company));
    }

    // Email the managers
    // Get the companies from the list of users in the temp table.
    $companies = $DB->get_records_sql("SELECT DISTINCT companyid FROM {" . $tempcomptablename ."}");
    foreach ($companies as $company) {
        // Get the managers.
        $managers = $DB->get_records_sql("SELECT * FROM {company_users}
                                          WHERE companyid = :companyid
                                          AND managertype != 0", array('companyid' => $company->companyid));
        if (!$companyrec = $DB->get_record('company', array('id' => $company->companyid))) {
            continue;
        }
        foreach ($managers as $manager) {
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
                                                  WHERE userid IN (" . $departmentids . ")");
            $summary = get_string('firstname') . "," .
                       get_string('lastname') . "," .
                       get_string('email') . "," .
                       get_string('department', 'block_iomad_company_admin') ."\n";
                       get_string('course') . "," .
                       get_string('completed', 'local_report_completion') ."\n";
            $foundusers = false;
            foreach ($managerusers as $manageruser) {
                if (!$user = $DB->get_record('user', array('id' => $manageruser->userid))) {
                    continue;
                }
                if (!$course = $DB->get_record('course', array('id' => $manageruser->courseid))) {
                    continue;
                }
                if ($DB->get_records_sql("SELECT id FROM {email}
                                          WHERE userid = :userid
                                          AND courseid = :courseid
                                          AND templatename = :templatename
                                          AND sent > " . $runtime . " - " . $manageruser->notifyperiod . " * 86400",
                                          array('userid' => $manageruser->userid,
                                                'courseid' => $manageruser->courseid,
                                                'templatename' => 'expiry_warn_user'))) {
                    continue;
                }
                $foundusers = true;
                $summary .= $manageruser->firstname . "," .
                            $manageruser->lastname . "," .
                            $manageruser->email . "," .
                            $manageruser->departmentname . "," .
                            $manageruser->coursename . "," .
                            date('d-m-y', $manageruser->timecompleted) . "\n";
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
    $dbman->drop_table($table);
}
