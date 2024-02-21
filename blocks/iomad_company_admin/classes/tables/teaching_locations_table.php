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

namespace block_iomad_company_admin\tables;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use \table_sql;
use \iomad;
use \moodle_url;

class teaching_locations_table extends table_sql {


    /**
     * Generate the display of the teaching location name
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_name($row) {
        global $output;

        return format_string($row->name);
    }

    /**
     * Generate the display of the teaching location address
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_address($row) {
        global $output;

        if (!empty($row->isvirtual)) {
            return get_string('statusna');
        }

        $address = "";
        if (!empty($row->address)) {
            $address .= "<b>" . get_string('address') . ":</b> " . format_string($row->address) . "<br>";
        }
        if (!empty($row->city)) {
            $address .= "<b>" . get_string('city') . ":</b> " . format_string($row->city) . "<br>";
        }
        if (!empty($row->country)) {
            $address .= "<b>" . get_string('country') . ":</b> " . get_string($row->country, 'countries') . "<br>";
        }
        if (!empty($row->postcode)) {
            $address .= "<b>" . get_string('postcode', 'block_iomad_commerce') . ":</b> " . format_string($row->postcode) . "<br>";
        }
        return $address;
    }

    /**
     * Generate the display of the teaching location capacity
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_capacity($row) {
        global $output;

        if (!empty($row->isvirtual)) {
            return get_string('virtual', 'block_iomad_company_admin');
        }

        return format_string($row->capacity);
    }

    /**
     * Generate the display of the action column.
     * @param object $row the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $CFG, $OUTPUT, $DB, $USER, $params, $companycontext;

        $deletebutton = "";
        $editbutton = "";
        $sesskey = sesskey();

        if (iomad::has_capability('block/iomad_company_admin:classrooms_delete', $companycontext)) {
            $deleteurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_company_admin/classroom_list.php',
                                        ['delete' => $row->id,
                                        'sesskeyy' => $sesskey]);
            $deletebutton = "<a href='" . $deleteurl . "'><i class='icon fa fa-trash fa-fw' title='" . get_string('delete') . "' role='img' aria-label='" . get_string('delete') . "'></i></a>";
        }

        if (iomad::has_capability('block/iomad_company_admin:classrooms_edit', $companycontext)) {
            $editurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_company_admin/classroom_edit_form.php',
                                      ['id' => $row->id]);
            $editbutton = "<a href='" . $editurl . "'><i class='icon fa fa-cog fa-fw' title='" . get_string('edit') . "' role='img' aria-label='" . get_string('edit') . "'></i></a>";
        }

        return $editbutton . "&nbsp" . $deletebutton;

    }
}
