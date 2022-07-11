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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_microlearning\tables;

use \table_sql;
use \moodle_url;
use context_system;
use iomad;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for the table used by block/iomad_microlearning/threads.php
 *
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class thread_import_table extends table_sql {

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_name($row) {
        global $output;

        return format_string($row->name, true, 1);
    }

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_companyname($row) {
        global $output;

        return format_string($row->companyname, true, 1);
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_active($row) {
        global $CFG;

        if (empty($row->active)) {
            return get_string('no');
        } else {
            return get_string('yes');
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timecreated($row) {
        global $CFG;

        if (!empty($row->timecreated)) {
            return date($CFG->iomad_date_format, $row->timecreated);
        } else {
            return;
        }
    }

    public function col_startdate($row) {
        global $CFG;

        if (!empty($row->startdate)) {
            return date($CFG->iomad_date_format, $row->startdate);
        } else {
            return;
        }
    }

    /**
     * Generate the display of the actions
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $DB, $output;

        if ($this->is_downloading()) {
            return;
        }

        $html = "";
        $context = context_system::instance();
        $importlink = new moodle_url('thread_import.php', array('importid' => $row->id, 'sesskey' => sesskey()));
        if (iomad::has_capability('block/iomad_microlearning:import_threads', $context)) {
            $html .= '<a class="btn btn-primary" href="' . $importlink . '" title="' . get_string('import') .'">' . get_string('import') . '</a>';
        }

        return $html;
    }
}
