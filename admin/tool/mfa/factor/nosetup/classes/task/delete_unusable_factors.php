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
 * Scheduled task to revoke unusable factors that will never pass.
 *
 * @package   factor_nosetup
 * @author    Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_nosetup\task;

/**
 * Scheduled task to add log events into DB table.
 */
class delete_unusable_factors extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('deleteunusablefactors', 'factor_nosetup');
    }

    /**
     * Execute the task.
     *
     * @return void
     */
    public function execute(): void {
        mtrace('Starting to revoke unusable Nosetup factors');
        $this->revoke_factors();
    }

    /**
     * Revokes all nosetup factors that will now always fail.
     *
     * @return void
     */
    private function revoke_factors(): void {
        global $DB;

        $factorobject = \tool_mfa\plugininfo\factor::get_factor('nosetup');
        // We need to get all nosetup factors, and check that for ones that no longer have a pass state.
        $allfactorssql = "SELECT DISTINCT tm.userid
                            FROM {tool_mfa} tm
                            JOIN {user} u ON u.id = tm.userid
                           WHERE tm.factor = :factor
                             AND u.suspended = 0
                             AND u.deleted = 0
                             AND (
                                 SELECT COUNT(id) as count
                                   FROM {tool_mfa}
                                  WHERE userid = tm.userid
                                    AND factor <> :notfactor
                                 ) > 0";
        $useridrecordset = $DB->get_recordset_sql($allfactorssql, ['factor' => 'nosetup', 'notfactor' => 'nosetup']);

        foreach ($useridrecordset as $userid) {
            // If pass state is no longer possible, add delete user factor.
            $user = \core_user::get_user($userid->userid);
            if (!in_array(\tool_mfa\plugininfo\factor::STATE_PASS, $factorobject->possible_states($user))) {
                $factorobject->delete_factor_for_user($user);
            }
        }
        $useridrecordset->close();
        mtrace('Finished revoking unusable Nosetup factors');
    }
}
