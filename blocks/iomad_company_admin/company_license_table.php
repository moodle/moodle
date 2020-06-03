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
 * @copyright 2020 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class company_license_table extends table_sql {

    /**
     * Generate the display of the licenses type
     * @param object $license the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_type($row) {
        $licensetypes = array(get_string('standard', 'block_iomad_company_admin'),
                              get_string('reusable', 'block_iomad_company_admin'),
                              get_string('educator', 'block_iomad_company_admin'),
                              get_string('educatorreusable', 'block_iomad_company_admin'));        $return = "";
        return $licensetypes[$row->type];
    }

    /**
     * Generate the display of the license program value
     * @param object $license the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_program($row) {
        global $output;

        if (!empty($row->program)) {
            return get_string('yes');
        } else {
            return get_string('no');
        }
    }

    /**
     * Generate the display of the license instant value
     * @param object $license the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_instant($row) {
        global $output;

        if (!empty($row->instant)) {
            return get_string('yes');
        } else {
            return get_string('no');
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_coursesname($row) {
        global $OUTPUT, $DB;

        $licensecourses = $DB->get_records('companylicense_courses', array('licenseid' => $row->id));
        $coursestring = "";
        if (is_siteadmin()) {
            $issiteadmin = true;
        } else {
            $issiteadmin = false;
        }
        $coursestring = "";
        $first = true;
        if (count($licensecourses) > 5) {
            $coursestring = "<details><summary>" . get_string('view') . "</summary>";
        }
        foreach ($licensecourses as $licensecourse) {
            $coursename = $DB->get_record('course', array('id' => $licensecourse->courseid));
            if ($first) {
                if ($issiteadmin) {
                    $coursestring .= "<a href='".new moodle_url('/course/view.php',
                                       array('id' => $licensecourse->courseid))."'>".format_string($coursename->fullname, true, 1)."</a>";
                    $first = false;
                } else {
                    $coursestring .= format_string($coursename->fullname, true, 1);
                }
            } else {
                if ($issiteadmin) {
                    $coursestring .= ",</br><a href='".new moodle_url('/course/view.php',
                                   array('id' => $licensecourse->courseid))."'>".format_string($coursename->fullname, true, 1)."</a>";
                } else {
                    $coursestring .= ",</br>". format_string($coursename->fullname, true, 1);
                }
            }
        }
        if (count($licensecourses) > 5) {
            $coursestring .= "</details>";
        }

        return $coursestring;
    }

    /**
     * Generate the display of the license instant value
     * @param object $license the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_expirydate($row) {
        global $CFG, $output;

        return date($CFG->iomad_date_format, $row->expirydate);
    }

    /**
     * Generate the display of the license instant value
     * @param object $license the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_validlength($row) {
        global $output;

        // Deal with valid length if a subscription.
        if ($row->type == 1) {
            return "-";
        } else {
            return $row->validlength;
        }
    }

    /**
     * Generate the display of the license instant value
     * @param object $license the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_used($row) {
        global $DB, $output;

        $licensecourses = $DB->get_records('companylicense_courses', array('licenseid' => $row->id));

        // Deal with allocation numbers if a program.
        if (!empty($license->program)) {
            return $row->used / count($licensecourses);
        } else {
            return $row->used;
        }
    }

    /**
     * Generate the display of the company name
     * @param object $license the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_companyname($row) {

        return format_string($row->companyname);
    }

    /**
     * Generate the display of the ucourses has grade column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $OUTPUT, $params, $context, $gotchildren, $departmentid;

        $stredit   = get_string('edit');
        $strdelete = get_string('delete');
        $strallocate = get_string('licenseallocate', 'block_iomad_company_admin');
        $strsplit = get_string('split', 'block_iomad_company_admin');

        // Set up the edit buttons.
        $deletebutton = "";
        $editbutton = "";
        $allocatebutton = "";

    // Set up the edit buttons.
    if ((iomad::has_capability('block/iomad_company_admin:edit_licenses', $context) ||
        iomad::has_capability('block/iomad_company_admin:edit_my_licenses', $context) ||
        iomad::has_capability('block/iomad_company_admin:split_my_licenses', $context)) &&
        $row->used < $row->allocation &&
        $gotchildren) {
        $splitbutton = "<a class='btn btn-primary' href='" . new moodle_url('company_license_edit_form.php',
                       array("parentid" => $row->id)) . "'>$strsplit</a>";
    } else {
        $splitbutton = "";
    }
        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context) ||
            (iomad::has_capability('block/iomad_company_admin:edit_my_licenses', $context) && !empty($row->parentid))) {
                // Is this above the user's company allocation?
                if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context) ||
                    $DB->get_record_sql("SELECT id FROM {company_users}
                                         WHERE userid = :userid
                                         AND companyid = (
                                            SELECT companyid FROM {companylicense}
                                            WHERE id = :parentid)",
                                         array('userid' => $USER->id,
                                               'parentid' => $license->parentid))) {
                $deletebutton = "<a class='btn btn-primary' href='".
                                 new moodle_url('company_license_list.php', array('delete' => $row->id,
                                                                                  'sesskey' => sesskey())) ."'>$strdelete</a>";
                $editbutton = "<a class='btn btn-primary' href='" . new moodle_url('company_license_edit_form.php',
                               array("licenseid" => $row->id, 'departmentid' => $departmentid)) . "'>$stredit</a>";
            }
        }

        if (iomad::has_capability('block/iomad_company_admin:allocate_licenses', $context)) {
            $allocatebutton = "<a class='btn btn-primary' href='".
                                 new moodle_url('company_license_users_form.php', array('licenseid' => $row->id)) ."'>$strallocate</a>";
        } else {
            $allocatebutton = "";
        }

        $actionsoutput = $editbutton . ' ' . $splitbutton . ' ' . $deletebutton . ' ' . $allocatebutton;
        return $actionsoutput;

    }
}
