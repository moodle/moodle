<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Choice module form definition.
 *
 * @package    mod_choice
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_choice_mod_form extends moodleform_mod {
    #[\Override]
    public function definition() {
        global $CFG, $DB;

        $mform = &$this->_form;

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('choicename', 'choice'), ['size' => '64']);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 1333), 'maxlength', 1333, 'client');

        $this->standard_intro_elements(get_string('description', 'choice'));

        $mform->addElement('select', 'display', get_string("displaymode", "choice"), [
            CHOICE_DISPLAY_HORIZONTAL   => get_string('displayhorizontal', 'choice'),
            CHOICE_DISPLAY_VERTICAL     => get_string('displayvertical', 'choice'),
        ]);

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'optionhdr', get_string('options', 'choice'));

        $mform->addElement('selectyesno', 'allowupdate', get_string("allowupdate", "choice"));

        $mform->addElement('selectyesno', 'allowmultiple', get_string('allowmultiple', 'choice'));
        if ($this->_instance) {
            if ($DB->count_records('choice_answers', ['choiceid' => $this->_instance]) > 0) {
                // Prevent user from toggeling the number of allowed answers once there are submissions.
                $mform->freeze('allowmultiple');
            }
        }

        $mform->addElement('selectyesno', 'limitanswers', get_string('limitanswers', 'choice'));
        $mform->addHelpButton('limitanswers', 'limitanswers', 'choice');

        $mform->addElement('selectyesno', 'showavailable', get_string('showavailable', 'choice'));
        $mform->addHelpButton('showavailable', 'showavailable', 'choice');
        $mform->hideIf('showavailable', 'limitanswers', 'eq', 0);

        $repeatarray = [];
        $repeatarray[] = $mform->createElement('text', 'option', get_string('optionno', 'choice'));
        $repeatarray[] = $mform->createElement('text', 'limit', get_string('limitno', 'choice'));
        $repeatarray[] = $mform->createElement('hidden', 'optionid', 0);

        if ($this->_instance) {
            $repeatno = $DB->count_records('choice_options', ['choiceid' => $this->_instance]);
            $repeatno += 2;
        } else {
            $repeatno = 5;
        }

        $repeateloptions = [];
        $repeateloptions['limit']['default'] = 0;
        $repeateloptions['limit']['hideif'] = ['limitanswers', 'eq', 0];
        $repeateloptions['limit']['rule'] = 'numeric';
        $repeateloptions['limit']['type'] = PARAM_INT;

        $repeateloptions['option']['helpbutton'] = ['choiceoptions', 'choice'];
        $mform->setType('option', PARAM_CLEANHTML);

        $mform->setType('optionid', PARAM_INT);

        $this->repeat_elements(
            $repeatarray,
            $repeatno,
            $repeateloptions,
            'option_repeats',
            'option_add_fields',
            3,
            null,
            true,
        );

        // Make the first option required.
        if ($mform->elementExists('option[0]')) {
            $mform->addRule('option[0]', get_string('atleastoneoption', 'choice'), 'required', null, 'client');
        }

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'availabilityhdr', get_string('availability'));
        $mform->addElement(
            'date_time_selector',
            'timeopen',
            get_string("choiceopen", "choice"),
            ['optional' => true]
        );

        $mform->addElement(
            'date_time_selector',
            'timeclose',
            get_string("choiceclose", "choice"),
            ['optional' => true]
        );

        $mform->addElement('advcheckbox', 'showpreview', get_string('showpreview', 'choice'));
        $mform->addHelpButton('showpreview', 'showpreview', 'choice');
        $mform->disabledIf('showpreview', 'timeopen[enabled]');

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'resultshdr', get_string('results', 'choice'));

        $mform->addElement('select', 'showresults', get_string("publish", "choice"), [
            CHOICE_SHOWRESULTS_NOT          => get_string('publishnot', 'choice'),
            CHOICE_SHOWRESULTS_AFTER_ANSWER => get_string('publishafteranswer', 'choice'),
            CHOICE_SHOWRESULTS_AFTER_CLOSE  => get_string('publishafterclose', 'choice'),
            CHOICE_SHOWRESULTS_ALWAYS       => get_string('publishalways', 'choice'),
        ]);

        $mform->addElement('select', 'publish', get_string("privacy", "choice"), [
            CHOICE_PUBLISH_ANONYMOUS  => get_string('publishanonymous', 'choice'),
            CHOICE_PUBLISH_NAMES      => get_string('publishnames', 'choice'),
        ]);

        $mform->hideIf('publish', 'showresults', 'eq', 0);

        $mform->addElement('selectyesno', 'showunanswered', get_string("showunanswered", "choice"));

        $mform->addElement('selectyesno', 'includeinactive', get_string('includeinactive', 'choice'));
        $mform->setDefault('includeinactive', 0);

        // -------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
        // -------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    #[\Override]
    public function data_preprocessing(&$defaultvalues) {
        global $DB;

        if (empty($this->_instance)) {
            return;
        }

        $options = $DB->get_records_menu('choice_options', ['choiceid' => $this->_instance], 'id', 'id,text');
        if (!$options) {
            return;
        }

        $options2 = $DB->get_records_menu('choice_options', ['choiceid' => $this->_instance], 'id', 'id,maxanswers');
        if (!$options2) {
            return;
        }

        $choiceids = array_keys($options);
        $options = array_values($options);
        $options2 = array_values($options2);

        foreach (array_keys($options) as $key) {
            $defaultvalues['option[' . $key . ']'] = $options[$key];
            $defaultvalues['limit[' . $key . ']'] = $options2[$key];
            $defaultvalues['optionid[' . $key . ']'] = $choiceids[$key];
        }
    }

    #[\Override]
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        // Set up completion section even if checkbox is not ticked.
        if (!empty($data->completionunlocked)) {
            $suffix = $this->get_suffix();
            if (empty($data->{'completionsubmit' . $suffix})) {
                $data->{'completionsubmit' . $suffix} = 0;
            }
        }
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check open and close times are consistent.
        if (
            $data['timeopen'] && $data['timeclose'] &&
                $data['timeclose'] < $data['timeopen']
        ) {
            $errors['timeclose'] = get_string('closebeforeopen', 'choice');
        }

        return $errors;
    }

    #[\Override]
    public function add_completion_rules() {
        $mform = &$this->_form;

        $suffix = $this->get_suffix();
        $completionsubmitel = 'completionsubmit' . $suffix;
        $mform->addElement('checkbox', $completionsubmitel, '', get_string('completionsubmit', 'choice'));
        // Enable this completion rule by default.
        $mform->setDefault($completionsubmitel, 1);
        return [$completionsubmitel];
    }

    #[\Override]
    public function completion_rule_enabled($data) {
        $suffix = $this->get_suffix();
        return !empty($data['completionsubmit' . $suffix]);
    }
}
