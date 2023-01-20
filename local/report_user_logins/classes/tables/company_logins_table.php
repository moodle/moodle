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
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @package   local_report_user_logins
 * @copyright 2012 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_report_user_logins\tables;

use \table_sql;
use \iomad;
use \context_system;
use \moodle_url;
use \company;
use \html_writer;   

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class company_logins_table extends table_sql {

    /**
     * Gnerates the formatted string of the company name
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_name($row) {
        global $CFG, $params;

        $parentlevel = company::get_company_parentnode($row->id);
        $params['deptid'] = $parentlevel->id;
        $params['showsummary'] = false;
        $url = new moodle_url('/local/report_user_logins/index.php', $params);
        return html_writer::tag('a', format_string($row->name), ['href' => $url]);
    }

    /**
     * Generate the total number of users in the company
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_total($row) {
        global $CFG, $DB;

        $totalusers = $DB->get_record_sql("SELECT count(u.id) AS number FROM {user} u
                                            JOIN {company_users} cu ON (u.id = cu.userid)
                                            WHERE u.deleted = 0
                                            AND u.confirmed = 1
                                            and cu.companyid = :companyid",
                                            ['companyid' => $row->id]);
        return $totalusers->number;
    }

    /**
     * Generate the total number of users in the company
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_real($row) {
        global $CFG, $DB;

        $realusers = $DB->get_record_sql("SELECT count(u.id) AS number FROM {user} u
                                           JOIN {company_users} cu ON (u.id = cu.userid)
                                           JOIN {local_report_user_logins} lrul ON (u.id = lrul.userid AND cu.userid = lrul.userid)
                                           WHERE u.deleted = 0
                                           AND u.confirmed = 1
                                           AND lrul.logincount > 0
                                           and cu.companyid = :companyid",
                                           ['companyid' => $row->id]);
         
        return $realusers->number;
    }

    /**
     * Generate the total number of users in the company
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_percentage($row) {
        global $CFG, $DB;

        $totalusers = $DB->get_record_sql("SELECT count(u.id) AS number FROM {user} u
                                            JOIN {company_users} cu ON (u.id = cu.userid)
                                            WHERE u.deleted = 0
                                            AND u.confirmed = 1
                                            and cu.companyid = :companyid",
                                            ['companyid' => $row->id]);
         
        $realusers = $DB->get_record_sql("SELECT count(u.id) AS number FROM {user} u
                                           JOIN {company_users} cu ON (u.id = cu.userid)
                                           JOIN {local_report_user_logins} lrul ON (u.id = lrul.userid AND cu.userid = lrul.userid)
                                           WHERE u.deleted = 0
                                           AND u.confirmed = 1
                                           AND lrul.logincount > 0
                                           and cu.companyid = :companyid",
                                           ['companyid' => $row->id]);
        if (!empty($totalusers->number)) {        
            return get_string('percents', 'moodle', number_format(($realusers->number / $totalusers->number) * 100, 2));
        } else {
            return 0;
        }
    }
}
