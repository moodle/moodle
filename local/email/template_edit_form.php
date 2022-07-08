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
    protected $editing;

    public function __construct($actionurl, $isadding, $isediting, $companyid, $templateid, $templaterecord, $templatesetid) {
        $this->isadding = $isadding;
        $this->isediting = $isediting;
        $this->templateid = $templateid;
        $this->templaterecord = $templaterecord;
        $this->companyid = $companyid;
        $this->templatesetid = $templatesetid;
        $company = new company($companyid);
        $this->companymanagers = $company->get_managers_select();
        $this->multiplecompanymanagers = $this->companymanagers;
        unset($this->multiplecompanymanagers[0]);
        if (!empty($isadding)) {
            $this->isediting = $isadding;
        }
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $PAGE, $DB;
        $context = context_system::instance();
        $company = new company($this->companyid);

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $buttonarr = array();
        $buttonarr[] = &$mform->createElement('html', '<span data-fieldtype="button">
            <button class="btn btn-secondary emailclicktoedit" name="edit" id="id_edit" type="button">' .
                get_string('edit') . '</button></span>');
        $buttonarr[] = &$mform->createElement('submit', 'save', get_string('save'));
        $buttonarr[] = &$mform->createElement('cancel', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarr, 'buttonar', '', array(' '), false);

        $mform->addElement('hidden', 'templateid', $this->templateid);
        $mform->addElement('hidden', 'templatename', $this->templaterecord->name);
        $mform->addElement('hidden', 'companyid', $this->companyid);
        $mform->addElement('hidden', 'templatesetid', $this->templatesetid);
        $mform->addElement('hidden', 'isediting', $this->isediting, array('id' => 'isediting'));
        $mform->setType('isediting', PARAM_INT);
        $mform->setType('templateid', PARAM_INT);
        $mform->setType('companyid', PARAM_INT);
        $mform->setType('templatesetid', PARAM_INT);
        $mform->setType('templatename', PARAM_CLEAN);

        if (empty($this->isadding)) {
            $mform->addElement('hidden', 'lang', $this->templaterecord->lang);
            $mform->setType('lang', PARAM_LANG);
        } else {
            $langs = get_string_manager()->get_list_of_translations();
            $languages = $DB->get_records('email_template', array('companyid' => $this->companyid, 'name' => $this->templaterecord->name), null, 'id,lang');
            unset($langs['en']);
            foreach ($languages as $language) {
                unset($langs[$language->lang]);
            }
            $mform->addElement('select', 'lang', get_string('language'), $langs);
        }

        $companymanagers = $company->get_managers_select();
        $mform->addElement('autocomplete', 'emailto', get_string('to'), $this->multiplecompanymanagers, array('multiple' => true));

        $mform->addElement('text', 'emailtoother', get_string('toother', 'local_email'),
                            array('size' => 100));
        $mform->setType('emailtoother', PARAM_EMAIL);

        $mform->addElement('autocomplete', 'emailfrom', get_string('from'), $this->companymanagers);

        $mform->addElement('text', 'emailfromother', get_string('fromother', 'local_email'),
                            array('size' => 100));
        $mform->setType('emailfromother', PARAM_EMAIL);

        $mform->addElement('text', 'emailfromothername', get_string('fromothername', 'local_email'),
                            array('size' => 100));
        $mform->setType('emailfromothername', PARAM_TEXT);
        $mform->setDefault('emailfromothername', '{Company_Name}');

        $mform->addElement('autocomplete', 'emailcc', get_string('cc', 'local_email'), $this->multiplecompanymanagers, array('multiple' => true));

        $mform->addElement('text', 'emailccother', get_string('ccother', 'local_email'),
                            array('size' => 100));
        $mform->setType('emailccother', PARAM_EMAIL);

        $mform->addElement('autocomplete', 'emailreplyto', get_string('replyto', 'local_email'), $this->companymanagers);

        $mform->addElement('text', 'emailreplytoother', get_string('replytoother', 'local_email'),
                            array('size' => 100));
        $mform->setType('emailreplytoother', PARAM_EMAIL);

        $mform->addElement('text', 'subject', get_string('subject', 'local_email'),
                            array('size' => 100, 'class' => 'inputholder'));
        $mform->setType('subject', PARAM_NOTAGS);
        $mform->addRule('subject', $strrequired, 'required');

        $mform->addElement('editor', 'body_editor', get_string('body', 'local_email'),
                           array('enable_filemanagement' => false,
                                 'changeformat' => false,
                                 'class' => 'fitem_id_body_editor'));
        $mform->setType('body_editor', PARAM_RAW);
        $mform->addRule('body_editor', $strrequired, 'required');
        $mform->setType('body_editor', PARAM_RAW);

        $vars = EmailVars::vars();
        $mform->addElement('html', "<div class='emailvars'>");
        $optioncount = 0;
        foreach ($vars as $option) {
            if ($optioncount > 10) {
                $break = "</br>";
                $optioncount = 0;
            } else {
                $break = "&nbsp";
            }
            $mform->addElement('html', "<a href='# data-text='$option' class='clickforword'>$option</a>$break");
            $optioncount++;
        }
        $mform->addElement('html', "</div>");

/*        $mform->addElement('filemanager', 'companylogo',
                            get_string('companylogo', 'block_iomad_company_admin'), null,
                            array('subdirs' => 0,
                                 'maxbytes' => 150 * 1024,
                                 'maxfiles' => 1,
                                 'accepted_types' => array('*.jpg', '*.gif', '*.png')));
*/
        $mform->addElement('editor', 'signature_editor', get_string('signature', 'local_email'),
                           array('enable_filemanagement' => false,
                                 'changeformat' => false,
                                 'class' => 'fitem_id_signature_editor'));
        $mform->setType('signature_editor', PARAM_RAW);
        $mform->addElement('html', "<div class='emailvars'>");
        $optioncount = 0;
        foreach ($vars as $option) {
            if ($optioncount > 10) {
                $break = "</br>";
                $optioncount = 0;
            } else {
                $break = "&nbsp";
            }
            $mform->addElement('html', "<a href='# data-text='$option' class='clickforword'>$option</a>$break");
            $optioncount++;
        }
        $mform->addElement('html', "</div>");

        // Add in repeation parts.
        $repeatperiods = array('99' => get_string('always'),
                               '0' => get_string('never'),
                               '1' => get_string('daily', 'local_email'),
                               '2' => get_string('weekly', 'local_email'),
                               '3' => get_string('fortnightly', 'local_email'),
                               '4' => get_string('monthly', 'local_email'));

        $repeatdays = array('99' => get_string('any'),
                            '0' => get_string('sunday', 'calendar'),
                            '1' => get_string('monday', 'calendar'),
                            '2' => get_string('tuesday', 'calendar'),
                            '3' => get_string('wednesday', 'calendar'),
                            '4' => get_string('thursday', 'calendar'),
                            '5' => get_string('friday', 'calendar'),
                            '6' => get_string('saturday', 'calendar'));

        $repeatselect = $mform->addElement('select', 'repeatperiod', get_string('emailrepeatperiod', 'local_email'), $repeatperiods);
        $repeatselect->setSelected($this->templaterecord->repeatperiod);
        $mform->addElement('text', 'repeatvalue', get_string('emailrepeatvalue', 'local_email'));
        $mform->setType('repeatvalue', PARAM_INT);
        $repeatdayselect = $mform->addElement('select', 'repeatday', get_string('emailrepeatday', 'local_email'), $repeatdays);
        $repeatdayselect->setSelected($this->templaterecord->repeatday - 1);
        $mform->addHelpButton('repeatperiod', 'emailrepeatperiod', 'local_email');
        $mform->addHelpButton('repeatvalue', 'emailrepeatvalue', 'local_email');
        $mform->addHelpButton('repeatday', 'emailrepeatday', 'local_email');

        $mform->addElement('html', '<div class="fdescription required">' . get_string('emailrepeatinfo', 'local_email').'</div>');

        // Disable everything unless isediting = 1;
        $mform->disabledIf('emailto', 'isediting', 'neq', 1);
        $mform->disabledIf('emailtoother', 'isediting', 'neq', 1);
        $mform->disabledIf('emailfrom', 'isediting','neq', 1);
        $mform->disabledIf('emailfromother', 'isediting', 'neq', 1);
        $mform->disabledIf('emailfromothername', 'isediting', 'neq', 1);
        $mform->disabledIf('emailcc', 'isediting', 'neq', 1);
        $mform->disabledIf('emailccother', 'isediting', 'neq', 1);
        $mform->disabledIf('emailreplyto', 'isediting', 'neq', 1);
        $mform->disabledIf('emailreplytoother', 'isediting', 'neq', 1);
        $mform->disabledIf('subject', 'isediting', 'neq', 1);
        $mform->disabledIf('body_editor', 'isediting', 'neq', 1);
//        $mform->disabledIf('companylogo', 'isediting', 'neq', 1);
        $mform->disabledIf('signature_editor', 'isediting', 'neq', 1);
        $mform->disabledIf('save', 'isediting', 'neq', 1);
        $mform->disabledIf('edit', 'isediting', 'eq', 1);
        $mform->disabledIf('repeatperiod', 'isediting', 'neq', 1);
        $mform->disabledIf('repeatvalue', 'isediting', 'neq', 1);
        $mform->disabledIf('repeatday', 'isediting', 'neq', 1);
        $mform->disabledIf('repeatvalue', 'repeatperiod', 'eq', 99);
        $mform->disabledIf('repeatvalue', 'repeatperiod', 'eq', 0);
        $mform->disabledIf('repeatday', 'repeatperiod', 'eq', 0);

        $submitlabel = null; // Default.
        if ($this->isadding) {
            $submitlabel = get_string('save_to_override_default_template', 'local_email');
            $mform->addElement('hidden', 'createnew', 1);
            $mform->setType('createnew', PARAM_INT);

        }
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

    public function validation($data, $files) {
        $errors = array();
        if (!empty($data['emailfromother']) && empty($data['emailfromothername'])) {
            $errors['emilfromother'] = get_string('required');
        }

        return $errors;
    }
}

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
    $linktext = get_string('email_template', 'local_email',
                           ['name' => $templatename,
                            'companyname' => $templatesetrec->templatesetname]);
} else {
    $linktext = get_string('email_template', 'local_email',
                           ['name' => $templatename,
                            'companyname' => $company->get_name()]);
}

// Set the page heading.
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

$templatelist = new moodle_url('/local/email/template_list.php', $urlparams);

// Set up the form.
$mform = new template_edit_form($PAGE->url, $isadding, $isediting, $companyid, $templateid, $templaterecord, $templatesetid);
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

/*if (!empty($templaterecord->id)) {
    // Get the company logo.
    $draftcompanylogoid = file_get_submitted_draft_itemid('companylogo');
    file_prepare_draft_area($draftcompanylogoid,
                            $context->id,
                            'local_email',
                            'companylogo', $templaterecord->id,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $templaterecord->companylogo = $draftcompanylogoid;
}
*/
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
/*    if (!empty($data->companylogo)) {
        file_save_draft_area_files($data->companylogo,
                                   $context->id,
                                   'local_email',
                                   'companylogo',
                                   $data->id,
                                   array('subdirs' => 0, 'maxbytes' => 150 * 1024, 'maxfiles' => 1));
    }
*/
    redirect($templatelist, $redirectmessage, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
