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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user create course groups within a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INT);
$deleteids = optional_param_array('courseids', null, PARAM_INT);
$createnew = optional_param('createnew', 0, PARAM_INT);
$selectedcourse = optional_param('selectedcourse', 0, PARAM_INTEGER);
$groupids = optional_param_array('groupids', 0, PARAM_INTEGER);

if (!empty($groupids)) {
    $groupid = $groupids[0];
} else {
    $groupid = 0;
}

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_company_admin:edit_groups', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/my', $urlparams);

$linktext = get_string('managegroups', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_groups_create_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the page heading.
$PAGE->set_heading($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Javascript for fancy select.
// Parameter is name of proper select form element.
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid'));

$groupsform = new \block_iomad_company_admin\forms\company_groups_form($PAGE->url, $context, $companyid, $selectedcourse);
if (!empty($selectedcourse)) {
    $defaultgroup = company::get_company_group($companyid, $selectedcourse);
    $mform = new \block_iomad_company_admin\forms\course_group_display_form($PAGE->url, $companyid, $selectedcourse, $output);
    $editform = new \block_iomad_company_admin\forms\group_edit_form($PAGE->url, $companyid, $selectedcourse, $groupid, $output);
}
$groupsform->set_data(array('selectedcourse' => $selectedcourse));

if (!empty($selectedcourse)) {
    if ($mform->is_cancelled()) {
        redirect($companylist);

    } else if ($data = $mform->get_data()) {
        if (isset($data->create)) {
            if (!empty($deleteids)) {
                $chosenid = $deleteids['0'];
            } else {
                $chosenid = 0;
            }
            $editform = new \block_iomad_company_admin\forms\group_edit_form($PAGE->url, $companyid, $selectedcourse, $groupid, $output);
            echo $output->header();

            $editform->display();
            echo $output->footer();
            die;
        } else if (isset($data->delete)) {
            $shownotice = false;
            if (empty($groupid)) {
                $shownotice = true;
                $noticestring = get_string('groupnoselect', 'block_iomad_company_admin');
            } else {
                if ($groupid != $defaultgroup->id) {
                    $course = $DB->get_record('course', array('id' => $selectedcourse));
                    company::delete_company_course_group($companyid, $course, false, $groupid);
                } else {
                    $shownotice = true;
                    $noticestring = get_string('isdefaultgroupdelete', 'block_iomad_company_admin');
                }
            }
            $mform = new \block_iomad_company_admin\forms\course_group_display_form($PAGE->url, $companyid, $selectedcourse, $output);
            // Redisplay the form.
            echo $output->header();
            $groupsform->display();
            if ($shownotice) {
                notice($noticestring, new moodle_url($PAGE->url, array('selectedcourse' => $selectedcourse)));
            }
            $mform->display();
            echo $output->footer();
            die;

        } else if (isset($data->edit)) {
            // Editing an existing group..
            if (!empty($groupid)) {
                $grouprecord = $DB->get_record('groups', array('id' => $groupid));
                $editform = new \block_iomad_company_admin\forms\group_edit_form($PAGE->url, $companyid, $selectedcourse, $groupid, $output);
                $editform->set_data(array('groupid' => $grouprecord->id,
                                          'name' => $grouprecord->name,
                                          'description' => $grouprecord->description));
                echo $output->header();

                // Check the department is valid.
                if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
                    print_error('invaliddepartment', 'block_iomad_company_admin');
                }

                $editform->display();

                echo $output->footer();
                die;
            } else {
                echo $output->header();
                // Check the department is valid.
                if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
                    print_error('invaliddepartment', 'block_iomad_company_admin');
                }

                echo get_string('departmentnoselect', 'block_iomad_company_admin');
                $mform->display();
                echo $output->footer();
                die;
            }

        }
    } else if ($createdata = $editform->get_data()) {

        // Create or update the department.
        company::create_company_course_group($companyid,
                                             $selectedcourse,
                                             $createdata);

        $mform = new \block_iomad_company_admin\forms\course_group_display_form($PAGE->url, $companyid, $selectedcourse, $output);
        // Redisplay the form.
        echo $output->header();

        // Check the department is valid.
        if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
            print_error('invaliddepartment', 'block_iomad_company_admin');
        }

        $groupsform->display();
        $mform->display();

        echo $output->footer();
        die;
    }
}
echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

$groupsform->display();
if (!empty($selectedcourse)) {
    $mform->display();
}

echo $output->footer();
