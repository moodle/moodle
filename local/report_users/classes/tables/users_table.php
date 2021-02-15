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
 * @package   local_report_user_license_allocations
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_report_users\tables;

use \table_sql;
use \moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class users_table extends table_sql {

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_firstname($row) {
        global $CFG;

        $userurl = '/local/report_users/userdisplay.php';
        if (!$this->is_downloading()) {
            return "<a href='".
                    new moodle_url($userurl, array('userid' => $row->id)).
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
        if (!$this->is_downloading()) {
            return "<a href='".
                    new moodle_url($userurl, array('userid' => $row->id)).
                    "'>$row->lastname</a>";
        } else {
            return $row->lastname;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_created($row) {
        global $CFG;

        if (!empty($row->created)) {
            return date($CFG->iomad_date_format, $row->created);
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_lastlogin($row) {
        global $CFG;

        if (!empty($row->lastlogin)) {
            return date($CFG->iomad_date_format, $row->lastlogin);
        } else {
            return get_string('never');
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timecompleted($row) {
        global $CFG;

        if (!empty($row->timecompleted)) {
            return date($CFG->iomad_date_format, $row->timecompleted);
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's course expiration timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timeexpires($row) {
        global $CFG;

        if (!empty($row->timeexpires)) {
            if ($icourserec = $DB->get_record_sql("SELECT * FROM {iomad_courses} WHERE courseid =: courseid AND expireafter !=0", array('courseid' => $row->courseid))) {
                $expiredate = $row->timecompleted + $icourserec->timeexpires * 24 * 60 * 60;
                return date($CFG->iomad_date_format, $expiredate);
            } else {
                return;
            }
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_finalscore($row) {
        global $CFG;

        if (!empty($row->finalscore)) {
            return round($row->finalscore, $CFG->iomad_report_grade_places)."%";
        } else {
            return;
        }
    }

}
