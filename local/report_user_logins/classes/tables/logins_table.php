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
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_firstname($row) {
        global $CFG;

        $userurl = '/local/report_users/userdisplay.php';
        if (!$this->is_downloading() && iomad::has_capability('local/report_users:view', context_system::instance())) {
            return "<a href='".
                    new moodle_url($userurl, array('userid' => $row->id)) .
                    "'>$row->firstname</a>";
        } else {
            return $row->firstname;
        }
    }

    /**
     * Generate the display of the user's lastname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_lastname($row) {
        global $CFG;

        $userurl = '/local/report_users/userdisplay.php';
        if (!$this->is_downloading() && iomad::has_capability('local/report_users:view', context_system::instance())) {
            return "<a href='".
                    new moodle_url($userurl, array('userid' => $row->id)).
                    "'>$row->lastname</a>";
        } else {
            return $row->lastname;
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
    public function col_firstlogin($user) {
        global $CFG;

        if ($user->firstlogin == null) {
            return(get_string('never'));
        } else {
            return date($CFG->iomad_date_format, $user->firstlogin);
        }
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_lastlogin($user) {
        global $CFG;

        if ($user->lastlogin == null) {
            return(get_string('never'));
        } else {
            return date($CFG->iomad_date_format, $user->lastlogin);
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
}
