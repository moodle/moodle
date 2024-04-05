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

            // Get the users in multiple companies
            $multiusers = $DB->get_records_sql("SELECT userid
                                                FROM {company_users}
                                                GROUP BY userid
                                                HAVING COUNT(companyid) > 1");
            $multisql = "";
            $notmultisql = "";
            if (!empty($multiusers)) {
                $notmultisql = " AND u.id NOT IN (" . implode(',', array_keys($multiusers)) . ")";
                $multisql = " WHERE u.id IN (" . implode(',', array_keys($multiusers)) . ")";
            }
            if ($CFG->iomad_sync_institution == 1) {
                mtrace("Copying company shortnames to user institution fields\n");

                // Get the users where it's wrong.
                $users = $DB->get_records_sql("SELECT u.*, c.shortname as targetname
                                               FROM {user} u
                                               JOIN {company_users} cu ON cu.userid = u.id
                                               JOIN {company} c ON cu.companyid = c.id
                                               WHERE u.institution != c.shortname
                                               AND c.parentid = 0
                                               $notmultisql
                                               LIMIT 1000");

            } else if ($CFG->iomad_sync_institution == 2) {
                mtrace("Copying company name to user institution fields\n");

                // Get the users where it's wrong.
                $users = $DB->get_records_sql("SELECT u.id, c.name as targetname
                                               FROM {user} u
                                               JOIN {company_users} cu ON cu.userid = u.id
                                               JOIN {company} c ON cu.companyid = c.id
                                               WHERE u.institution != c.name
                                               AND c.parentid = 0
                                               $notmultisql
                                               LIMIT 1000");
            }

            // Update the users.
            foreach ($users as $user) {
                $DB->set_field('user', 'institution', $user->targetname, ['id' => $user->id]);
            }
            $users = [];
        }

        // Are we copying department to department?
        if (!empty($CFG->iomad_sync_department &&
            $CFG->iomad_sync_department == 1)) {
            mtrace("Copying company department name to user department fields\n");

            // Get the users where it's wrong.
            $multiusers = $DB->get_records_sql("SELECT userid
                                                FROM {company_users}
                                                GROUP BY userid
                                                HAVING COUNT(departmentid) > 1");
            $notmultisql = "";
            $multisql = "";
            if (!empty($multiusers)) {
                $notmultisql = " AND u.id NOT IN (" . implode(',', array_keys($multiusers)) . ")";
                $multisql = " WHERE u.id IN (" . implode(',', array_keys($multiusers)) . ")";
            }
            $users = $DB->get_records_sql("SELECT DISTINCT u.*, d.name as targetname
                                           FROM {user} u
                                           JOIN {company_users} cu ON cu.userid = u.id
                                           JOIN {company} c ON cu.companyid = c.id
                                           JOIN {department} d ON cu.departmentid = d.id
                                           WHERE u.department != d.name
                                           AND c.parentid = 0
                                           $notmultisql
                                           LIMIT 1000");
            // Update the users.
            foreach ($users as $user) {
                $DB->set_field('user', 'department', $user->targetname, ['id' => $user->id]);
            }

            // Deal with those in multiple departments.
            if (!empty($multiusers)) {
                $users = $DB->get_records_sql("SELECT DISTINCT u.*
                                               FROM {user} u
                                               $multisql");
                foreach ($users as $user) {
                    $string = get_string_manager()->get_string('blockmultiple', 'admin', '', $user->lang);
                    $user->department = $string;
                    $DB->update_record('user', $user);
                }
            }
            
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
                    // Get the corresponding entry from the LIT table.
                    if ($litrecs = $DB->get_records_select('local_iomad_track',
                                                           '*',
                                                           'userid = :userid
                                                            AND courseid = :courseid
                                                            AND licenseid =: licenseid
                                                            AND timecompleted IS NULL',
                                                           ['userid' => $userlicense->userid,
                                                            'courseid' => $userlicense->licensecourseid,
                                                            'licenseid' => $userlicense->licenseid])) {
                        foreach ($litrecs as $litrec) {
                            \company_user::delete_user_course($userlicense->userid, $userlicense->licensecourseid, 'autodelete', $litrec->id);
                        }
                    }
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
