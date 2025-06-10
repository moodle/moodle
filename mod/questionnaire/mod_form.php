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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');
require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

/**
 * print the form to add or edit a questionnaire-instance
 *
 * @package mod_questionnaire
 * @author Mike Churchward
 * @copyright  2016 onward Mike Churchward (mike.churchward@poetgroup.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class mod_questionnaire_mod_form extends moodleform_mod {

    /**
     * Form definition.
     */
    protected function definition() {
        global $COURSE;
        global $questionnairetypes, $questionnairerespondents, $questionnaireresponseviewers, $autonumbering;

        $questionnaire = new questionnaire($COURSE, $this->_cm, $this->_instance, null);

        $mform    =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'questionnaire'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('description'));

        $mform->addElement('header', 'availabilityhdr', get_string('availability'));
        $mform->addElement('date_time_selector', 'opendate', get_string('opendate', 'questionnaire'), ['optional' => true]);
        $mform->addElement('date_time_selector', 'closedate', get_string('closedate', 'questionnaire'), ['optional' => true]);

        $mform->addElement('header', 'questionnairehdr', get_string('responseoptions', 'questionnaire'));

        $mform->addElement('select', 'qtype', get_string('qtype', 'questionnaire'), $questionnairetypes);
        $mform->addHelpButton('qtype', 'qtype', 'questionnaire');

        $mform->addElement('hidden', 'cannotchangerespondenttype');
        $mform->setType('cannotchangerespondenttype', PARAM_INT);
        $mform->addElement('select', 'respondenttype', get_string('respondenttype', 'questionnaire'), $questionnairerespondents);
        $mform->addHelpButton('respondenttype', 'respondenttype', 'questionnaire');
        $mform->disabledIf('respondenttype', 'cannotchangerespondenttype', 'eq', 1);

        $mform->addElement('select', 'resp_view', get_string('responseview', 'questionnaire'), $questionnaireresponseviewers);
        $mform->addHelpButton('resp_view', 'responseview', 'questionnaire');

        $notificationoptions = array(0 => get_string('no'), 1 => get_string('notificationsimple', 'questionnaire'),
            2 => get_string('notificationfull', 'questionnaire'));
        $mform->addElement('select', 'notifications', get_string('notifications', 'questionnaire'), $notificationoptions);
        $mform->addHelpButton('notifications', 'notifications', 'questionnaire');

        $options = array('0' => get_string('no'), '1' => get_string('yes'));
        $mform->addElement('select', 'resume', get_string('resume', 'questionnaire'), $options);
        $mform->addHelpButton('resume', 'resume', 'questionnaire');

        $options = array('0' => get_string('no'), '1' => get_string('yes'));
        $mform->addElement('select', 'navigate', get_string('navigate', 'questionnaire'), $options);
        $mform->addHelpButton('navigate', 'navigate', 'questionnaire');

        $mform->addElement('select', 'autonum', get_string('autonumbering', 'questionnaire'), $autonumbering);
        $mform->addHelpButton('autonum', 'autonumbering', 'questionnaire');
        // Default = autonumber both questions and pages.
        $mform->setDefault('autonum', 3);

        $mform->addElement('advcheckbox', 'progressbar', get_string('progressbar', 'questionnaire'));

        // Removed potential scales from list of grades. CONTRIB-3167.
        $grades[0] = get_string('nograde');
        for ($i = 100; $i >= 1; $i--) {
            $grades[$i] = $i;
        }
        $mform->addElement('select', 'grade', get_string('grade', 'questionnaire'), $grades);

        if (empty($questionnaire->sid)) {
            if (!isset($questionnaire->id)) {
                $questionnaire->id = 0;
            }

            $mform->addElement('header', 'contenthdr', get_string('contentoptions', 'questionnaire'));
            $mform->addHelpButton('contenthdr', 'createcontent', 'questionnaire');

            $mform->addElement('radio', 'create', get_string('createnew', 'questionnaire'), '', 'new-0');

            // Retrieve existing private questionnaires from current course.
            $surveys = questionnaire_get_survey_select($COURSE->id, 'private');
            if (!empty($surveys)) {
                $prelabel = get_string('useprivate', 'questionnaire');
                foreach ($surveys as $value => $label) {
                    $mform->addElement('radio', 'create', $prelabel, $label, $value);
                    $prelabel = '';
                }
            }
            // Retrieve existing template questionnaires from this site.
            $surveys = questionnaire_get_survey_select($COURSE->id, 'template');
            if (!empty($surveys)) {
                $prelabel = get_string('usetemplate', 'questionnaire');
                foreach ($surveys as $value => $label) {
                    $mform->addElement('radio', 'create', $prelabel, $label, $value);
                    $prelabel = '';
                }
            } else {
                $mform->addElement('static', 'usetemplate', get_string('usetemplate', 'questionnaire'),
                                '('.get_string('notemplatesurveys', 'questionnaire').')');
            }

            // Retrieve existing public questionnaires from this site.
            $surveys = questionnaire_get_survey_select($COURSE->id, 'public');
            if (!empty($surveys)) {
                $prelabel = get_string('usepublic', 'questionnaire');
                foreach ($surveys as $value => $label) {
                    $mform->addElement('radio', 'create', $prelabel, $label, $value);
                    $prelabel = '';
                }
            } else {
                $mform->addElement('static', 'usepublic', get_string('usepublic', 'questionnaire'),
                                   '('.get_string('nopublicsurveys', 'questionnaire').')');
            }

            $mform->setDefault('create', 'new-0');
        }

        $this->standard_coursemodule_elements();

        // Buttons.
        $this->add_action_buttons();
    }

    /**
     * Pre-process form data.
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        global $DB;
        if (empty($defaultvalues['opendate'])) {
            $defaultvalues['useopendate'] = 0;
        } else {
            $defaultvalues['useopendate'] = 1;
        }
        if (empty($defaultvalues['closedate'])) {
            $defaultvalues['useclosedate'] = 0;
        } else {
            $defaultvalues['useclosedate'] = 1;
        }
        // Prevent questionnaire set to "anonymous" to be reverted to "full name".
        $defaultvalues['cannotchangerespondenttype'] = 0;
        if (!empty($defaultvalues['respondenttype']) && $defaultvalues['respondenttype'] == "anonymous") {
            // If this questionnaire has responses.
            $numresp = $DB->count_records('questionnaire_response',
                            array('questionnaireid' => $defaultvalues['instance'], 'complete' => 'y'));
            if ($numresp) {
                $defaultvalues['cannotchangerespondenttype'] = 1;
            }
        }
    }

    /**
     * Enforce validation rules here
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array
     **/
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check open and close times are consistent.
        if ($data['opendate'] && $data['closedate'] &&
            $data['closedate'] < $data['opendate']) {
            $errors['closedate'] = get_string('closebeforeopen', 'questionnaire');
        }

        return $errors;
    }

    /**
     * Add any completion rules for the form.
     * @return string[]
     */
    public function add_completion_rules() {
        global $CFG;

        // Changes for Moodle 4.3 - MDL-78516.
        if ($CFG->branch < 403) {
            $suffix = '';
        } else {
            $suffix = $this->get_suffix();
        }

        $mform =& $this->_form;
        $mform->addElement('checkbox', 'completionsubmit' . $suffix, '',
            get_string('completionsubmit', 'questionnaire'));
        return ['completionsubmit' . $suffix];
    }

    /**
     * True if the completion rule is enabled.
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }

}
