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

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_course_users', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($userid) {
    $urlparams['userid'] = $userid;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('edit_users_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/editusers.php');
$formurl = new moodle_url('/blocks/iomad_company_admin/company_users_course_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');

// Deal with the link back to the user edit page.
$buttoncaption = get_string('edit_users_title', 'block_iomad_company_admin');
$buttonlink = new moodle_url('/blocks/iomad_company_admin/editusers.php');
$buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
$PAGE->set_button($buttons);

$user = $DB->get_record('user', ['id' => $userid]);
$linktext = get_string('user_courses_for', 'block_iomad_company_admin', fullname($user));
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

$coursesform = new \block_iomad_company_admin\forms\company_users_course_form($formurl, $context, $companyid, $departmentid, $userid);

echo $OUTPUT->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Check the userid is valid.
if (!company::check_valid_user($companyid, $userid, $departmentid)) {
    print_error('invaliduserdepartment', 'block_iomad_company_management');
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
        echo $coursesform->display();
    }

    echo $OUTPUT->footer();
}
