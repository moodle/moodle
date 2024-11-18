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

require_once('lib.php');
require_once($CFG->dirroot.'/user/profile/definelib.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_companies, PARAM_INT);        // How many per page.

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_company_admin:company_view', $companycontext);

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('company_list_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_list.php');

// Print the page header.
$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('company_list_title', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;


if ($delete and confirm_sesskey()) {              // Delete a selected company, after confirmation.

    iomad::require_capability('block/iomad_company_admin:company_delete', $companycontext);

    $company = $DB->get_record('company', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $name = $company->name;
        echo $OUTPUT->heading(get_string('deletecompany', 'block_iomad_company_admin'), 2, 'headingblock header');
        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('companydeletecheckfull', 'block_iomad_company_admin', "'$name'"),
                              new moodle_url('company_list.php', $optionsyes), 'company_list.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $a2bdcompany = new company($delete);
        $userids = $a2bdcompany->get_user_ids();

        $transaction = $DB->start_delegated_transaction();

        if ($DB->delete_records('company', array('id' => $delete))) {

            // Delete the company profile category.
            if ($category = $DB->get_record('user_info_category', array('name' => $company->shortname))) {
                // Remove the category.
                profile_delete_category($ccategory->id);
            }
            // Delete company users as well.
            foreach ($userids as $userid) {
                $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
                // Must not allow deleting of admins or self!!!
                if (is_siteadmin($user)) {
                    throw new moodle_exception('useradminodelete', 'error');
                }
                if ($USER->id == $user->id) {
                    throw new moodle_exception('usernotdeletederror', 'error');
                }
                user_delete_user($user);
            }

            $transaction->allow_commit();
            redirect($returnurl);
        } else {
            $transaction->rollback();
            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $company->name));
            die;
        }

        $transaction->rollback();
    }
}

echo $OUTPUT->header();

// Get the number of companies.
$objectcount = $DB->count_records('company');
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

flush();

if ($companies = company::get_companies_rs($page, $perpage)) {
    $stredit   = get_string('edit');
    $strdelete = get_string('delete');
    $strusers = get_string('company_users', 'block_iomad_company_admin');
    $strnewuser = get_string('newuser', 'block_iomad_company_admin');
    $strmanagers = get_string('company_managers', 'block_iomad_company_admin');
    $strcourses = get_string('company_courses', 'block_iomad_company_admin');
    $strcreatecourse = get_string('newcourse', 'block_iomad_company_admin');
    $strcourseusers = get_string('courseusers', 'block_iomad_company_admin');
    $strusersdownload = get_string('users_download', 'block_iomad_company_admin');

    $table = new html_table();
    $table->head = array ("Name", "Short name", "City", "", "", "", "", "", "", "", "", "");
    $table->align = array ("left",
                           "left",
                           "left",
                           "center",
                           "center",
                           "center",
                           "center",
                           "center",
                           "center",
                           "center",
                           "center",
                           "center");
    $table->width = "95%";

    foreach ($companies as $company) {
        if (company_user::can_see_company($company)) {
            if (iomad::has_capability('block/iomad_company_admin:company_delete', $companycontext)) {
                $deletebutton = "<a href=\"company_list.php?delete=$company->id&amp;sesskey=".sesskey()."\">$strdelete</a>";
            } else {
                $deletebutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_edit', $companycontext)) {
                $editbutton = "<a href='" . new moodle_url('company_edit_form.php',
                                array("companyid" => $company->id)) . "'>$stredit</a>";
            } else {
                $editbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_user', $companycontext)) {
                $usersbutton = "<a href='" . new moodle_url('company_users_form.php',
                                 array("companyid" => $company->id)) . "'>$strusers</a>";
            } else {
                $usersbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:user_create', $companycontext)) {
                $newuserbutton = "<a href='" . new moodle_url('company_user_create_form.php',
                                   array("companyid" => $company->id)) . "'>$strnewuser</a>";
            } else {
                $newuserbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_manager', $companycontext)) {
                $managersbutton = "<a href='" . new moodle_url('company_managers_form.php',
                                   array("companyid" => $company->id)) . "'>$strmanagers</a>";
            } else {
                $managersbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_course', $companycontext)) {
                $coursesbutton = "<a href='" . new moodle_url('company_courses_form.php',
                                   array("companyid" => $company->id)) . "'>$strcourses</a>";
            } else {
                $coursesbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:createcourse', $companycontext)) {
                $createcoursebutton = "<a href='" . new moodle_url('company_course_create_form.php',
                                       array("companyid" => $company->id)) . "'>$strcreatecourse</a>";
            } else {
                $createcoursebutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_course_users', $companycontext)) {
                $courseusersbutton = "<a href='" . new moodle_url('company_course_users_form.php',
                                      array("companyid" => $company->id)) . "'>$strcourseusers</a>";
            } else {
                $courseusersbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:user_upload', $companycontext)) {
                $downloadbutton = "<a href='" . new moodle_url('user_bulk_download.php',
                                   array("companyid" => $company->id)) . "'>$strusersdownload</a>";
            } else {
                $downloadbutton = "";
            }

            $table->data[] = array ("$company->name",
                                "$company->shortname",
                                "$company->city",
                                $editbutton,
                                $usersbutton,
                                $newuserbutton,
                                $managersbutton,
                                $coursesbutton,
                                $createcoursebutton,
                                $courseusersbutton,
                                $downloadbutton,
                                $deletebutton);
        }
    }

    if (!empty($table)) {
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
    }

    $companies->close();
}

if (iomad::has_capability('block/iomad_company_admin:company_add', $companycontext)) {
    echo '<div class="buttons">';

    echo $OUTPUT->single_button(new moodle_url('company_edit_form.php'),
                                                get_string('addnewcompany', 'block_iomad_company_admin'), 'get');
    echo $OUTPUT->single_button(new moodle_url($CFG->wwwroot .'/blocks/iomad_company_admin/index.php'), get_string('cancel'), 'get');

    echo '</div>';
}

echo $OUTPUT->footer();