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
 * Scheduled task to revoke expired factors
 *
 * @package   factor_grace
 * @author    Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_grace\task;

/**
 * Scheduled task to revoke expired gracemode factors
 */
class revoke_expired_factors extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('revokeexpiredfactors', 'factor_grace');
    }

    /**
     * Execute the task.
     *
     * @return void
     */
    public function execute(): void {
        mtrace('Starting to revoke expired Grace factors');
        $this->revoke_factors();
    }

    /**
     * Revokes all grace factors that have a valid timecreated and are outside the duration.
     *
     * @return void
     */
    private function revoke_factors(): void {
        global $DB;

        $runtime = time();
        // IOMAD
        // Get all of the tenants
        $configuredtenants = [];
        $alltenants = $DB->get_records("SELECT id FROM {company} WHERE suspended = 0");
        foreach ($alltenants as $tenantid) {
            // If config is not set, pull out.
            $duration = get_config('factor_grace', 'graceperiod_' . $tenantid);
            if ($duration) {
                $configuredtenants[$tenantid] = $tenantid;
                $revoketime = $runtime - $duration;

                // Single query implementation.
                $sql = "UPDATE {tool_mfa}
                            SET revoked = 1,
                                timemodified = :timemodified
                            WHERE timecreated < :revoketime
                            AND factor = :factor
                            AND userid IN (
                                SELECT userid FROM {company_users}
                                 WHERE companyid = :companyid)";
                $DB->execute($sql, ['timemodified' => time(), 'revoketime' => $revoketime, 'factor' => 'grace', 'companyid' => $tenantid]);
            }
        }
        // If config is not set, pull out.
        $duration = get_config('factor_grace', 'graceperiod');
        if (!$duration) {
            mtrace('Gracemode duration is not set. Exiting...');
            return;
        }
        $revoketime = $runtime - $duration;

        // Single query implementation.
        $tenantsql = "";
        if (!empty($configuredtenants)) {
            $tenantsql = " AND userid NOT IN (
                              SELECT userid FROM {company_users}
                              WHERE companyid NOT IN (" . implode(',', array_keys($configuredtenants)) . "))";
            $sql = "UPDATE {tool_mfa}
                        SET revoked = 1,
                            timemodified = :timemodified
                        WHERE timecreated < :revoketime
                        AND factor = :factor
                        $tenantsql";
            $DB->execute($sql, ['timemodified' => time(), 'revoketime' => $revoketime, 'factor' => 'grace']);
        }
        mtrace('Finished revoking expired Grace factors');
    }
}
