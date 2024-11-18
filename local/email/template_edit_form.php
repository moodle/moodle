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

$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$templateid = optional_param('templateid', 0, PARAM_INTEGER);
$templatesetid = optional_param('templatesetid', 0, PARAM_INTEGER);
$templatename = optional_param('templatename', '', PARAM_NOTAGS);
$new = optional_param('createnew', 0, PARAM_INTEGER);
$lang = optional_param('lang', '', PARAM_LANG);
$edit = optional_param('edit', '', PARAM_TEXT);
$view = optional_param('view', '', PARAM_TEXT);
$add = optional_param('add', '', PARAM_TEXT);
$reset = optional_param('reset', '', PARAM_TEXT);

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

$urlparams = array('templateid' => $templateid, 'templatename' => $templatename);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

iomad::require_capability('local/email:edit', $companycontext);

if (empty($templatesetid)) {
    if (!$templaterecord = $DB->get_record('email_template',['id' => $templateid])) {
        throw new \moodle_exception('templatenotfound', 'local_email', new moodle_url('/local/email/template_list.php'));
    }
} else {
    if (!$templaterecord = $DB->get_record('email_templateset_templates', ['id' => $templateid])) {
        throw new \moodle_exception('templatenotfound', 'local_email', new moodle_url('/local/email/template_list.php'));
    }
}

if (empty($templaterecord->subject)) {
    $templaterecord->subject = get_string($templatename . '_subject', 'local_email', $lang);
}
if (empty($templaterecord->body)) {
    $templaterecord->body = get_string($templatename . '_body', 'local_email', $lang);
}
if (empty($templaterecord->emailfromothername)) {
    $templaterecord->emailfromothername = '{Company_Name}';
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
$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->requires->jquery();
$PAGE->requires->js('/local/email/module.js');

// Are we dealing with a reset?
//  Deal with any deletes.
if ($reset == 'Reset' && confirm_sesskey()) {
    if ($confirm != md5($templateid)) {
        echo $OUTPUT->header();

        $optionsyes = ['templateid' => $templateid,
                       'templatesetid' => $templatesetid,
                       'templatename' => $templatename,
                       'lang' => $lang,
                       'confirm' => md5($templateid),
                       'sesskey' => sesskey(),
                       'reset' => 'Reset'];
        echo $OUTPUT->confirm(get_string('resettemplatefull', 'local_email', "'" . get_string($templaterecord->name . "_name", 'local_email') ."'"),
                              new moodle_url('/local/email/template_edit_form.php', $optionsyes),
                                             '/local/email/template_list.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Reset the template.
        $templaterecord->subject = '';
        $templaterecord->body = '';
        $templaterecord->emailto = '';
        $templaterecord->emailfrom = '';
        $templaterecord->emailreplyto = '';
        $templaterecord->emailcc = '';
        $templaterecord->emailfromothername = '{Company_Name}';
        $templaterecord->emailtoother = '';
        $templaterecord->emailfromother = '';
        $templaterecord->emailreplytoother = '';
        $templaterecord->emailccother = '';
        $templaterecord->repeatday = 0;
        $templaterecord->repeatperiod = 0;
        $templaterecord->repeatvalue = 0;
        $templaterecord->signature = "";
        if (empty($templatesetid)) {
            $DB->update_record('email_template', $templaterecord);
        } else {
            $DB->update_record('email_templateset_templates', $templaterecord);
        }

        redirect(new moodle_url('/local/email/template_list.php', ['templatesetid' => $templatesetid]),
                 get_string('templateresetok', 'local_email', "'" . get_string($templaterecord->name . "_name", 'local_email') ."'"),
                 null,
                 \core\output\notification::NOTIFY_SUCCESS);
        die;
    }
}

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