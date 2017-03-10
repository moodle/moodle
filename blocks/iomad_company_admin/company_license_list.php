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
$companyid    = optional_param('companyid', 0, PARAM_INTEGER);

global $DB;

$context = context_system::instance();

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('company_license_list_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_license_list.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('name', 'local_iomad_dashboard') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;

// Get the appropriate company department.
$companydepartment = company::get_company_parentnode($companyid);
if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
    $departmentid = $companydepartment->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $departmentid = $userlevel->id;
}

if ($delete and confirm_sesskey()) {              // Delete a selected company, after confirmation.

    iomad::require_capability('block/iomad_company_admin:edit_licenses', $context);

    $license = $DB->get_record('companylicense', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        // TODO SET THE LICENSE NAME.
        $name = $license->name;
        echo $OUTPUT->heading(get_string('deletelicense', 'block_iomad_company_admin'), 2, 'headingblock header');
        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('companydeletelicensecheckfull', 'block_iomad_company_admin', "'$name'"),
                              new moodle_url('company_license_list.php', $optionsyes), 'company_license_list.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        // Actually delete license.
        if (!$DB->delete_records('companylicense', array('id' => $delete))) {
            print_error('error while deleting license');
        }
    }
}

echo $OUTPUT->header();

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_company_admin:view_licenses', $context);

$company = new company($companyid);
echo "<h3>".$company->get_name()."</h3>";

// Get the number of companies.
$objectcount = $DB->count_records('companylicense', array('companyid' => $companyid));
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

flush();

$stredit   = get_string('edit');
$strdelete = get_string('delete');
$straddlicense = get_string('licenseaddnew', 'block_iomad_company_admin');
$strlicensename = get_string('licensename', 'block_iomad_company_admin');
$strcoursesname = get_string('allocatedcourses', 'block_iomad_company_admin');
$strlicenseshelflife = get_string('licenseexpires', 'block_iomad_company_admin');
$strlicenseduration = get_string('licenseduration', 'block_iomad_company_admin');
$strlicenseallocated = get_string('licenseallocated', 'block_iomad_company_admin');
$strlicenseremaining = get_string('licenseremaining', 'block_iomad_company_admin');

$table = new html_table();
$table->head = array ($strlicensename,
                      $strcoursesname,
                      $strlicenseshelflife,
                      $strlicenseduration,
                      $strlicenseallocated,
                      $strlicenseremaining,
                      "",
                      "");
$table->align = array ("left", "left", "left", "left", "center", "center", "center", "center");
$table->width = "95%";

if ($departmentid == $companydepartment->id) {
    $licenses = $DB->get_records('companylicense', array('companyid' => $companyid));

    // Cycle through the results.
    foreach ($licenses as $license) {
        // Set up the edit buttons.
        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context)) {
            $deletebutton = "<a href=\"company_license_list.php?delete=$license->id&amp;sesskey=".sesskey()."\">$strdelete</a>";
            $editbutton = "<a href='" . new moodle_url('company_license_edit_form.php',
                           array("licenseid" => $license->id, 'departmentid' => $departmentid)) . "'>$stredit</a>";
        } else {
            $deletebutton = "";
            $editbutton = "";
        }
        $licensecourses = $DB->get_records('companylicense_courses', array('licenseid' => $license->id));
        $coursestring = "";
        foreach ($licensecourses as $licensecourse) {
            $coursename = $DB->get_record('course', array('id' => $licensecourse->courseid));
            if (empty($coursestring)) {
                $coursestring = "<a href='".new moodle_url('/course/view.php',
                                   array('id' => $licensecourse->courseid))."'>".$coursename->fullname."</a>";
            } else {
                $coursestring .= ",</br><a href='".new moodle_url('/course/view.php',
                                   array('id' => $licensecourse->courseid))."'>".$coursename->fullname."</a>";
            }
        }

        // Create the table data.
        $table->data[] = array ("$license->name",
                           $coursestring,
                           date($CFG->iomad_date_format, $license->expirydate),
                           "$license->validlength",
                           "$license->allocation",
                           "$license->used",
                           $editbutton,
                           $deletebutton);
    }
} else if ($licenses = company::get_recursive_departments_licenses($companydepartment->id)) {
    foreach ($licenses as $licenseid) {

        // Get the license record.
        $license = $DB->get_record('companylicense', array('id' => $licenseid->licenseid));

        // Set up the edit buttons.
        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context)) {
            $deletebutton = "<a href=\"company_license_list.php?delete=$license->id&amp;sesskey=".sesskey()."\">$strdelete</a>";
            $editbutton = "<a href='" . new moodle_url('company_license_edit_form.php',
                                                        array("licenseid" => $license->id, 'departmentid' => $departmentid)) .
                                                        "'>$stredit</a>";
        } else {
            $deletebutton = "";
            $editbutton = "";
        }

        $table->data[] = array ("$license->name",
                            "$license->expirydate",
                            "$license->validlength",
                            "$license->allocation",
                            "$license->used",
                            $editbutton,
                            $deletebutton);
    }
}

if (!empty($table)) {
    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
}


echo '<div class="buttons">';

echo $OUTPUT->single_button(new moodle_url('company_license_edit_form.php'),
                                            get_string('licenseaddnew', 'block_iomad_company_admin'), 'get');
echo $OUTPUT->single_button(new moodle_url('/local/iomad_dashboard/index.php'), get_string('cancel'), 'get');

echo '</div>';

echo $OUTPUT->footer();
