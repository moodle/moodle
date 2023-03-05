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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user edit the properties of a particular email template.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib.php');
require_once('lib.php');
require_once('config.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$templateid = optional_param('templateid', 0, PARAM_INTEGER);
$templatesetid = optional_param('templatesetid', 0, PARAM_INTEGER);
$templatename = optional_param('templatename', '', PARAM_NOTAGS);
$new = optional_param('createnew', 0, PARAM_INTEGER);
$lang = optional_param('lang', '', PARAM_LANG);
$edit = optional_param('edit', '', PARAM_TEXT);
$view = optional_param('view', '', PARAM_TEXT);
$add = optional_param('add', '', PARAM_TEXT);

if (!empty($edit)) {
    $isediting = true;
} else {
    $isediting = false;
}
$isadding = false;

// Deal with the default language.
if (empty($lang)) {
    if (isset($SESSION->lang)) {
        $lang = $SESSION->lang;
    } else {
        $lang = $CFG->lang;
    }
}

$context = context_system::instance();
require_login();

$urlparams = array('templateid' => $templateid, 'templatename' => $templatename);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

if ($templatename && empty($add)) {
    if (empty($templatesetid)) {
        if (!$templaterecord = $DB->get_record('email_template', array('name' => $templatename, 'companyid' => $companyid, 'lang' => $lang))) {
            $templaterecord = new stdclass();
            $templaterecord->name = $templatename;
            $templaterecord->lang = $lang;
            $templaterecord->templateset = 0;
            $templaterecord->companyid = $companyid;
            $templaterecord->subject = get_string($templatename . '_subject', 'local_email', $lang);
            $templaterecord->body = get_string($templatename . '_body', 'local_email', $lang);
            $templaterecord->emailto = '';
            $templaterecord->emailcc = '';
            $templaterecord->emailfromothername = '{Company_Name}';
            $templaterecord->repeatday = 0;
            $templaterecord->repeatperiod = 0;
            $templaterecord->repeatvalue = 0;
            $templaterecord->signature = "";
        } else {
            $templateid = $templaterecord->id;
        }
    } else {
        if (!$templaterecord = $DB->get_record('email_templateset_templates', array('name' => $templatename, 'templateset' => $templatesetid, 'lang' => $lang))) {
            $templaterecord = new stdclass();
            $templaterecord->name = $templatename;
            $templaterecord->lang = $lang;
            $templaterecord->templateset = $templatesetid;
            $templaterecord->subject = get_string($templatename . '_subject', 'local_email', $lang);
            $templaterecord->body = get_string($templatename . '_body', 'local_email', $lang);
            $templaterecord->emailto = '';
            $templaterecord->emailcc = '';
            $templaterecord->emailfromothername = '{Company_Name}';
            $templaterecord->repeatday = 0;
            $templaterecord->repeatperiod = 0;
            $templaterecord->repeatvalue = 0;
            $templaterecord->signature = "";
        } else {
            $templateid = $templaterecord->id;
        }
    }
    iomad::require_capability('local/email:edit', $context);
} else {
    $isadding = true;
    $templaterecord = new stdclass();
    $templaterecord->name = $templatename;
    $templaterecord->lang = $lang;
    $templaterecord->companyid = $companyid;
    $templaterecord->subject = get_string($templatename . '_subject', 'local_email', $lang);
    $templaterecord->body = get_string($templatename . '_body', 'local_email', $lang);
    $templaterecord->emailto = '';
    $templaterecord->emailcc = '';
    $templaterecord->emailfromothername = '{Company_Name}';
    $templaterecord->repeatday = 0;
    $templaterecord->repeatperiod = 0;
    $templaterecord->repeatvalue = 0;
    $templaterecord->signature = "";
    $templaterecord->templateset = $templatesetid;

    iomad::require_capability('local/email:add', $context);
}

// Correct the navbar.
// Set the url.
$linkurl = new moodle_url('/local/email/template_edit_form.php');

if (!empty($isadding)) {
    $title = get_string('addnewtemplate', 'local_email');
} else {
    $title = get_string('editatemplate', 'local_email');
}

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->requires->jquery();
$PAGE->requires->js('/local/email/module.js');

// Set the name for the page.
if (!empty($templatesetid)) {
    $templatesetrec = $DB->get_record('email_templateset', array('id' => $templatesetid));
    $linktextextra = (object) ['name' => get_string($templatename .'_name', 'local_email'),
                               'companyname' => format_string($templatesetrec->templatesetname)];
} else {
    $linktextextra = (object) ['name' => get_string($templatename .'_name', 'local_email'),
                               'companyname' => format_string($company->get_name())];
}
$linktext = get_string('email_template', 'local_email', $linktextextra);

// Set the page heading.
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

$templatelist = new moodle_url('/local/email/template_list.php', $urlparams);

// Set up the form.
$mform = new \local_email\forms\template_edit_form($PAGE->url, $isadding, $isediting, $companyid, $templateid, $templaterecord, $templatesetid);
$templaterecord->body_editor = array('text' => $templaterecord->body, 'format' => 1);
$templaterecord->signature_editor = array('text' => $templaterecord->signature, 'format' => 1);
$emailtoarr = array();
foreach(explode(',', $templaterecord->emailto) as $emailto) {
    $emailtoarr[$emailto] = $emailto;
}
$templaterecord->emailto = $emailtoarr;
$emailccarr = array();
foreach(explode(',', $templaterecord->emailcc) as $emailcc) {
    $emailccarr[$emailcc] = $emailcc;
}
$templaterecord->emailcc = $emailccarr;

// Set the form data.
$mform->set_data($templaterecord);

if ($mform->is_cancelled()) {
    redirect($templatelist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;
    if (!empty($data->emailto)) {
        $data->emailto = implode(',', $data->emailto);
    } else {
        $data->emailto = '';
    }
    if (!empty($data->emailto)) {
        $data->emailcc = implode(',', $data->emailcc);
    } else {
        $data->emailcc = '';
    }
    if ($isadding || empty($data->templateid)) {
        $data->companyid = $companyid;
        $data->name = $templatename;
        $data->body = $data->body_editor['text'];
        $data->signature = $data->signature_editor['text'];
        if (!empty($data->templatesetid)) {
            $data->templateset = $data->templatesetid;
            $templateid = $DB->insert_record('email_templateset_templates', $data);
        } else {
            $templateid = $DB->insert_record('email_template', $data);
        }
        $data->id = $templateid;
        $redirectmessage = get_string('templatecreatedok', 'local_email');
    } else {
        $data->id = $templateid;
        $data->body = $data->body_editor['text'];
        $data->signature = $data->signature_editor['text'];
        if (!empty($data->templatesetid)) {
            $data->templateset = $data->templatesetid;
            $DB->update_record('email_templateset_templates', $data);
        } else {
            $DB->update_record('email_template', $data);
        }
        $redirectmessage = get_string('templateupdatedok', 'local_email');
    }

    redirect($templatelist, $redirectmessage, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
