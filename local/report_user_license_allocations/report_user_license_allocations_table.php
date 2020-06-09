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
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

/**
 * Base class for the table used by local_report_user_license_allocations
 *
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_report_user_license_allocations_table extends table_sql {

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
                    new moodle_url($userurl, array('userid' => $row->id,
                                                   'courseid' => $row->courseid)).
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
                    new moodle_url($userurl, array('userid' => $row->id,
                                                   'courseid' => $row->courseid)).
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
    public function col_licenseallocated($row) {
        global $DB;
        $allocated = $DB->count_records('local_report_user_lic_allocs',
                                        array('userid' => $row->id,
                                              'licenseid' => $row->licenseid,
                                              'courseid' => $row->courseid,
                                              'action' => 1));
        $unallocated = $DB->count_records('local_report_user_lic_allocs',
                                        array('userid' => $row->id,
                                              'licenseid' => $row->licenseid,
                                              'courseid' => $row->courseid,
                                              'action' => 0));
        if ($allocated > $unallocated) {
            return get_string('yes');
        } else {
            return get_string('no');
        }
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_dateallocated($row) {
        global $CFG, $DB;

        $allocations = $DB->get_records('local_report_user_lic_allocs',
                                        array('userid' => $row->id,
                                              'licenseid' => $row->licenseid,
                                              'courseid' => $row->courseid,
                                              'action' => 1));
        $returnstring = "";

        // Process them.
        foreach ($allocations as $allocation) {
            $returnstring .= date($CFG->iomad_date_format, $allocation->issuedate) . "</br>";
        }

        return $returnstring;
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_dateunallocated($row) {
        global $CFG, $DB;

        $unallocations = $DB->get_records('local_report_user_lic_allocs',
                                          array('userid' => $row->id,
                                                'licenseid' => $row->licenseid,
                                                'courseid' => $row->courseid,
                                                'action' => 0));
        $returnstring = "";

        // Process them.
        foreach ($unallocations as $unallocation) {
            $returnstring .= date($CFG->iomad_date_format, $unallocation->issuedate) . "</br>";
        }

        return $returnstring;
    }

    /**
     * Generate the display of the user's licensename
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_licensename($row) {
        global $CFG, $DB;

        if ($row->licenseid == null) {
            $row->licenseid = 0;
        }
        $licenseurl = $CFG->wwwroot . "/local/report_license_usage/index.php";
	// Is the name valid?
	if (empty($row->licensename)) {
            // Try and get it from local_iomad_track table.
            if (!empty($row->licenseid) && $litinfos = $DB->get_records('local_iomad_track', array('licenseid' => $row->licenseid), '', '*', 0, 1)) {
                $litinfo = array_pop($litinfos);
                $row->licensename = $litinfo->licensename;
            } else {
                $row->licensename = "-"; 
            }
        }
        if (!$this->is_downloading() && iomad::has_capability('local/report_license_usage:view', context_system::instance())) {
            return  "<a href='".
                    new moodle_url($licenseurl, array('licenseid' => $row->licenseid)).
                    "'>" . format_string($row->licensename) . "</a>";
        } else {
            return format_string($row->licensename);
        }
    }

    /**
     * Generate the display of the user's license coursename
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_coursename($row) {
        global $CFG, $DB;

        $courseurl  = '/local/report_completion/index.php';
        if (!$this->is_downloading() && iomad::has_capability('local/report_completion:view', context_system::instance())) {
            return "<a href='".
                    new moodle_url($courseurl, array('courseid' => $row->courseid)).
                    "'>" . format_string($row->coursename, true, 1) . "</a>";
        } else {
            return format_string($row->coursename, true, 1);
        }
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_numallocations($row) {
        global $DB;

        return $DB->count_records('local_report_user_lic_allocs',
                                  array('userid' => $row->id,
                                        'licenseid' => $row->licenseid,
                                        'courseid' => $row->courseid,
                                        'action' => 1));
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_numunallocations($row) {
        global $DB;

        return $DB->count_records('local_report_user_lic_allocs',
                                  array('userid' => $row->id,
                                        'licenseid' => $row->licenseid,
                                        'courseid' => $row->courseid,
                                        'action' => 0));
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     */
    function query_db($pagesize, $useinitialsbar=true) {
        global $DB;
        if (!$this->is_downloading()) {
            if ($this->countsql === NULL) {
                $this->countsql = "SELECT DISTINCT " . $DB->sql_concat("u.id", $DB->sql_concat("'-'", $DB->sql_concat("urla.licenseid", $DB->sql_concat("'-'", "urla.courseid")))) . " AS caindex FROM " . $this->sql->from.' WHERE '.$this->sql->where;
                $this->countparams = $this->sql->params;
            }
            $subgrandtotal = $DB->get_records_sql($this->countsql, $this->countparams);
            $grandtotal = count($subgrandtotal);
            if ($useinitialsbar && !$this->is_downloading()) {
                $this->initialbars($grandtotal > $pagesize);
            }

            list($wsql, $wparams) = $this->get_sql_where();
            if ($wsql) {
                $this->countsql .= ' AND '.$wsql;
                $this->countparams = array_merge($this->countparams, $wparams);

                $this->sql->where .= ' AND '.$wsql;
                $this->sql->params = array_merge($this->sql->params, $wparams);

                $subtotal  = $DB->get_records_sql($this->countsql, $this->countparams);
                $total = count($subtotal);
            } else {
                $total = $grandtotal;
            }

            $this->pagesize($pagesize, $total);
        }

        // Fetch the attempts
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }
        $sql = "SELECT
                {$this->sql->fields}
                FROM {$this->sql->from}
                WHERE {$this->sql->where}
                {$sort}";

        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params, $this->get_page_start(), $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params);
        }
    }
}
