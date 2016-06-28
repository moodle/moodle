<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_choice_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $CHOICE_SHOWRESULTS, $CHOICE_PUBLISH, $CHOICE_DISPLAY, $DB;

        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('choicename', 'choice'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements(get_string('description', 'choice'));

        $mform->addElement('select', 'display', get_string("displaymode","choice"), $CHOICE_DISPLAY);

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'optionhdr', get_string('options', 'choice'));

        $mform->addElement('selectyesno', 'allowupdate', get_string("allowupdate", "choice"));

        $mform->addElement('selectyesno', 'allowmultiple', get_string('allowmultiple', 'choice'));

        $mform->addElement('selectyesno', 'limitanswers', get_string('limitanswers', 'choice'));
        $mform->addHelpButton('limitanswers', 'limitanswers', 'choice');

        $repeatarray = array();
        $repeatarray[] = $mform->createElement('text', 'option', get_string('optionno', 'choice'));
        $repeatarray[] = $mform->createElement('text', 'limit', get_string('limitno', 'choice'));
        $repeatarray[] = $mform->createElement('hidden', 'optionid', 0);

        if ($this->_instance){
            $repeatno = $DB->count_records('choice_options', array('choiceid'=>$this->_instance));
            $repeatno += 2;
        } else {
            $repeatno = 5;
        }

        $repeateloptions = array();
        $repeateloptions['limit']['default'] = 0;
        $repeateloptions['limit']['disabledif'] = array('limitanswers', 'eq', 0);
        $repeateloptions['limit']['rule'] = 'numeric';
        $repeateloptions['limit']['type'] = PARAM_INT;

        $repeateloptions['option']['helpbutton'] = array('choiceoptions', 'choice');
        $mform->setType('option', PARAM_CLEANHTML);

        $mform->setType('optionid', PARAM_INT);

        $this->repeat_elements($repeatarray, $repeatno,
                    $repeateloptions, 'option_repeats', 'option_add_fields', 3, null, true);

        // Make the first option required
        if ($mform->elementExists('option[0]')) {
            $mform->addRule('option[0]', get_string('atleastoneoption', 'choice'), 'required', null, 'client');
        }

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'timerestricthdr', get_string('availability'));
        $mform->addElement('checkbox', 'timerestrict', get_string('timerestrict', 'choice'));

        $mform->addElement('date_time_selector', 'timeopen', get_string("choiceopen", "choice"));
        $mform->disabledIf('timeopen', 'timerestrict');

        $mform->addElement('date_time_selector', 'timeclose', get_string("choiceclose", "choice"));
        $mform->disabledIf('timeclose', 'timerestrict');

        $mform->addElement('advcheckbox', 'showpreview', get_string('showpreview', 'choice'));
        $mform->addHelpButton('showpreview', 'showpreview', 'choice');
        $mform->disabledIf('showpreview', 'timerestrict');

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'resultshdr', get_string('results', 'choice'));

        $mform->addElement('select', 'showresults', get_string("publish", "choice"), $CHOICE_SHOWRESULTS);

        $mform->addElement('select', 'publish', get_string("privacy", "choice"), $CHOICE_PUBLISH);
        $mform->disabledIf('publish', 'showresults', 'eq', 0);

        $mform->addElement('selectyesno', 'showunanswered', get_string("showunanswered", "choice"));

        $mform->addElement('selectyesno', 'includeinactive', get_string('includeinactive', 'choice'));
        $mform->setDefault('includeinactive', 0);

//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values){
        global $DB;
        if (!empty($this->_instance) && ($options = $DB->get_records_menu('choice_options',array('choiceid'=>$this->_instance), 'id', 'id,text'))
               && ($options2 = $DB->get_records_menu('choice_options', array('choiceid'=>$this->_instance), 'id', 'id,maxanswers')) ) {
            $choiceids=array_keys($options);
            $options=array_values($options);
            $options2=array_values($options2);

            foreach (array_keys($options) as $key){
                $default_values['option['.$key.']'] = $options[$key];
                $default_values['limit['.$key.']'] = $options2[$key];
                $default_values['optionid['.$key.']'] = $choiceids[$key];
            }

        }
        if (empty($default_values['timeopen'])) {
            $default_values['timerestrict'] = 0;
        } else {
            $default_values['timerestrict'] = 1;
        }

    }

    function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Set up completion section even if checkbox is not ticked
        if (!empty($data->completionunlocked)) {
            if (empty($data->completionsubmit)) {
                $data->completionsubmit = 0;
            }
        }
        return $data;
    }

    /**
     * Enforce validation rules here
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array
     **/
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check open and close times are consistent.
        if ($data['timeopen'] && $data['timeclose'] &&
                $data['timeclose'] < $data['timeopen']) {
            $errors['timeclose'] = get_string('closebeforeopen', 'choice');
        }

        return $errors;
    }

    function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'completionsubmit', '', get_string('completionsubmit', 'choice'));
        return array('completionsubmit');
    }

    function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }
}

