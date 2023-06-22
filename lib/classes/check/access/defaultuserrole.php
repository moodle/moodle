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
 * Verifies sanity of default user role.
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\access;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * Verifies sanity of default user role.
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class defaultuserrole extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_defaultuserrole_name', 'report_security');
    }

    /**
     * A link to a place to action this
     *
     * @return action_link|null
     */
    public function get_action_link(): ?\action_link {
        global $CFG;
        return new \action_link(
            new \moodle_url('/admin/roles/define.php?action=view&roleid=' . $CFG->defaultuserroleid),
            get_string('userpolicies', 'admin'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $DB, $CFG;
        $details = '';

        if (!$defaultrole = $DB->get_record('role', ['id' => $CFG->defaultuserroleid])) {
            $status  = result::WARNING;
            $summary = get_string('check_defaultuserrole_notset', 'report_security');
            return new result($status, $summary, $details);
        }

        // Risky caps - usually very dangerous.
        $sql = "SELECT rc.contextid, rc.capability
                  FROM {role_capabilities} rc
                  JOIN {capabilities} cap ON cap.name = rc.capability
                 WHERE " . $DB->sql_bitand('cap.riskbitmask', (RISK_XSS | RISK_CONFIG | RISK_DATALOSS)) . " <> 0
                   AND rc.permission = :capallow
                   AND rc.roleid = :roleid";

        $riskyresults = $DB->get_records_sql($sql, [
            'capallow' => CAP_ALLOW,
            'roleid' => $defaultrole->id,
        ]);

        // If automatic approval is disabled, then the requestdelete capability is not risky.
        if (!get_config('tool_dataprivacy', 'automaticdatadeletionapproval')) {
            $riskyresults = array_filter($riskyresults, function ($object) {
                return $object->capability !== 'tool/dataprivacy:requestdelete';
            });
        }

        // Count the number of unique contexts that have risky caps.
        $riskycount = count(array_unique(array_column($riskyresults, 'contextid')));

        // It may have either none or 'user' archetype - nothing else, or else it would break during upgrades badly.
        if ($defaultrole->archetype === '' or $defaultrole->archetype === 'user') {
            $legacyok = true;
        } else {
            $legacyok = false;
        }

        if ($riskycount or !$legacyok) {
            $status = result::CRITICAL;
            $summary = get_string('check_defaultuserrole_error', 'report_security', role_get_name($defaultrole));

        } else {
            $status = result::OK;
            $summary = get_string('check_defaultuserrole_ok', 'report_security');
        }

        $details = get_string('check_defaultuserrole_details', 'report_security');
        return new result($status, $summary, $details);
    }
}

