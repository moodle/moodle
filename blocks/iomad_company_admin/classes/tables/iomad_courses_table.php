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
use \single_select;
use \html_writer;

class iomad_courses_table extends table_sql {

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_company($row) {
        global $output, $DB;

        $companies = $DB->get_records_sql("SELECT c.id,c.shortname FROM {company} c
                                           JOIN {company_course} cc ON (c.id = cc.companyid)
                                           WHERE cc.courseid = :courseid",
                                           array('courseid' => $row->courseid));
        $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";

        $return = "";
        $first = true;
        foreach ($companies as $company) {
            if ($first) {
                $return .= "<a href='" . new moodle_url($linkurl, array('companyid' => $company->id)) .
                           "'>$company->shortname</a>";
                $first = false;
            } else {
                $return .= ",<a href='" . new moodle_url($linkurl, array('companyid' => $company->id)) .
                           "'>$company->shortname</a>";
            }
        }
        return $return;
    }

    /**
     * Generate the display of the user's lastname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_coursename($row) {
        global $output;

        $courseurl = "/course/view.php";
        return "<a href='" . new moodle_url($courseurl, array('id' => $row->courseid)) .
               "'>" . format_string($row->coursename, true, 1) . "</a>";

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_licensed($row) {
        global $USER, $systemcontext, $company, $OUTPUT, $DB;

        // Deal with self enrol.
        if ($DB->get_record('enrol', array('courseid' => $row->courseid, 'enrol' => 'self', 'status' => 0))) {
            $row->licensed = 3;
            $licenseselectoutput = get_string('pluginname', 'enrol_self');
        }

        if (!empty($USER->editing) &&
        iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_license_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->licensed);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {
            if ($row->licensed == 0) {
                $licenseselectoutput = get_string('no');
            } else if ($row->licensed == 1) {
                $licenseselectoutput = get_string('yes');
            }
        }

        return $licenseselectoutput;
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_shared($row) {
        global $USER, $systemcontext, $company, $OUTPUT, $DB;

        $sharedselectoptions = array('0' => get_string('no'),
                                    '1' => get_string('open', 'block_iomad_company_admin'),
                                    '2' => get_string('closed', 'block_iomad_company_admin'));

        if (!empty($USER->editing) &&
        iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_shared_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->shared);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {

            return $sharedselectoptions[$row->shared];
        }

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_validlength($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {
            $editable = new \block_iomad_company_admin\output\courses_validlength_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->validlength);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {

            return $row->validlength;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_expireafter($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\enrolment_expireafter_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->expireafter);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {

            return $row->expireafter;
        }
    }

    /**
     * Generate the display of the warn expiry time.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warnexpire($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_warnexpire_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->warnexpire);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {
            return $row->warnexpire;
        }

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warnnotstarted($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_warnnotstarted_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->warnnotstarted);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {
            return $row->warnnotstarted;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warncompletion($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_warncompletion_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->warncompletion);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {
            return $row->warncompletion;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_notifyperiod($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_notifyperiod_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->notifyperiod);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {
            return $row->notifyperiod;
        }
    }

    /**
     * Generate the display of the ucourses has grade column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_hasgrade($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {
            $editable = new \block_iomad_company_admin\output\courses_hasgrade_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->hasgrade);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));


        } else {
            if ($row->hasgrade) {
                return get_string('yes');
            } else {
                return get_string('no');
            }
        }

    }

    /**
     * Generate the display of the ucourses has grade column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $OUTPUT, $params, $systemcontext, $USER;

        $actionsoutput = "";

        if (!empty($USER->editing)) {
            if ($row->shared == 0 && 
                (iomad::has_capability('block/iomad_company_admin:deletecourses', $systemcontext) ||
                iomad::has_capability('block/iomad_company_admin:deletecourses', $systemcontext))) {
                $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";
                $linkparams = $params;
                if (!empty($params['coursesearchtext'])) {
                    $linkparams['coursesearch'] = $params['coursesearchtext'];
                }
                $linkparams['deleteid'] = $row->courseid;
                $linkparams['sesskey'] = sesskey();
                $deleteurl = new moodle_url($linkurl, $linkparams);
                $actionsoutput = html_writer::start_tag('div');
                $actionsoutput .= "<a href='$deleteurl'><i class='icon fa fa-trash fa-fw ' title='" . get_string('delete') . "' role='img' aria-label='" . get_string('delete') . "'></i></a>";
                $actionsoutput .= html_writer::end_tag('div');
    
            } else if (iomad::has_capability('block/iomad_company_admin:deleteallcourses', $systemcontext)) {
                $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";
                $linkparams = $params;
                if (!empty($params['coursesearchtext'])) {
                    $linkparams['coursesearch'] = $params['coursesearchtext'];
                }
                $linkparams['deleteid'] = $row->courseid;
                $linkparams['sesskey'] = sesskey();
                $deleteurl = new moodle_url($linkurl, $linkparams);
                $actionsoutput = html_writer::start_tag('div');
                $actionsoutput .= "<a href='$deleteurl'><i class='icon fa fa-trash fa-fw ' title='" . get_string('delete') . "' role='img' aria-label='" . get_string('delete') . "'></i></a>";
                $actionsoutput .= html_writer::end_tag('div');
            }
        }

        return $actionsoutput;

    }
}
