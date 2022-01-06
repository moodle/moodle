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
 * Verifies sanity of frontpage role
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
 * Verifies sanity of frontpage role
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontpagerole extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_frontpagerole_name', 'report_security');
    }

    /**
     * A link to a place to action this
     *
     * @return action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/admin/settings.php?section=frontpagesettings#admin-defaultfrontpageroleid'),
            get_string('frontpagesettings', 'admin'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $DB, $CFG;

        if (!$frontpagerole = $DB->get_record('role', array('id' => $CFG->defaultfrontpageroleid))) {
            $status  = result::INFO;
            $summary = get_string('check_frontpagerole_notset', 'report_security');
            $details = get_string('check_frontpagerole_details', 'report_security');
            return new result($status, $summary, $details);
        }

        // Risky caps - usually very dangerous.
        $sql = "SELECT COUNT(DISTINCT rc.contextid)
                  FROM {role_capabilities} rc
                  JOIN {capabilities} cap ON cap.name = rc.capability
                 WHERE " . $DB->sql_bitand('cap.riskbitmask', (RISK_XSS | RISK_CONFIG | RISK_DATALOSS)) . " <> 0
                   AND rc.permission = :capallow
                   AND rc.roleid = :roleid";

        $riskycount = $DB->count_records_sql($sql, [
            'capallow' => CAP_ALLOW,
            'roleid' => $frontpagerole->id,
        ]);

        // There is no legacy role type for frontpage yet - anyway we can not allow teachers or admins there!
        if ($frontpagerole->archetype === 'teacher' or $frontpagerole->archetype === 'editingteacher'
          or $frontpagerole->archetype === 'coursecreator' or $frontpagerole->archetype === 'manager') {
            $legacyok = false;
        } else {
            $legacyok = true;
        }

        if ($riskycount or !$legacyok) {
            $status  = result::CRITICAL;
            $summary = get_string('check_frontpagerole_error', 'report_security', role_get_name($frontpagerole));

        } else {
            $status  = result::OK;
            $summary = get_string('check_frontpagerole_ok', 'report_security');
        }

        $details = get_string('check_frontpagerole_details', 'report_security');
        return new result($status, $summary, $details);
    }
}

