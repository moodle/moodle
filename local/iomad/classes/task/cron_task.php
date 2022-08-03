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
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad\task;

class cron_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'local_iomad');
    }

    /**
     * Run local_iomad cron.
     */
    public function execute() {
        global $DB, $CFG;

        // We need company stuff.
        require_once($CFG->dirroot . '/local/iomad/lib/company.php');

        $runtime = time();
        // Are we copying Company to institution?
        if (!empty($CFG->iomad_sync_institution)) {
            mtrace("Copying company shortnames to user institution fields\n");
            // Get the users where it's wrong.
            $users = $DB->get_records_sql("SELECT u.*, c.id as companyid
                                           FROM {user} u
                                           JOIN {company_users} cu ON cu.userid = u.id
                                           JOIN {company} c ON cu.companyid = c.id
                                           WHERE u.institution != c.shortname
                                           AND c.parentid = 0");
            // Get all of the companies.
            $companies = $DB->get_records('company', array(), '', 'id,shortname');
            foreach ($users as $user) {
                $user->institution = $companies[$user->companyid]->shortname;
                $DB->update_record('user', $user);
            }
            $companies = array();
            $users = array();
        }

        // Are we copying department to department?
        if (!empty($CFG->iomad_sync_department)) {
            mtrace("Copying company department name to user department fields\n");
            // Get the users where it's wrong.
            $users = $DB->get_records_sql("SELECT u.*, d.id as departmentid
                                           FROM {user} u
                                           JOIN {company_users} cu ON cu.userid = u.id
                                           JOIN {company} c ON cu.companyid = c.id
                                           JOIN {department} d ON cu.departmentid = d.id
                                           WHERE u.department != d.name
                                           AND c.parentid = 0");
            // Get all of the companies.
            $departments = $DB->get_records('department', array(), '', 'id,name');
            foreach ($users as $user) {
                $user->department = $departments[$user->departmentid]->name;
                $DB->update_record('user', $user);
            }
            $companies = array();
            $users = array();
        }

        // Suspend any companies which need it.
        mtrace("suspending any companies which need it");
        if ($suspendcompanies = $DB->get_records_sql("SELECT * FROM {company}
                                                      WHERE suspended = 0
                                                      AND validto IS NOT NULL
                                                      AND validto < :runtime",
                                                      array('runtime' => $runtime))) {
            foreach ($suspendcompanies as $suspendcompany) {
                $target = new \company($suspendcompany->id);
                $target->suspend(true);
            }
        }

        // Terminate any companies which need it.
        mtrace("Terminating any companies which need it");
        if ($terminatecompanies = $DB->get_records_sql("SELECT * FROM {company}
                                                        WHERE companyterminated = 0
                                                        AND validto IS NOT NULL
                                                        AND suspendafter > 0
                                                        AND validto + suspendafter < :runtime",
                                                        array('runtime' => $runtime))) {
            foreach ($suspendcompanies as $suspendcompany) {
                $target = new \company($suspendcompany->id);
                $target->terminate();
            }
        }

        // Clear users from courses where the license has expired and the option is chosen
        mtrace ("Clear users from courses where the license has expired and the option is chosen");
        if ($userlicenses = $DB->get_records_sql("SELECT clu.* FROM {companylicense_users} clu
                                                  JOIN {companylicense} cl on (clu.licenseid = cl.id)
                                                  WHERE cl.clearonexpire = 1
                                                  AND cl.cutoffdate < :time
                                                  AND clu.timecompleted IS NULL",
                                                  array('time' => $runtime))) {
            foreach ($userlicenses as $userlicense) {
                mtrace("Clearing userid $userlicense->userid from courseid $userlicense->licensecourseid");
                if ($userlicense->isusing == 1) {
                    \company_user::delete_user_course($userlicense->userid, $userlicense->licensecourseid, 'autodelete');
                } else {
                    $DB->delete_records('companylicense_users', array('id' => $userlicense->id));

                    // Create an event.
                    $eventother = array('licenseid' => $userlicense->licenseid,
                                        'duedate' => 0);
                    $event = \block_iomad_company_admin\event\user_license_unassigned::create(array('context' => \context_course::instance($userlicense->licensecourseid),
                                                                                                    'objectid' => $userlicense->licenseid,
                                                                                                    'courseid' => $userlicense->licensecourseid,
                                                                                                    'userid' => $userlicense->userid,
                                                                                                    'other' => $eventother));
                    $event->trigger();
                }
            }
        }
    }
}
