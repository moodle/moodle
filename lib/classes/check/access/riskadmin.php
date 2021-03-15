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
 * Lists all admins.
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
 * Lists all admins.
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class riskadmin extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_riskadmin_name', 'report_security');
    }

    /**
     * A link to a place to action this
     *
     * @return action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/admin/roles/admins.php'),
            get_string('siteadministrators', 'role'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $DB, $CFG;
        $userfieldsapi = \core_user\fields::for_userpic();
        $userfields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $sql = "SELECT $userfields
                  FROM {user} u
                 WHERE u.id IN ($CFG->siteadmins)";

        $admins = $DB->get_records_sql($sql);
        $admincount = count($admins);

        foreach ($admins as $uid => $user) {
            $url = "$CFG->wwwroot/user/view.php?id=$user->id";
            $link = \html_writer::link($url, fullname($user, true) . ' (' . s($user->email) . ')');
            $admins[$uid] = \html_writer::tag('li' , $link);
        }
        $admins = \html_writer::tag('ul', implode('', $admins));
        $status  = result::INFO;
        $summary = get_string('check_riskadmin_ok', 'report_security', $admincount);
        $details = get_string('check_riskadmin_detailsok', 'report_security', $admins);

        return new result($status, $summary, $details);
    }
}

