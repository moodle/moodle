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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
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
class block_iomad_microlearning_thread_table extends table_sql {

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
        $deletelink = new moodle_url('threads.php', array('deleteid' => $row->id, 'sesskey' => sesskey()));
        $clonelink = new moodle_url('threads.php', array('cloneid' => $row->id, 'sesskey' => sesskey()));
        $editlink = new moodle_url('thread_edit.php', array('threadid' => $row->id));
        $nuggetlink = new moodle_url('nuggets.php', array('threadid' => $row->id));
        $userlink = new moodle_url('users.php', array('threadid' => $row->id));
        $schedulelink = new moodle_url('thread_schedule.php', array('threadid' => $row->id));
        if (iomad::has_capability('block/iomad_microlearning:edit_threads', $context)) {
            $html .= '<a href="' . $editlink . '" title="' . get_string('editthread', 'block_iomad_microlearning') .'"><i class="fa fa-cog"></i></a>&nbsp';
        }
        if (iomad::has_capability('block/iomad_microlearning:edit_nuggets', $context)) {
            $html .= '<a href="' . $nuggetlink . '" title="' . get_string('learningnuggets', 'block_iomad_microlearning') .'"><i class="fa fa-microchip"></i></a>&nbsp';
        }
        if (iomad::has_capability('block/iomad_microlearning:edit_threads', $context)) {
            $html .= '<a href="' . $schedulelink . '" title="' . get_string('threadschedule', 'block_iomad_microlearning') .'"><i class="fa fa-list-alt"></i></a>&nbsp';
        }
        if (iomad::has_capability('block/iomad_microlearning:assign_threads', $context)) {
            $html .= '<a href="' . $userlink . '" title="' . get_string('learningusers', 'block_iomad_microlearning') .'"><i class="fa fa-group"></i></a>&nbsp';
        }
        if (iomad::has_capability('block/iomad_microlearning:thread_clone', $context)) {
            $html .= '<a href="' . $clonelink . '" title="' . get_string('clonethread', 'block_iomad_microlearning') .'"><i class="fa fa-clone"></i></a>';
        }
        if (iomad::has_capability('block/iomad_microlearning:thread_delete', $context)) {
            $html .= '<a href="' . $deletelink . '" title="' . get_string('deletethread', 'block_iomad_microlearning') .'"><i class="fa fa-times"></i></a>';
        }

        return $html;
    }
}
