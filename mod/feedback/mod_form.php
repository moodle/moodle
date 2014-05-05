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
 * print the form to add or edit a feedback-instance
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

//It must be included from a Moodle page
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_feedback_mod_form extends moodleform_mod {

    public function definition() {
        global $CFG, $DB;

        $editoroptions = feedback_get_editor_options();

        $mform    =& $this->_form;

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'feedback'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->add_intro_editor(true, get_string('description', 'feedback'));

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'timinghdr', get_string('availability'));

        $mform->addElement('date_time_selector', 'timeopen', get_string('feedbackopen', 'feedback'),
            array('optional' => true));

        $mform->addElement('date_time_selector', 'timeclose', get_string('feedbackclose', 'feedback'),
            array('optional' => true));

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'feedbackhdr', get_string('questionandsubmission', 'feedback'));

        $options=array();
        $options[1]  = get_string('anonymous', 'feedback');
        $options[2]  = get_string('non_anonymous', 'feedback');
        $mform->addElement('select',
                           'anonymous',
                           get_string('anonymous_edit', 'feedback'),
                           $options);

        // check if there is existing responses to this feedback
        if (is_numeric($this->_instance) AND
                    $this->_instance AND
                    $feedback = $DB->get_record("feedback", array("id"=>$this->_instance))) {

            $completed_feedback_count = feedback_get_completeds_group_count($feedback);
        } else {
            $completed_feedback_count = false;
        }

        if ($completed_feedback_count) {
            $multiple_submit_value = $feedback->multiple_submit ? get_string('yes') : get_string('no');
            $mform->addElement('text',
                               'multiple_submit_static',
                               get_string('multiplesubmit', 'feedback'),
                               array('size'=>'4',
                                    'disabled'=>'disabled',
                                    'value'=>$multiple_submit_value));
            $mform->setType('multiple_submit_static', PARAM_RAW);

            $mform->addElement('hidden', 'multiple_submit', '');
            $mform->setType('multiple_submit', PARAM_INT);
            $mform->addHelpButton('multiple_submit_static', 'multiplesubmit', 'feedback');
        } else {
            $mform->addElement('selectyesno',
                               'multiple_submit',
                               get_string('multiplesubmit', 'feedback'));

            $mform->addHelpButton('multiple_submit', 'multiplesubmit', 'feedback');
        }

        $mform->addElement('selectyesno', 'email_notification', get_string('email_notification', 'feedback'));
        $mform->addHelpButton('email_notification', 'email_notification', 'feedback');

        $mform->addElement('selectyesno', 'autonumbering', get_string('autonumbering', 'feedback'));
        $mform->addHelpButton('autonumbering', 'autonumbering', 'feedback');

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'aftersubmithdr', get_string('after_submit', 'feedback'));

        $mform->addElement('selectyesno', 'publish_stats', get_string('show_analysepage_after_submit', 'feedback'));

        $mform->addElement('editor',
                           'page_after_submit_editor',
                           get_string("page_after_submit", "feedback"),
                           null,
                           $editoroptions);

        $mform->setType('page_after_submit_editor', PARAM_RAW);

        $mform->addElement('text',
                           'site_after_submit',
                           get_string('url_for_continue', 'feedback'),
                           array('size'=>'64', 'maxlength'=>'255'));

        $mform->setType('site_after_submit', PARAM_TEXT);
        $mform->addHelpButton('site_after_submit', 'url_for_continue', 'feedback');
        //-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    public function data_preprocessing(&$default_values) {

        $editoroptions = feedback_get_editor_options();

        if ($this->current->instance) {
            // editing an existing feedback - let us prepare the added editor elements (intro done automatically)
            $draftitemid = file_get_submitted_draft_itemid('page_after_submit');
            $default_values['page_after_submit_editor']['text'] =
                                    file_prepare_draft_area($draftitemid, $this->context->id,
                                    'mod_feedback', 'page_after_submit', false,
                                    $editoroptions,
                                    $default_values['page_after_submit']);

            $default_values['page_after_submit_editor']['format'] = $default_values['page_after_submitformat'];
            $default_values['page_after_submit_editor']['itemid'] = $draftitemid;
        } else {
            // adding a new feedback instance
            $draftitemid = file_get_submitted_draft_itemid('page_after_submit_editor');

            // no context yet, itemid not used
            file_prepare_draft_area($draftitemid, null, 'mod_feedback', 'page_after_submit', false);
            $default_values['page_after_submit_editor']['text'] = '';
            $default_values['page_after_submit_editor']['format'] = editors_get_preferred_format();
            $default_values['page_after_submit_editor']['itemid'] = $draftitemid;
        }

    }

    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->page_after_submitformat = $data->page_after_submit_editor['format'];
            $data->page_after_submit = $data->page_after_submit_editor['text'];

            if (!empty($data->completionunlocked)) {
                // Turn off completion settings if the checkboxes aren't ticked
                $autocompletion = !empty($data->completion) &&
                    $data->completion == COMPLETION_TRACKING_AUTOMATIC;
                if (!$autocompletion || empty($data->completionsubmit)) {
                    $data->completionsubmit=0;
                }
            }
        }

        return $data;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }

    public function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('checkbox',
                           'completionsubmit',
                           '',
                           get_string('completionsubmit', 'feedback'));
        return array('completionsubmit');
    }

    public function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }
}
