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
 * Base class for the table used by block/iomad_microlearning/threads.php
 *
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_iomad_microlearning_nugget_table extends table_sql {

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
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_target($row) {
        global $CFG;

        if (!empty($row->active)) {
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
    public function col_updown($row) {
        global $CFG, $DB;

        $html = "";
        $count=$DB->count_records('microlearning_nugget', array('threadid' => $row->threadid));

        if ($row->nuggetorder != 0) {
            $uplink = new moodle_url('nuggets.php', array('action' => 'up', 'nuggetid' => $row->id, 'threadid' => $row->threadid));
            $html .= '<a href=" '. $uplink . '"><i class="icon fa fa-arrow-up fa-fw " title="' . get_string('up') . '" aria-label="'. get_string('up') . '"></i></a>';
        }
        if (($row->nuggetorder  + 1) < $count) {
            $downlink = new moodle_url('nuggets.php', array('action' => 'down', 'nuggetid' => $row->id, 'threadid' => $row->threadid));
            $html .= '<a href=" '. $downlink . '"><i class="icon fa fa-arrow-down fa-fw " title="' . get_string('down') . '" aria-label="'. get_string('down') . '"></i></a>';
        }
        return $html;
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_nuggetorder($row) {
        global $CFG, $DB;

        return $row->nuggetorder + 1;
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
        $deletelink = new moodle_url('nuggets.php', array('deleteid' => $row->id, 'threadid' => $row->threadid, 'sesskey' => sesskey()));
        $editlink = new moodle_url('nugget_edit.php', array('nuggetid' => $row->id, 'threadid' => $row->threadid));
        if (iomad::has_capability('block/iomad_microlearning:edit_nuggets', $context)) {
            $html = '<a href="' . $editlink . '"><i class="icon fa fa-cog fa-fw " title="' . get_string('edit') . '" aria-label="'. get_string('edit') . '"></i></a>';
            $html .= '<a href="' . $deletelink . '"><i class="icon fa fa-times fa-fw " title="' . get_string('delete') . '" aria-label="'. get_string('delete') . '"></i></a>';
        }
        return $html;
    }
}
