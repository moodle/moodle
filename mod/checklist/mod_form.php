<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
 * This file defines the main checklist configuration form
 * It uses the standard core Moodle (>1.8) formslib. For
 * more info about them, please visit:
 *
 * http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * The form must provide support for, at least these fields:
 *   - name: text element of 64cc max
 *
 * Also, it's usual to use these fields:
 *   - intro: one htmlarea element to describe the activity
 *            (will be showed in the list of activities of
 *             newmodule type (index.php) and in the header
 *             of the checklist main page (view.php).
 *   - introformat: The format used to write the contents
 *             of the intro field. It automatically defaults
 *             to HTML when the htmleditor is used and can be
 *             manually selected if the htmleditor is not used
 *             (standard formats are: MOODLE, HTML, PLAIN, MARKDOWN)
 *             See lib/weblib.php Constants and the format_text()
 *             function for more info
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_checklist_mod_form extends moodleform_mod {

    public function definition() {

        global $CFG;
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('modulename', 'checklist'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        if ($CFG->branch < 29) {
            $this->add_intro_editor(true, get_string('checklistintro', 'checklist'));
        } else {
            $this->standard_intro_elements(get_string('checklistintro', 'checklist'));
        }

        $mform->addElement('header', 'checklistsettings', get_string('checklistsettings', 'checklist'));
        $mform->setExpanded('checklistsettings', true);

        $ynoptions = array(0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'useritemsallowed', get_string('useritemsallowed', 'checklist'), $ynoptions);

        $teditoptions = array(
            CHECKLIST_MARKING_STUDENT => get_string('teachernoteditcheck', 'checklist'),
            CHECKLIST_MARKING_TEACHER => get_string('teacheroverwritecheck', 'checklist'),
            CHECKLIST_MARKING_BOTH => get_string('teacheralongsidecheck', 'checklist')
        );
        $mform->addElement('select', 'teacheredit', get_string('teacheredit', 'checklist'), $teditoptions);

        $mform->addElement('select', 'duedatesoncalendar', get_string('duedatesoncalendar', 'checklist'), $ynoptions);
        $mform->setDefault('duedatesoncalendar', 0);

        $mform->addElement('select', 'teachercomments', get_string('teachercomments', 'checklist'), $ynoptions);
        $mform->setDefault('teachercomments', 1);

        $mform->addElement('text', 'maxgrade', get_string('maximumgrade'), array('size' => '10'));
        $mform->setDefault('maxgrade', 100);
        $mform->setType('maxgrade', PARAM_INT);

        $emailrecipients = array(
            CHECKLIST_EMAIL_NO => get_string('no'),
            CHECKLIST_EMAIL_STUDENT => get_string('teachernoteditcheck', 'checklist'),
            CHECKLIST_EMAIL_TEACHER => get_string('teacheroverwritecheck', 'checklist'),
            CHECKLIST_EMAIL_BOTH => get_string('teacheralongsidecheck', 'checklist')
        );
        $mform->addElement('select', 'emailoncomplete', get_string('emailoncomplete', 'checklist'), $emailrecipients);
        $mform->setDefault('emailoncomplete', 0);
        $mform->addHelpButton('emailoncomplete', 'emailoncomplete', 'checklist');

        $autopopulateoptions = array(
            CHECKLIST_AUTOPOPULATE_NO => get_string('no'),
            CHECKLIST_AUTOPOPULATE_SECTION => get_string('importfromsection', 'checklist'),
            CHECKLIST_AUTOPOPULATE_COURSE => get_string('importfromcourse', 'checklist')
        );
        $mform->addElement('select', 'autopopulate', get_string('autopopulate', 'checklist'), $autopopulateoptions);
        $mform->setDefault('autopopulate', 0);
        $mform->addHelpButton('autopopulate', 'autopopulate', 'checklist');

        $checkdisable = true;
        $str = 'autoupdate';
        if (get_config('mod_checklist', 'linkcourses')) {
            $str = 'autoupdate2';
            $checkdisable = false;
        }

        $autoupdateoptions = array(
            CHECKLIST_AUTOUPDATE_NO => get_string('no'),
            CHECKLIST_AUTOUPDATE_YES => get_string('yesnooverride', 'checklist'),
            CHECKLIST_AUTOUPDATE_YES_OVERRIDE => get_string('yesoverride', 'checklist')
        );
        $mform->addElement('select', 'autoupdate', get_string($str, 'checklist'), $autoupdateoptions);
        $mform->setDefault('autoupdate', 1);
        $mform->addHelpButton('autoupdate', $str, 'checklist');
        $mform->addElement('static', 'autoupdatenote', '', get_string('autoupdatenote', 'checklist'));
        if ($checkdisable) {
            $mform->disabledIf('autoupdate', 'autopopulate', 'eq', 0);
        }

        $mform->addElement('selectyesno', 'lockteachermarks', get_string('lockteachermarks', 'checklist'));
        $mform->setDefault('lockteachermarks', 0);
        $mform->addHelpButton('lockteachermarks', 'lockteachermarks', 'checklist');

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        // Set up the completion checkboxes which aren't part of standard data.
        // We also make the default value (if you turn on the checkbox) for those
        // numbers to be 1, this will not apply unless checkbox is ticked.
        $defaultvalues['completionpercentenabled'] = !empty($defaultvalues['completionpercent']) ? 1 : 0;
        if (empty($defaultvalues['completionpercent'])) {
            $defaultvalues['completionpercent'] = 100;
        }
    }

    public function add_completion_rules() {
        $mform =& $this->_form;

        $group = array();
        $group[] =& $mform->createElement('checkbox', 'completionpercentenabled', '', get_string('completionpercent', 'checklist'));
        $group[] =& $mform->createElement('text', 'completionpercent', '', array('size' => 3));
        $mform->setType('completionpercent', PARAM_INT);
        $mform->addGroup($group, 'completionpercentgroup', get_string('completionpercentgroup', 'checklist'), array(' '), false);
        $mform->disabledIf('completionpercent', 'completionpercentenabled', 'notchecked');

        return array('completionpercentgroup');
    }

    public function completion_rule_enabled($data) {
        return (!empty($data['completionpercentenabled']) && $data['completionpercent'] != 0);
    }

    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Turn off completion settings if the checkboxes aren't ticked.
        if (isset($data->completionpercent)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionpercentenabled) || !$autocompletion) {
                $data->completionpercent = 0;
            }
        }
        return $data;
    }

}
