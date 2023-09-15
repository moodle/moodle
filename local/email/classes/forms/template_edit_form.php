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

namespace local_email\forms;

use \moodleform;
use \context_system;
use \block_iomad_commerce\helper;
use company;
use EmailVars;

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
                $break = "<br>";
                $optioncount = 0;
            } else {
                $break = "&nbsp";
            }
            $mform->addElement('html', "<a href='# data-text='$option' class='clickforword'>$option</a>$break");
            $optioncount++;
        }
        $mform->addElement('html', "</div>");

        $mform->addElement('editor', 'signature_editor', get_string('signature', 'local_email'),
                           array('enable_filemanagement' => false,
                                 'changeformat' => false,
                                 'class' => 'fitem_id_signature_editor'));
        $mform->setType('signature_editor', PARAM_RAW);
        $mform->addElement('html', "<div class='emailvars'>");
        $optioncount = 0;
        foreach ($vars as $option) {
            if ($optioncount > 10) {
                $break = "<br>";
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