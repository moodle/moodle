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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir . '/formslib.php');

class local_email {
    // Returns true if user is allowed to send emails using a particular template.
    public static function allow_sending_to_template($templatename) {
        return in_array($templatename, array('advertise_classroom_based_course'));
    }

    public static function create_default_template_row($templatename, $strdefault, $stradd, $strsend, $enable, $lang, $prefix, $templatesetid = 0) {
        global $PAGE, $company, $OUTPUT;

        $deletebutton = "";

        if ($enable) {
            $value ="{$prefix}.e.{$templatename}";
            $enablebutton = '<label class="switch"><input class="checkbox enableall" type="checkbox" checked value="' . $value . '" />' .
                             "<span class='slider round'></span></label>";
            $value ="{$prefix}.em.{$templatename}";
            $enablemanagerbutton = '<label class="switch"><input class="checkbox enablemanager" type="checkbox" checked value="' . $value . '" />' .
                             "<span class='slider round'></span></label";
            $value ="{$prefix}.es.{$templatename}";
            $enablesupervisorbutton = '<label class="switch"><input class="checkbox enablesupervisor" type="checkbox" checked value="' . $value . '" />' .
                             "<span class='slider round'></span></label>";

        } else {
            $enablebutton = "";
            $enablemanagerbutton = "";
            $enablesupervisorbutton = "";
        }
        if ($stradd) {
            $editbutton = "<a class='btn' href='" . new moodle_url('template_edit_form.php',
                           array("templatename" => $templatename, 'lang' => $lang)) . "'>$stradd</a>";
        } else {
            $editbutton = "";
        }
        if ($stradd) {
            $editbutton = "<a class='btn' href='" . new moodle_url('template_edit_form.php',
                           array("templatename" => $templatename, 'lang' => $lang)) . "'>$stradd</a>";
        } else {
            $editbutton = "";
        }
        if ($strsend && self::allow_sending_to_template($templatename) ) {
            $sendbutton = "<a class='btn' href='" . new moodle_url('template_send_form.php',
                           array("templatename" => $templatename, 'lang' => $lang)) . "'>$strsend</a>";
        } else {
            $sendbutton = "";
        }

        $rowform = new email_template_edit_form(new moodle_url('template_edit_form.php'), $company->id, $templatename, $templatesetid);
        $rowform->set_data(array('templatename' => $templatename, 'lang' => $lang));
        $row = new html_table_row();
        $row->cells[] = get_string($templatename.'_name', 'local_email') . $OUTPUT->help_icon($templatename.'_name', 'local_email');
        $cell = new html_table_cell($enablebutton);
        $row->cells[] = $cell;
        $cell = new html_table_cell($enablemanagerbutton);
        $row->cells[] = $cell;
        $cell = new html_table_cell($enablesupervisorbutton);
        $row->cells[] = $cell;
        $cell = new html_table_cell($rowform->render());
        $row->cells[] = $cell;

        return $row;
    }

    public static function get_templates() {
        $email = array();

        // Add emails with subject and body strings from lang/??/local_email.php.
        $emailarray = array('admin_deleted',
                            'advertise_classroom_based_course',
                            'approval',
                            'company_licenseassigned',
                            'company_suspended',
                            'company_unsuspended',
                            'completion_course_user',
                            'completion_course_supervisor',
                            'completion_digest_manager',
                            'completion_expiry_warn_supervisor',
                            'completion_warn_manager',
                            'completion_warn_supervisor',
                            'completion_warn_user',
                            'course_classroom_approval',
                            'course_classroom_approved',
                            'course_classroom_approval_request',
                            'course_classroom_denied',
                            'course_completed_manager',
                            'course_classroom_manager_denied',
                            'course_not_started_warning',
                            'expire',
                            'expire_manager',
                            'expiry_warn_manager',
                            'expiry_warn_user',
                            'invoice_ordercomplete',
                            'invoice_ordercomplete_admin',
                            'licensepoolexpiring',
                            'licensepoolwarning',
                            'license_allocated',
                            'license_reminder',
                            'license_removed',
                            'microlearning_nugget_scheduled',
                            'microlearning_nugget_reminder1',
                            'microlearning_nugget_reminder2',
                            'password_update',
                            'trainingevent_not_selected',
                            'user_added_to_course',
                            'user_create',
                            'user_deleted',
                            'user_programcompleted',
                            'user_promoted',
                            'user_removed_from_event',
                            'user_removed_from_event_teacher',
                            'user_removed_from_event_waitlist',
                            'user_reset',
                            'user_signed_up_for_event',
                            'user_suspended',
                            'user_unsuspended');

        // Set up the email template array.
        foreach ($emailarray as $templatename) {
            $email[$templatename] = array(
                'subject' => get_string($templatename . '_subject', 'local_email' ),
                'body' => get_string($templatename . '_body', 'local_email')
            );
        }

        return $email;
    }
}

class email_template_edit_form extends moodleform {

    public function __construct($actionurl, $companyid, $templatename, $templatesetid) {
        global $DB;

        $this->langs = get_string_manager()->get_list_of_translations(true);
        $this->templatesetid = $templatesetid;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $DB,$CFG, $USER;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'templatename');
        $mform->addElement('hidden', 'templatesetid', $this->templatesetid);
        $mform->setType('templatename', PARAM_CLEAN);
        $mform->setType('templatesetid', PARAM_INT);
        $mform->addElement('select', 'lang', get_string('language'), $this->langs);
        $mform->setDefault('lang', $USER->lang);
        $buttonarr = array();
        $buttonarr[] = &$mform->createElement('submit', 'edit', get_string('edit'));
        $buttonarr[] = &$mform->createElement('submit', 'view', get_string('view'));
        $buttonarr[] = &$mform->createElement('submit', 'add', get_string('add'));
        $mform->addGroup($buttonarr, 'buttonar', '', array(' '), false);

    }
}
