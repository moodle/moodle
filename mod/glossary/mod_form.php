<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_glossary_mod_form extends moodleform_mod {

    function definition() {

        global $CFG, $COURSE;
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'intro', get_string('description'));
        $mform->setType('intro', PARAM_RAW);
        $mform->addRule('intro', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('intro', array('writing', 'questions', 'text'), false, 'editorhelpbutton');

        $mform->addElement('text', 'entbypage', get_string('entbypage', 'glossary'));
        $mform->setDefault('entbypage', 10);
        $mform->addRule('entbypage', null, 'required', null, 'client');
        $mform->addRule('entbypage', null, 'numeric', null, 'client');

        if (has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
            $mform->addElement('checkbox', 'globalglossary', get_string('isglobal', 'glossary'));
            $mform->setHelpButton('globalglossary', array('globalglossary', get_string('globalglossary', 'glossary'), 'glossary'));

        }else{
            $mform->addElement('hidden', 'globalglossary');
            $mform->setType('globalglossary', PARAM_INT);
        }

        $options = array(1=>get_string('mainglossary', 'glossary'), 0=>get_string('secondaryglossary', 'glossary'));
        $mform->addElement('select', 'mainglossary', get_string('glossarytype', 'glossary'), $options);
        $mform->setHelpButton('mainglossary', array('mainglossary', get_string('mainglossary', 'glossary'), 'glossary'));
        $mform->setDefault('mainglossary', 0);

        $mform->addElement('selectyesno', 'allowduplicatedentries', get_string('allowduplicatedentries', 'glossary'));
        $mform->setDefault('allowduplicatedentries', $CFG->glossary_dupentries);
        $mform->setHelpButton('allowduplicatedentries', array('allowduplicatedentries', get_string('allowduplicatedentries', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'allowcomments', get_string('allowcomments', 'glossary'));
        $mform->setDefault('allowcomments', $CFG->glossary_allowcomments);
        $mform->setHelpButton('allowcomments', array('allowcomments', get_string('allowcomments', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'allowprintview', get_string('allowprintview', 'glossary'));
        $mform->setDefault('allowprintview', 1);
        $mform->setHelpButton('allowprintview', array('allowprintview', get_string('allowprintview', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'usedynalink', get_string('usedynalink', 'glossary'));
        $mform->setDefault('usedynalink', $CFG->glossary_linkbydefault);
        $mform->setHelpButton('usedynalink', array('usedynalink', get_string('usedynalink', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'defaultapproval', get_string('defaultapproval', 'glossary'));
        $mform->setDefault('defaultapproval', $CFG->glossary_defaultapproval);
        $mform->setHelpButton('defaultapproval', array('defaultapproval', get_string('defaultapproval', 'glossary'), 'glossary'));

        //get and update available formats
        $recformats = glossary_get_available_formats();

        $formats = array();

        //Take names
        foreach ($recformats as $format) {
           $formats[$format->name] = get_string('displayformat'.$format->name, 'glossary');
        }
        //Sort it
        asort($formats);
        $mform->addElement('select', 'displayformat', get_string('displayformat', 'glossary'), $formats);
        $mform->setDefault('displayformat', 'dictionary');
        $mform->setHelpButton('displayformat', array('displayformat', get_string('displayformat', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'showspecial', get_string('showspecial', 'glossary'));
        $mform->setDefault('showspecial', 1);
        $mform->setHelpButton('showspecial', array('shows', get_string('showspecial', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'showalphabet', get_string('showalphabet', 'glossary'));
        $mform->setDefault('showalphabet', 1);
        $mform->setHelpButton('showalphabet', array('shows', get_string('showalphabet', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'showall', get_string('showall', 'glossary'));
        $mform->setDefault('showall', 1);
        $mform->setHelpButton('showall', array('shows', get_string('showall', 'glossary'), 'glossary'));

        $mform->addElement('selectyesno', 'editalways', get_string('editalways', 'glossary'));
        $mform->setDefault('editalways', 0);
        $mform->setHelpButton('editalways', array('editalways', get_string('editalways', 'glossary'), 'glossary'));

        if ($CFG->enablerssfeeds && isset($CFG->glossary_enablerssfeeds) && $CFG->glossary_enablerssfeeds) {
//-------------------------------------------------------------------------------
            $mform->addElement('header', '', get_string('rss'));
            $choices = array();
            $choices[0] = get_string('none');
            $choices[1] = get_string('withauthor', 'glossary');
            $choices[2] = get_string('withoutauthor', 'glossary');
            $mform->addElement('select', 'rsstype', get_string('rsstype'), $choices);
            $mform->setHelpButton('rsstype', array('rsstype', get_string('rsstype'), 'glossary'));

            $choices = array();
            $choices[0] = '0';
            $choices[1] = '1';
            $choices[2] = '2';
            $choices[3] = '3';
            $choices[4] = '4';
            $choices[5] = '5';
            $choices[10] = '10';
            $choices[15] = '15';
            $choices[20] = '20';
            $choices[25] = '25';
            $choices[30] = '30';
            $choices[40] = '40';
            $choices[50] = '50';
            $mform->addElement('select', 'rssarticles', get_string('rssarticles'), $choices);
            $mform->setHelpButton('rssarticles', array('rssarticles', get_string('rssarticles'), 'glossary'));
            $mform->disabledIf('rssarticles', 'rsstype', 'eq', 0);
        }

//-------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('grade'));
        $mform->addElement('checkbox', 'userating', get_string('allowratings', 'glossary') , get_string('ratingsuse', 'glossary'));

        $options=array();
        $options[2] = get_string('ratingonlyteachers', 'glossary', moodle_strtolower($COURSE->teachers));
        $options[1] = get_string('ratingeveryone', 'glossary');
        $mform->addElement('select', 'assessed', get_string('users'), $options);
        $mform->disabledIf('assessed', 'userating');

        $mform->addElement('modgrade', 'scale', get_string('grade'), false);
        $mform->disabledIf('scale', 'userating');

        $mform->addElement('checkbox', 'ratingtime', get_string('ratingtime', 'glossary'));
        $mform->disabledIf('ratingtime', 'userating');

        $mform->addElement('date_time_selector', 'assesstimestart', get_string('from'));
        $mform->disabledIf('assesstimestart', 'userating');
        $mform->disabledIf('assesstimestart', 'ratingtime');

        $mform->addElement('date_time_selector', 'assesstimefinish', get_string('to'));
        $mform->disabledIf('assesstimefinish', 'userating');
        $mform->disabledIf('assesstimefinish', 'ratingtime');

//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements(array('groups'=>false, 'groupmembersonly'=>true));

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function definition_after_data() {
        parent::definition_after_data();
        global $COURSE;
        $mform    =& $this->_form;
        $mainglossaryel =& $mform->getElement('mainglossary');
        $mainglossary = get_record('glossary', 'mainglossary', 1, 'course', $COURSE->id);
        if ($mainglossary && ($mainglossary->id != $mform->getElementValue('instance'))){
            //secondary glossary, a main one already exists in this course.
            $mainglossaryel->setValue(0);
            $mainglossaryel->freeze();
            $mainglossaryel->setPersistantFreeze(true);
        } else {
            $mainglossaryel->unfreeze();
            $mainglossaryel->setPersistantFreeze(false);

        }
    }

    function data_preprocessing(&$default_values){
        if (empty($default_values['scale'])){
            $default_values['assessed'] = 0;
        }        

        if (empty($default_values['assessed'])){
            $default_values['userating'] = 0;
            $default_values['ratingtime'] = 0;
        } else {
            $default_values['userating'] = 1;
            $default_values['ratingtime']=
                ($default_values['assesstimestart'] && $default_values['assesstimefinish']) ? 1 : 0;
        }
    }

}
?>
