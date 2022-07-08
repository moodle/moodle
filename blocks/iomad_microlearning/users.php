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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib/user_selectors.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/local/email/lib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$threadid = optional_param('threadid', 0, PARAM_INTEGER);
$groupid = optional_param('groupid', "-1", PARAM_INTEGER);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$selectedthread = optional_param('selectedthread', 0, PARAM_INTEGER);
$groupid = optional_param('groupid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();

$params = array('companyid' => $companyid,
                'threadid' => $threadid,
                'groupid' => $groupid,
                'deptid' => $departmentid,
                'selectedthread' => $selectedthread,
                'groupid' => $groupid);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($threadid) {
    $urlparams['threadid'] = $threadid;
}

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/users.php');
$threadlink = new moodle_url('/blocks/iomad_microlearning/threads.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));

require_login(null, false); // Adds to $PAGE, creates $output.
iomad::require_capability('block/iomad_microlearning:assign_threads', $context);
// Set the companyid
$companyid = iomad::get_my_companyid($context);
$parentlevel = company::get_company_parentnode($companyid);
$companydepartment = $parentlevel->id;
$syscontext = context_system::instance();
$company = new company($companyid);

// Set the name for the page.
$linktext = get_string('company_threads_for', 'block_iomad_microlearning', $company->get_name());

// Set the page heading.
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

// Deal with the link back to the main microlearning page.
$buttoncaption = get_string('threads', 'block_iomad_microlearning');
$buttonlink = new moodle_url('/blocks/iomad_microlearning/threads.php');
$buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
$PAGE->set_button($buttons);

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $syscontext)) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = key($userlevel);
}

// Set up the forms.
$threadsform = new block_iomad_microlearning\forms\microlearning_threads_form($PAGE->url, $context, $companyid, $departmentid, $selectedthread, $parentlevel);
$usersform = new block_iomad_microlearning\forms\microlearning_thread_users_form($PAGE->url, $context, $companyid, $departmentid, $threadid, $groupid);
echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

if ($threadsform->is_cancelled() || $usersform->is_cancelled() ||
     optional_param('cancel', false, PARAM_BOOL) ) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    echo $output->display_tree_selector($company, $parentlevel, $linkurl, $params, $departmentid);
    echo html_writer::start_tag('div', array('class' => 'iomadclear'));
    if ($companyid > 0) {
        $threadsform->set_data($params);
        echo $threadsform->display();
        if ($data = $threadsform->get_data() || !empty($selectedthread)) {
             if ($threadid > 0) {
                $thread = $DB->get_record('microlearning_thread', array('id' => $threadid));
                //$usersform->set_thread(array($thread));
                $usersform->process();
                $usersform = new block_iomad_microlearning\forms\microlearning_thread_users_form($PAGE->url, $context, $companyid, $departmentid, $threadid, $groupid);
                //$usersform->set_thread(array($thread));
            } else if (!empty($selectedthread)) {
                $usersform->set_thread($selectedthread);
            }
            echo $usersform->display();
        } else if ($threadid > 0) {
            $thread = $DB->get_record('microlearning_thread', array('id' => $threadid));
            //$usersform->set_thread(array($thread));
            $usersform->process();
            $usersform = new block_iomad_microlearning\forms\microlearning_thread_users_form($PAGE->url, $context, $companyid, $departmentid, $threadid, $groupid);
            //$usersform->set_thread(array($thread));
            echo $usersform->display();
        }
    }
    echo html_writer::end_tag('div');

    echo $output->footer();
}
