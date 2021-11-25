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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class list_groups_table extends table_sql {

    /**
     * Generate the display of the thread name.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_threadname($row) {
        return format_string($row->threadname);
    }

    /**
     * Generate the display of the group name.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_name($row) {

        return format_string($row->name);
    }

    /**
     * Generate the display of the action items
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $CFG;

        $deleteurl = new \moodle_url($CFG->wwwroot . '/blocks/iomad_microlearning/groups.php', ['deleteid' => $row->id, 'sesskey' => sesskey()]);
        $editurl = new \moodle_url($CFG->wwwroot . '/blocks/iomad_microlearning/group_edit_form.php', ['id' => $row->id]);
        return "<a href='" . $editurl . "' class='btn'>" . get_string('edit') . "</a>&nbsp<a href='" . $deleteurl . "' class='btn btn-danger'>" . get_string('delete') . "</a>";
    }

}
