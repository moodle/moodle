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

class template_edit_form extends moodleform {
    protected $isadding;
    protected $subject = '';
    protected $body = '';
    protected $templateid;
    protected $templaterecord;
    protected $companyid;

    public function __construct($actionurl, $isadding, $companyid, $templateid, $templaterecord) {
        $this->isadding = $isadding;
        $this->templateid = $templateid;
        $this->templaterecord = $templaterecord;
        $this->companyid = $companyid;
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $PAGE, $DB;
        $context = context_system::instance();

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'templateid', $this->templateid);
        $mform->addElement('hidden', 'templatename', $this->templaterecord->name);
        $mform->addElement('hidden', 'companyid', $this->companyid);
        $mform->addElement('hidden', 'lang', $this->templaterecord->lang);
        $mform->setType('templateid', PARAM_INT);
        $mform->setType('companyid', PARAM_INT);
        $mform->setType('templatename', PARAM_CLEAN);
        $mform->setType('lang', PARAM_LANG);

        $company = new company($this->companyid);

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header', get_string('email_template', 'local_email', array(
            'name' => $this->templaterecord->name,
            'companyname' => $company->get_name()
        )));

        $mform->addElement('text', 'subject', get_string('subject', 'local_email'),
                            array('size' => 100));
        $mform->setType('subject', PARAM_NOTAGS);
        $mform->addRule('subject', $strrequired, 'required');

        $mform->addElement('editor', 'body_editor', get_string('body', 'local_email'),
                           array('enable_filemanagement' => false,
                                 'changeformat' => false));
        $mform->setType('body_editor', PARAM_RAW);
        $mform->addRule('body_editor', $strrequired, 'required');

        $vars = EmailVars::vars();
        $options = "<option value=''>" . get_string('select_email_var', 'local_email') .
                   "</option>";
        foreach ($vars as $i) {
            $options .= "<option value='{{$i}}'>$i</option>";
        }

        $select = "<select class='emailvars' onchange='Iomad.onSelectEmailVar(this)'>
                 $options</select>";
        $html = "<div class='fitem'><div class='fitemtitle'></div><div class='felement'>
                 $select</div></div>";

        $mform->addElement('html', $html);

        global $PAGE;
        $PAGE->requires->js('/local/email/module.js');

        $submitlabel = null; // Default.
        if ($this->isadding) {
            $submitlabel = get_string('save_to_override_default_template', 'local_email');
            $mform->addElement('hidden', 'createnew', 1);
            $mform->setType('createnew', PARAM_INT);

        }
        $this->add_action_buttons(true, $submitlabel);
    }

    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            if ($data->body_editor) {
                $data->body = $data->body_editor;
            }
        }

        return $data;
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$templateid = optional_param('templateid', 0, PARAM_INTEGER);
$templatename = optional_param('templatename', '', PARAM_NOTAGS);
$new = optional_param('createnew', 0, PARAM_INTEGER);
$lang = optional_param('lang', 'en', PARAM_LANG);

$context = context_system::instance();
require_login();

$urlparams = array('templateid' => $templateid, 'templatename' => $templatename);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

if (!$new) {
    $isadding = false;

    if ($templateid) {
        $templaterecord = $DB->get_record('email_template', array('id' => $templateid),
                                                                  '*', MUST_EXIST);
        iomad::require_capability('local/email:edit', $context);
    } else {
        $isadding = true;
        $templateid = 0;
        $templaterecord = (object) $email[$templatename];
        $templaterecord->name = $templatename;
        iomad::require_capability('local/email:add', $context);
    }
} else {
    $isadding = true;
    $templateid = 0;
    $templaterecord = (object) $email[$templatename];
    $templaterecord->name = $templatename;

    iomad::require_capability('local/email:add', $context);
}

$templaterecord->lang = $lang;

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('edit_template', 'local_email');
// Set the url.
$linkurl = new moodle_url('/local/email/template_edit_form.php');

if ($isadding) {
    $title = get_string('addnewtemplate', 'local_email');
} else {
    $title = get_string('editatemplate', 'local_email');
}

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($title);


// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);


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

// Set up the form.
$mform = new template_edit_form($PAGE->url, $isadding, $companyid, $templateid, $templaterecord);
$templaterecord->body_editor = array('text' => $templaterecord->body, 'format' => 1);
$mform->set_data($templaterecord);

if ($mform->is_cancelled()) {
    redirect($templatelist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;
/*echo "<pre>";
print_r($data);
die;
*/
    if ($isadding) {
        $data->companyid = $companyid;
        $data->name = $templatename;
        $data->body = $data->body_editor['text']; 
        $templateid = $DB->insert_record('email_template', $data);
        $data->id = $templateid;
    } else {
        $data->id = $templateid;
        $data->body = $data->body_editor['text']; 
        $DB->update_record('email_template', $data);
    }
    redirect($templatelist);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
