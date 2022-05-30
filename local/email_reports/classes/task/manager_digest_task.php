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

class manager_digest_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('manager_digest_task', 'local_email_reports');
    }

    /**
     * Run email course_not_started_task.
     */
    public function execute() {
        global $DB, $CFG;

        // Set some defaults.
        $runtime = time();
        $courses = array();
        $dayofweek = date('w', $runtime) + 1;

        // We only want the student role.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        mtrace("Running email report manager completion digest task at ".date('D M Y h:m:s', $runtime));

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
                                                      WHERE c.visible = 1
                                                      AND cc.userid IN (" . $departmentids . ")
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
                                                              WHERE c.visible = 1
                                                              AND cc.userid IN (" . $departmentids . ")
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
                            $course = (object) [];
                            $course->reporttext = $summary;
                            $course->id = 0;
                            mtrace("Sending completion summary report to $user->email");
                            EmailTemplate::send('completion_digest_manager', array('user' => $user, 'course' => $course, 'company' => $companyobj));
                        }
                    }
                }
            }
        }

        mtrace("email reporting manager digest task completed at " . date('D M Y h:m:s', time()));
    }

}