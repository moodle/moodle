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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib.php');
require_once($CFG->libdir . '/formslib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$userid = required_param('userid', PARAM_INTEGER);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_license_users', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$urlparams = array('companyid' => $companyid, 'licenseid' => $licenseid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($userid) {
    $urlparams['userid'] = $userid;
}

// Correct the navbar.
// Set the name for the page.
$user = $DB->get_record('user', ['id' => $userid]);
$linktext = get_string('company_license_users_for', 'block_iomad_company_admin', fullname($user));

// Set the url.
$returnurl = new moodle_url('/blocks/iomad_company_admin/editusers.php');
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_users_licenses_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

// Deal with the link back to the user edit page.
$buttoncaption = get_string('edit_users_title', 'block_iomad_company_admin');
$buttonlink = new moodle_url('/blocks/iomad_company_admin/editusers.php');
$buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
$PAGE->set_button($buttons);

$coursesform = new \block_iomad_company_admin\forms\company_users_licenses_form($PAGE->url, $context, $companyid, $departmentid, $userid, $licenseid);

echo $OUTPUT->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Check the userid is valid.
if (!company::check_valid_user($companyid, $userid, $departmentid)) {
    print_error('invaliduserdepartment', 'block_iomad_company_management');
}

//  Check the license is valid for this company.
if (!empty($licenseid) && !company::check_valid_company_license($companyid, $licenseid)) {
    print_error('invalidcompanylicense', 'block_iomad_company_admin');
}

if ($coursesform->is_cancelled() || optional_param('cancel', false, PARAM_BOOL)) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    if ($companyid > 0) {
        $coursesform->process();
        $coursesform = new \block_iomad_company_admin\forms\company_users_licenses_form($PAGE->url, $context, $companyid, $departmentid, $userid, $licenseid);
        // Display the license selector.
        $availablewarning = "";
        $licenselist = array();
        if (iomad::has_capability('block/iomad_company_admin:unallocate_licenses', context_system::instance())) {
            $parentlevel = company::get_company_parentnode($companyid);
            $userhierarchylevel = $parentlevel->id;
            // Get all the licenses.
            // Are we an educator?
            if (!empty($userid) && $DB->get_records('company_users', array('userid' => $userid, 'educator' => 1))) {
                $licenses = $DB->get_records('companylicense', array('companyid' => $companyid), 'expirydate DESC', 'id,type,name,startdate,expirydate');
            } else {
                $licenses = $DB->get_records_sql("SELECT id,type,name,startdate,expirydate FROM {companylicense}
                                                  WHERE companyid = :companyid
                                                  AND type < 2
                                                  ORDER BY expirydate DESC",
                                                  array('companyid' => $companyid));
            }
            foreach ($licenses as $license) {
                if ($license->expirydate < time()) {
                    $licenselist[$license->id] = $license->name . " (" . get_string('licenseexpired', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->expirydate)) . ")";
                } else if ($license->startdate > time()) {
                    $licenselist[$license->id] = $license->name . " (" . get_string('licensevalidfrom', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate)) . ")";
                    if ($licenseid == $license->id) {
                        $availablewarning = get_string('licensevalidfromwarning', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate));
                    }
                } else {
                    $licenselist[$license->id] = $license->name;
                }
                if ($license->type > 1) {
                    $licenselist[$license->id] = $licenselist[$license->id] . " (" . get_string('educator', 'block_iomad_company_admin') .")";
                }
            }
        } else {
            $userlevel = $company->get_userlevel($USER);
            $userhierarchylevel = key($userlevel);
            if (!empty($userid) && $DB->get_record('company_users', array('userid' => $userid, 'educator' => 1))) {
                $educator = true;
            } else {
                $educator = false;
            }
            $licenses = company::get_recursive_departments_licenses($userhierarchylevel);
            if (!empty($licenses)) {
                foreach ($licenses as $deptlicenseid) {
                    // Get the license record.
                    if ($license = $DB->get_records('companylicense',
                                                     array('id' => $deptlicenseid->licenseid, 'companyid' => $companyid),
                                                     null, 'id,name,startdate,expirydate')) {
                        if (!$educator && !empty($license->type) && $license->type > 1) {
                            continue;
                        }

                        if ($license[$deptlicenseid->licenseid]->expirydate > time()) {
                            if (!empty($license->startdate) && $license->startdate > time()) {
                                $licenselist[$license->id] = $license->name . " (" . get_string('licensevalidfrom', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate)) . ")";
                                if ($licenseid == $license->id) {
                                    $availablewarning = get_string('licensevalidfromwarning', 'block_iomad_company_admin', date($CFG->iomad_date_format, $license->startdate));
                                }  
                            } else {
                                $licenselist[$license[$deptlicenseid->licenseid]->id]  = $license[$deptlicenseid->licenseid]->name;
                            }
                        }
                        if (!empty($license->type) && $license->type > 1) {
                            $licenselist[$license->id] = $licenselist[$license->id] . " (" . get_string('educator', 'block_iomad_company_admin') . ")";
                        }
                    }
                }
            }
        }

        if (count($licenses) == 0) {
            echo '<h3>' . get_string('editlicensestitle', 'block_iomad_company_admin') . '</h3>';
            echo '<p>' . get_string('licensehelp', 'block_iomad_company_admin') . '</p>';
            echo '<b>' . get_string('nolicenses', 'block_iomad_company_admin') . '</b>';
        } else {
            $selecturl = new moodle_url('/blocks/iomad_company_admin/company_users_licenses_form.php', $urlparams);
            $licenseselect = new single_select($selecturl, 'licenseid', $licenselist, $licenseid);
            $licenseselect->label = get_string('select_license', 'block_iomad_company_admin');
            $licenseselect->formid = 'chooselicense';
            echo html_writer::tag('div', $OUTPUT->render($licenseselect), array('id' => 'iomad_license_selector'));

            if (!empty($availablewarning)) {
                echo html_writer::start_tag('div', array('class' => "alert alert-success"));
                echo $availablewarning;
                echo "</div>";
            }

            $coursesform->get_data();
            echo $coursesform->display();

        }
    }

    echo $OUTPUT->footer();
}
