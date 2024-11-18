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

//require_once($CFG->dirroot . '/local/iomad/lib/company.php');

class company_license_expiring_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('company_license_expiring_task', 'local_email_reports');
    }

    /**
     * Run email company_license_expiring_task.
     */
    public function execute() {
        global $DB, $CFG;

        // Set some defaults.
        $runtime = time();
        $courses = array();
        $dayofweek = date('w', $runtime) + 1;

        mtrace("Running email report company license expiring task at ".date('d M Y h:i:s', $runtime));

        // Get all of the licenses which are going to expire in the next 30 days and have un-unsed slots.
        $licenses = $DB->get_records_sql("SELECT * FROM {companylicense}
                                          WHERE used < allocated
                                          AND expirydate > :now
                                          AND expirydate < :warn",
                                          ['now' => $runtime,
                                           'warn' => $runtime + 30 * 24 * 60 * 60]);
        // Process any we found.
        foreach ($licenses as $license) {
            $company = new company($license->companyid);
            $companyusql = "";
            $companysql = "";

            // Only want company managers not parent company managers.
            if ($parentslist = $company->get_parent_companies_recursive()) {
                $companyusql = " AND u.id NOT IN (
                                SELECT userid FROM {company_users}
                                WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
                $companysql = " AND userid NOT IN (
                                SELECT userid FROM {company_users}
                                WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
            }

            $managers = $DB->get_records_sql("SELECT * FROM {company_users}
                                              WHERE companyid = :companyid
                                              AND managertype = 1
                                              $companysql",
                                              ['companyid' => $company->id]);
            foreach ($managers as $manager) {
                if ($user = $DB->get_record('user', ['id' => $manager->userid, 'deleted' => 0, 'suspended' => 0])) {

                    $license->expirydate =  date($CFG->iomad_date_format, $license->expirydate);
                    // Passed all checks, send the email.
                    mtrace("Sending license pool expiring email to $user->email");
                    EmailTemplate::send('licensepoolexpiringq', array('user' => $user, 'license' => $license, 'company' => $company));
                }
            }
        }

        mtrace("email reporting training event not selected completed at " . date('d M Y h:i:s', time()));
    }

}
