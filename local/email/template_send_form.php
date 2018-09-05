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
 * Script to let a user edit the properties of a particular email template.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once('config.php');

require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib/course_selectors.php');
require_once($CFG->dirroot . '/local/email/lib.php');

class template_send_form extends moodleform {
    protected $context = null;
    protected $subject = '';
    protected $body = '';
    protected $templateid;
    protected $templaterecord;
    protected $companyid;

    public function __construct($actionurl, $context, $companyid, $templateid, $templaterecord) {
        $this->templateid = $templateid;
        $this->context = $context;
        $this->templaterecord = $templaterecord;
        $this->companyid = $companyid;

        $department = company::get_company_parentnode($this->companyid);
        $subhierarchieslist = company::get_all_subdepartments($department->id);

        $options = array('context' => $this->context,
                         'multiselect' => false,
                         'companyid' => $this->companyid,
                         'departmentid' => $department->id,
                         'subdepartments' => $subhierarchieslist,
                         'parentdepartmentid' => $department);
        $this->currentcourses = new current_company_course_selector('currentcourses', $options);
        $this->currentcourses->set_rows(1);

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $PAGE, $DB;
        $context = context_system::instance();

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'templateid', $this->templateid);
        $mform->setType('templateid', PARAM_INT);
        $mform->addElement('hidden', 'templatename', $this->templaterecord->name);
        $mform->setType('templatename', PARAM_NOTAGS);
        $mform->addElement('hidden', 'companyid', $this->companyid);
        $mform->setType('companyid', PARAM_INT);

        $company = new company($this->companyid);

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header', get_string('email_template_send',
            'local_email', array(
            'name' => $this->templaterecord->name,
            'companyname' => $company->get_name()
        )));

        $mform->addElement('static', 'subject', get_string('subject', 'local_email'));
        $mform->addElement('static', 'body', get_string('body', 'local_email'));

        $mform->addElement('header', 'header', get_string('email_data', 'local_email'));

        $this->addHtml($mform, get_string('company', 'block_iomad_company_admin'),
                                           $company->get_name());
        $this->addHtml($mform, get_string('select_course', 'local_email'),
                                           $this->currentcourses->display(true));

        $this->add_action_buttons(true, get_string('send_emails', 'local_email'));
    }

    public function addHtml($mform, $text, $html) {
        $mform->addElement('html', "<div class='fitem'><div class='fitemtitle'>" . $text .
                                   "</div><div class='felement'>");
        $mform->addElement('html', $html);
        $mform->addElement('html', "</div></div>");
    }

    public function get_data() {
        $data = parent::get_data();

        if ($data !== null && $this->currentcourses) {
            $data->selectedcourses = $this->currentcourses->get_selected_courses();
        }

        return $data;
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$templateid = optional_param('templateid', 0, PARAM_INTEGER);
$templatename = optional_param('templatename', '', PARAM_NOTAGS);
$new = optional_param('createnew', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();

$urlparams = array('templateid' => $templateid, 'templatename' => $templatename);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$templatelist = new moodle_url('/local/email/template_list.php', $urlparams);

// Set the companyid to bypass the company select form if possible.
if (!empty($SESSION->currenteditingcompany)) {
    $companyid = $SESSION->currenteditingcompany;
} else if (!empty($USER->company)) {
    $companyid = company_user::companyid();
} else if (!iomad::has_capability('local/email:edit', context_system::instance())) {
    print_error('There has been a configuration error, please contact the site administrator');
} else {
    redirect(new moodle_url('/my'),
                            'Please select a company from the dropdown first');
}

iomad::require_capability('local/email:send', $context);

if ($templateid) {
    $templaterecord = $DB->get_record('email_template',
                                       array('id' => $templateid), '*', MUST_EXIST);
} else if ($templatename) {
    if (!$templaterecord = $DB->get_record('email_template', array('companyid' => $companyid,
                                           'name' => $templatename), '*')) {
        $templaterecord = (object) $email[$templatename];
        $templaterecord->name = $templatename;
    }
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('send_emails', 'local_email');
// Set the url.
$linkurl = new moodle_url('/local/email/template_edit_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($title);

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
// Get the form data.

// Set up the form.
$mform = new template_send_form($PAGE->url, $context, $companyid, $templateid, $templaterecord);
$mform->set_data($templaterecord);

if ($mform->is_cancelled()) {
    redirect($templatelist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;

    foreach ($data->selectedcourses as $course) {
        $depts = company::get_departments_by_course($course->id);

        $sql = "SELECT
                    cr.*,
                    ccr.startdatetime,
                    ccr.enddatetime,
                    ccr.intro as summary
                FROM {courseclassroom} ccr
                    INNER JOIN {classroom} cr ON ccr.classroomid = cr.id
                WHERE ccr.course = :course
                ORDER BY ccr.startdatetime > " . time() . ", ccr.startdatetime";

        $classroom = $DB->get_record_sql($sql, array('course' => $course->id), 0, 1);

        foreach ($depts as $departmentid) {
            EmailTemplate::send_to_all_users_in_department($departmentid, $templaterecord->name,
                            array('course' => $course->id, 'classroom' => $classroom));
        }
    }

    redirect($templatelist);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
