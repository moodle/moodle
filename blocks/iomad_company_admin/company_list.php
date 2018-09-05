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

require_once('lib.php');
require_once($CFG->dirroot.'/user/profile/definelib.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.

$context = context_system::instance();
require_login();

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('company_list_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_list.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('company_list_title', 'block_iomad_company_admin'));

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;


if ($delete and confirm_sesskey()) {              // Delete a selected company, after confirmation.

    iomad::require_capability('block/iomad_company_admin:company_delete', $context);

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

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_company_admin:company_view', $context);

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
            if (iomad::has_capability('block/iomad_company_admin:company_delete', $context)) {
                $deletebutton = "<a href=\"company_list.php?delete=$company->id&amp;sesskey=".sesskey()."\">$strdelete</a>";
            } else {
                $deletebutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_edit', $context)) {
                $editbutton = "<a href='" . new moodle_url('company_edit_form.php',
                                array("companyid" => $company->id)) . "'>$stredit</a>";
            } else {
                $editbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_user', $context)) {
                $usersbutton = "<a href='" . new moodle_url('company_users_form.php',
                                 array("companyid" => $company->id)) . "'>$strusers</a>";
            } else {
                $usersbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:user_create', $context)) {
                $newuserbutton = "<a href='" . new moodle_url('company_user_create_form.php',
                                   array("companyid" => $company->id)) . "'>$strnewuser</a>";
            } else {
                $newuserbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_manager', $context)) {
                $managersbutton = "<a href='" . new moodle_url('company_managers_form.php',
                                   array("companyid" => $company->id)) . "'>$strmanagers</a>";
            } else {
                $managersbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_course', $context)) {
                $coursesbutton = "<a href='" . new moodle_url('company_courses_form.php',
                                   array("companyid" => $company->id)) . "'>$strcourses</a>";
            } else {
                $coursesbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:createcourse', $context)) {
                $createcoursebutton = "<a href='" . new moodle_url('company_course_create_form.php',
                                       array("companyid" => $company->id)) . "'>$strcreatecourse</a>";
            } else {
                $createcoursebutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:company_course_users', $context)) {
                $courseusersbutton = "<a href='" . new moodle_url('company_course_users_form.php',
                                      array("companyid" => $company->id)) . "'>$strcourseusers</a>";
            } else {
                $courseusersbutton = "";
            }

            if (iomad::has_capability('block/iomad_company_admin:user_upload', $context)) {
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

if (iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
    echo '<div class="buttons">';

    echo $OUTPUT->single_button(new moodle_url('company_edit_form.php'),
                                                get_string('addnewcompany', 'block_iomad_company_admin'), 'get');
    echo $OUTPUT->single_button(new moodle_url('/my'), get_string('cancel'), 'get');

    echo '</div>';
}

echo $OUTPUT->footer();
