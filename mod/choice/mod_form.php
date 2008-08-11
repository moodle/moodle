<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_choice_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $CHOICE_SHOWRESULTS, $CHOICE_PUBLISH, $CHOICE_DISPLAY;

        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('choicename', 'choice'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'text', get_string('choicetext', 'choice'));
        $mform->setType('text', PARAM_RAW);
        $mform->addRule('text', null, 'required', null, 'client');
        $mform->setHelpButton('text', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('format', 'format', get_string('format'));

//-------------------------------------------------------------------------------
        $repeatarray=array();
        $repeatarray[] = &MoodleQuickForm::createElement('header', '', get_string('choice','choice').' {no}');
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'option', get_string('choice','choice'));
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'limit', get_string('limit','choice'));
        $repeatarray[] = &MoodleQuickForm::createElement('hidden', 'optionid', 0);

        $menuoptions=array();
        $menuoptions[0] = get_string('disable');
        $menuoptions[1] = get_string('enable');
        $mform->addElement('header', 'timerestricthdr', get_string('limit', 'choice'));
        $mform->addElement('select', 'limitanswers', get_string('limitanswers', 'choice'), $menuoptions);
        $mform->setHelpButton('limitanswers', array('limit', get_string('limit', 'choice'), 'choice'));

        if ($this->_instance){
            $repeatno=count_records('choice_options', 'choiceid', $this->_instance);
            $repeatno += 2;
        } else {
            $repeatno = 5;
        }

        $repeateloptions = array();
        $repeateloptions['limit']['default'] = 0;
        $repeateloptions['limit']['disabledif'] = array('limitanswers', 'eq', 0);
        $mform->setType('limit', PARAM_INT);

        $repeateloptions['option']['helpbutton'] = array('options', get_string('modulenameplural', 'choice'), 'choice');
        $mform->setType('option', PARAM_CLEAN);

        $mform->setType('optionid', PARAM_INT);

        $this->repeat_elements($repeatarray, $repeatno,
                    $repeateloptions, 'option_repeats', 'option_add_fields', 3);




//-------------------------------------------------------------------------------
        $mform->addElement('header', 'timerestricthdr', get_string('timerestrict', 'choice'));
        $mform->addElement('checkbox', 'timerestrict', get_string('timerestrict', 'choice'));
        $mform->setHelpButton('timerestrict', array("timerestrict", get_string("timerestrict","choice"), "choice"));


        $mform->addElement('date_time_selector', 'timeopen', get_string("choiceopen", "choice"));
        $mform->disabledIf('timeopen', 'timerestrict');

        $mform->addElement('date_time_selector', 'timeclose', get_string("choiceclose", "choice"));
        $mform->disabledIf('timeclose', 'timerestrict');

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'miscellaneoussettingshdr', get_string('miscellaneoussettings', 'form'));

        $mform->addElement('select', 'display', get_string("displaymode","choice"), $CHOICE_DISPLAY);

        $mform->addElement('select', 'showresults', get_string("publish", "choice"), $CHOICE_SHOWRESULTS);

        $mform->addElement('select', 'publish', get_string("privacy", "choice"), $CHOICE_PUBLISH);
        $mform->disabledIf('publish', 'showresults', 'eq', 0);

        $mform->addElement('selectyesno', 'allowupdate', get_string("allowupdate", "choice"));

        $mform->addElement('selectyesno', 'showunanswered', get_string("showunanswered", "choice"));


//-------------------------------------------------------------------------------
        $features = new stdClass;
        $features->groups = true;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $features->gradecat = false;
        $this->standard_coursemodule_elements($features);
//-------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values){
        if (!empty($this->_instance) && ($options = get_records_menu('choice_options','choiceid', $this->_instance, 'id', 'id,text'))
               && ($options2 = get_records_menu('choice_options','choiceid', $this->_instance, 'id', 'id,maxanswers')) ) {
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

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $choices = 0;
        foreach ($data['option'] as $option){
            if (trim($option) != ''){
                $choices++;
            }
        }

        if ($choices < 1) {
           $errors['option[0]'] = get_string('fillinatleastoneoption', 'choice');
        }

        if ($choices < 2) {
           $errors['option[1]'] = get_string('fillinatleastoneoption', 'choice');
        }

        return $errors;
    }

}
?>
