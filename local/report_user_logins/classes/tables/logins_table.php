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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class logins_table extends table_sql {

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_fullname($row) {
        global $params;

        $name = fullname($row, has_capability('moodle/site:viewfullnames', $this->get_context()));
        $userurl = '/local/report_users/userdisplay.php';

        if (!$this->is_downloading() && iomad::has_capability('local/report_users:view', context_system::instance())) {
            return "<a href='".
                    new moodle_url($userurl, ['userid' => $row->id]).
                    "'>$name</a>";
        } else {
            return $name;
        }
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_created($user) {
        global $CFG;

        return date($CFG->iomad_date_format, $user->created);
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_urlfirstlogin($user) {
        global $CFG;

        if ($user->urlfirstlogin == null) {
            return(get_string('never'));
        } else {
            return date($CFG->iomad_date_format, $user->urlfirstlogin);
        }
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_urllastlogin($user) {
        global $CFG;

        if ($user->urllastlogin == null) {
            return(get_string('never'));
        } else {
            return date($CFG->iomad_date_format, $user->urllastlogin);
        }
    }

    /**
     * Generate the display of the user's departments
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_department($row) {
        global $DB;

        $departments = $DB->get_records_sql("SELECT d.name FROM {department} d
                                             JOIN {company_users} cu
                                             ON (d.id = cu.departmentid)
                                             WHERE cu.userid = :userid
                                             AND cu.companyid = :companyid
                                             ORDER BY d.name",
                                             array('userid' => $row->id,
                                                   'companyid' => $row->companyid));
        $returnstr = "";
        $count = count($departments);
        $current = 1;
        if ($count > 5) {
            $returnstr = "<details><summary>" . get_string('show') . "</summary>";
        }

        foreach($departments as $department) {
            $returnstr .= format_string($department->name);
            if ($current < $count) {
                $returnstr .= ",</br>";
            }
            $current++;
        }

        if ($count > 5) {
            $returnstr .= "</details>";
        }

        return $returnstr;

    }

    /**
     * Generate the display of the user's companies
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_company($row) {
        global $DB;
        $companies = $DB->get_records_sql("SELECT DISTINCT c.name FROM {company} c
                                           JOIN {company_users} cu ON (c.id = cu.companyid)
                                           WHERE cu.userid = :userid",
                                           ['userid' => $row->id]);
        $returnstr = "";
        $count = count($companies);
        $current = 1;
        if ($count > 5) {
            $returnstr = "<details><summary>" . get_string('show') . "</summary>";
        }

        foreach($companies as $company) {
            $returnstr .= format_string($company->name);
            if ($current < $count) {
                $returnstr .= ",</br>";
            }
            $current++;
        }

        if ($count > 5) {
            $returnstr .= "</details>";
        }

        return $returnstr;

    }
}
