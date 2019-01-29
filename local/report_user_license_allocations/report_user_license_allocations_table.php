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
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_licenseallocated($row) {
        global $DB;
        $allocated = $DB->count_records('local_report_user_lic_allocs',
                                        array('userid' => $row->id,
                                              'licenseid' => $row->licenseid,
                                              'action' => 1));
        $unallocated = $DB->count_records('local_report_user_lic_allocs',
                                        array('userid' => $row->id,
                                              'licenseid' => $row->licenseid,
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
                                              'action' => 1));
        $returnstring = "";

        // Process them.
        foreach ($allocations as $allocation) {
            $returnstring .= date($CFG->iomad_date_format, $allocation->date) . "</br>";
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
                                                'action' => 0));
        $returnstring = "";

        // Process them.
        foreach ($unallocations as $unallocation) {
            $returnstring .= date($CFG->iomad_date_format, $unallocation->date) . "</br>";
        }

        return $returnstring;
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
                                        'action' => 0));
    }
}