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
               "'>$row->coursename</a>";

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_licensed($row) {
        global $OUTPUT, $params;

        $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";
        $licenseselectbutton = array('0' => get_string('no'), '1' => get_string('yes'), '3' => get_string('pluginname', 'enrol_self'));

        $linkparams = $params;
        if (!empty($params['coursesearchtext'])) {
            $linkparams['coursesearch'] = $params['coursesearchtext'];
        }
        $linkparams['courseid'] = $row->courseid;
        $linkparams['update'] = 'license';
        $licenseurl = new moodle_url($linkurl, $linkparams);
        $licenseselect = new single_select($licenseurl, 'license', $licenseselectbutton, $row->licensed);
        $licenseselect->label = '';
        $licenseselect->formid = 'licenseselect'.$row->courseid;
        $licenseselectoutput = html_writer::tag('div', $OUTPUT->render($licenseselect), array('id' => 'license_selector'.$row->courseid));

        return $licenseselectoutput;
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_shared($row) {
        global $OUTPUT, $params;

        $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";
        $sharedselectbutton = array('0' => get_string('no'),
                                    '1' => get_string('open', 'block_iomad_company_admin'),
                                    '2' => get_string('closed', 'block_iomad_company_admin'));

        $linkparams = $params;
        if (!empty($params['coursesearchtext'])) {
            $linkparams['coursesearch'] = $params['coursesearchtext'];
        }
        $linkparams['courseid'] = $row->courseid;
        $linkparams['update'] = 'shared';
        $sharedurl = new moodle_url($linkurl, $linkparams);
        $sharedselect = new single_select($sharedurl, 'shared', $sharedselectbutton, $row->shared);
        $sharedselect->label = '';
        $sharedselect->formid = 'sharedselect'.$row->courseid;
        $sharedselectoutput = html_writer::tag('div', $OUTPUT->render($sharedselect), array('id' => 'shared_selector'.$row->courseid));

        return $sharedselectoutput;

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_validlength($row) {
        global $output, $CFG, $DB, $params;

        if (!empty($params['coursesearchtext'])) {
            $coursesearch = '<input type="hidden" name="coursesearch" value="'.$params['coursesearchtext'].'" />';
        } else {
            $coursesearch = '';
        }

        return '<form action="iomad_courses_form.php" method="get">
                <input type="hidden" name="courseid" value="' . $row->courseid . '" />
                <input type="hidden" name="companyid" value="'.$row->companyid.'" />'.
                $coursesearch .'
               <input type="hidden" name="update" value="validfor" />
               <input type="text" name="validfor" id="id_validfor" value="'.$row->validlength.'" size="10"/>
               <input type="submit" value="' . get_string('submit') . '" />
               </form>';

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_expireafter($row) {
        global $output, $CFG, $DB, $params;

        if (!empty($params['coursesearchtext'])) {
            $coursesearch = '<input type="hidden" name="coursesearch" value="'.$params['coursesearchtext'].'" />';
        } else {
            $coursesearch = '';
        }

        return '<form action="iomad_courses_form.php" method="get">
                <input type="hidden" name="courseid" value="' . $row->courseid . '" />
                <input type="hidden" name="companyid" value="'.$row->companyid.'" />'.
                $coursesearch .'
                <input type="hidden" name="update" value="expireafter" />
                <input type="text" name="expireafter" id="id_expire" value="'.$row->expireafter.'" size="10"/>
                <input type="submit" value="' . get_string('submit') . '" />
                </form>';

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warnexpire($row) {
        global $output, $CFG, $DB, $params;

        if (!empty($params['coursesearchtext'])) {
            $coursesearch = '<input type="hidden" name="coursesearch" value="'.$params['coursesearchtext'].'" />';
        } else {
            $coursesearch = '';
        }

        return '<form action="iomad_courses_form.php" method="get">
                <input type="hidden" name="courseid" value="' . $row->courseid . '" />
                <input type="hidden" name="companyid" value="'.$row->companyid.'" />'.
                $coursesearch .'
                <input type="hidden" name="update" value="warnexpire" />
                <input type="text" name="warnexpire" id="id_warnexpire" value="'.$row->warnexpire.'" size="10"/>
                <input type="submit" value="' . get_string('submit') . '" />
                </form>';

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warnnotstarted($row) {
        global $output, $CFG, $DB, $params;

        if (!empty($params['coursesearchtext'])) {
            $coursesearch = '<input type="hidden" name="coursesearch" value="'.$params['coursesearchtext'].'" />';
        } else {
            $coursesearch = '';
        }

        return '<form action="iomad_courses_form.php" method="get">
                <input type="hidden" name="courseid" value="' . $row->courseid . '" />
                <input type="hidden" name="companyid" value="'.$row->companyid.'" />'.
                $coursesearch .'
                <input type="hidden" name="update" value="warnnotstarted" />
                <input type="text" name="warnnotstarted" id="id_warnnotstarted" value="'.$row->warnnotstarted.'" size="10"/>
                <input type="submit" value="' . get_string('submit') . '" />
                </form>';

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warncompletion($row) {
        global $output, $CFG, $DB, $params;

        if (!empty($params['coursesearchtext'])) {
            $coursesearch = '<input type="hidden" name="coursesearch" value="'.$params['coursesearchtext'].'" />';
        } else {
            $coursesearch = '';
        }

        return '<form action="iomad_courses_form.php" method="get">
                <input type="hidden" name="courseid" value="' . $row->courseid . '" />
                <input type="hidden" name="companyid" value="'.$row->companyid.'" />'.
                $coursesearch .'
                <input type="hidden" name="update" value="warncompletion" />
                <input type="text" name="warncompletion" id="id_warncompletion" value="'.$row->warncompletion.'" size="10"/>
                <input type="submit" value="' . get_string('submit') . '" />
                </form>';

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_notifyperiod($row) {
        global $output, $CFG, $DB, $params;

        if (!empty($params['coursesearchtext'])) {
            $coursesearch = '<input type="hidden" name="coursesearch" value="'.$params['coursesearchtext'].'" />';
        } else {
            $coursesearch = '';
        }

        return '<form action="iomad_courses_form.php" method="get">
                <input type="hidden" name="courseid" value="' . $row->courseid . '" />
                <input type="hidden" name="companyid" value="'.$row->companyid.'" />'.
                $coursesearch .'
                <input type="hidden" name="update" value="notifyperiod" />
                <input type="text" name="notifyperiod" id="id_notifyperiod" value="'.$row->notifyperiod.'" size="10"/>
                <input type="submit" value="' . get_string('submit') . '" />
                </form>';

    }
}