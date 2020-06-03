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
require_once(dirname('__FILE__').'/company_license_table.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_licenses, PARAM_INT);        // How many per page.
$companyid    = optional_param('companyid', 0, PARAM_INTEGER);
$save         = optional_param('save', 0, PARAM_INTEGER);
$showexpired  = optional_param('showexpired', 0, PARAM_INTEGER);

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
$PAGE->set_heading(get_string('myhome') . " - $linktext");
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'showexpired' => $showexpired));
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

    if ($company->is_child_license($delete)) {
        iomad::require_capability('block/iomad_company_admin:edit_my_licenses', $context);
    } else {
        iomad::require_capability('block/iomad_company_admin:edit_licenses', $context);
    }

    $license = $DB->get_record('companylicense', array('id' => $delete), '*', MUST_EXIST);
    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        // TODO SET THE LICENSE NAME.
        $name = $license->name;
        if ($license->used > 0) {
            notice(get_string('licenseinuse', 'block_iomad_company_admin'), $linkurl);
        } else {
            echo $OUTPUT->heading(get_string('deletelicense', 'block_iomad_company_admin'), 2, 'headingblock header');
            $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());

            echo $OUTPUT->confirm(get_string('companydeletelicensecheckfull', 'block_iomad_company_admin', "'$name'"),
                                  new moodle_url('company_license_list.php', $optionsyes), 'company_license_list.php');
            echo $OUTPUT->footer();
            die;
        }
    } else if (data_submitted()) {
        // Actually delete license.
        if (!$DB->delete_records('companylicense', array('id' => $delete))) {
            print_error('error while deleting license');
        }

        // Create an event to deal with an parent license allocations.
        $eventother = array('licenseid' => $license->id,
                            'parentid' => $license->parentid);

        $event = \block_iomad_company_admin\event\company_license_deleted::create(array('context' => context_system::instance(),
                                                                                        'userid' => $USER->id,
                                                                                        'objectid' => $license->parentid,
                                                                                        'other' => $eventother));
        $event->trigger();

        redirect($returnurl, get_string('licensedeletedok', 'block_iomad_company_admin'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_company_admin:view_licenses', $context);

$company = new company($companyid);
echo "<h3>".$company->get_name()."</h3>";

flush();

$straddlicense = get_string('licenseaddnew', 'block_iomad_company_admin');
$strlicensename = get_string('licensename', 'block_iomad_company_admin');
$strlicensereference = get_string('licensereference', 'block_iomad_company_admin');
$strlicensetype = get_string('licensetype', 'block_iomad_company_admin');
$strlicenseprogram = get_string('licenseprogram', 'block_iomad_company_admin');
$strlicenseinstant = get_string('licenseinstant', 'block_iomad_company_admin');
$strcoursesname = get_string('allocatedcourses', 'block_iomad_company_admin');
$strlicenseshelflife = get_string('licenseexpires', 'block_iomad_company_admin');
$strlicenseduration = get_string('licenseduration', 'block_iomad_company_admin');
$strlicenseallocated = get_string('licenseallocated', 'block_iomad_company_admin');
$strlicenseremaining = get_string('licenseremaining', 'block_iomad_company_admin');
$strcompany = get_string('company', 'block_iomad_company_admin');

// Set up the table
$table = new company_license_table('company_licenses_table');

$tableheaders = array ($strlicensename,
                       $strlicensereference,
                       $strlicensetype,
                       $strlicenseprogram,
                       $strlicenseinstant,
                       $strcoursesname,
                       $strlicenseshelflife,
                       $strlicenseduration,
                       $strlicenseallocated,
                       $strlicenseremaining,
                       "",
                       "");

$tablecolumns = array('name',
                      'reference',
                      'type',
                      'program',
                      'instant',
                      'coursesname',
                      'expirydate',
                      'validlength',
                      'humanallocation',
                      'used',
                      'actions');

if (iomad::has_capability('block/iomad_company_admin:company_add_child', $context) && $childcompanies = $company->get_child_companies_recursive()) {
    $tableheaders = array_merge(array($strcompany), $tableheaders);
    $tablecolumns = array_merge(array('companyname'), $tablecolumns);
    $showcompanies = true;
    $gotchildren = true;
    $childsql = "OR cl.companyid IN (" . join(',', array_keys($childcompanies)) . ")";
} else {
    $showcompanies = false;
    $gotchildren = false;
    $childsql = "";
}

// Are we showing the expired licenses?
if (empty($showexpired)) {
    $expiredsql = " AND cl.expirydate > :time ";
} else {
    $expiredsql = "";
}

// Does this company have children?
if ($childcompanies = $company->get_child_companies_recursive()) {
    $gotchildren = true;
} else {
    $gotchildren = false;
}

// Get the licenses.
$table->set_sql("cl.*,c.name AS companyname", "{companylicense} cl JOIN {company} c ON (cl.companyid = c.id)", "cl.companyid = :companyid $childsql $expiredsql", array('companyid' => $companyid, 'time' => time()));

$table->define_baseurl($baseurl);
$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);
$table->sort_default_column = 'expirydate DESC';
$table->no_sorting('coursesname');
$table->no_sorting('used');
$table->no_sorting('actions');


echo '<div class="buttons">';
if ($showexpired) {
    $showexpiredstring = get_string('hideexpiredlicenses', 'block_iomad_company_admin');
} else {
    $showexpiredstring = get_string('showexpiredlicenses', 'block_iomad_company_admin');
}
echo $OUTPUT->single_button(new moodle_url('company_license_list.php', array('showexpired' => !$showexpired)),
                                            $showexpiredstring);
if (iomad::has_capability('block/iomad_company_admin:edit_licenses', $context)) {
    echo $OUTPUT->single_button(new moodle_url('company_license_edit_form.php'),
                                                get_string('licenseaddnew', 'block_iomad_company_admin'), 'get');
}
echo '</div>';

// Display the list of licenses.
$table->out($CFG->iomad_max_list_licenses, true);
echo $OUTPUT->footer();
