<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_glossary_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $COURSE, $DB;

        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $this->add_intro_editor(true);

        $mform->addElement('text', 'entbypage', get_string('entbypage', 'glossary'));
        $mform->setDefault('entbypage', 10);
        $mform->addRule('entbypage', null, 'required', null, 'client');
        $mform->addRule('entbypage', null, 'numeric', null, 'client');

        if (has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
            $mform->addElement('checkbox', 'globalglossary', get_string('isglobal', 'glossary'));
            $mform->addHelpButton('globalglossary', 'isglobal', 'glossary');

        }else{
            $mform->addElement('hidden', 'globalglossary');
            $mform->setType('globalglossary', PARAM_INT);
        }

        $options = array(1=>get_string('mainglossary', 'glossary'), 0=>get_string('secondaryglossary', 'glossary'));
        $mform->addElement('select', 'mainglossary', get_string('glossarytype', 'glossary'), $options);
        $mform->addHelpButton('mainglossary', 'glossarytype', 'glossary');
        $mform->setDefault('mainglossary', 0);

        $mform->addElement('selectyesno', 'allowduplicatedentries', get_string('allowduplicatedentries', 'glossary'));
        $mform->setDefault('allowduplicatedentries', $CFG->glossary_dupentries);
        $mform->addHelpButton('allowduplicatedentries', 'allowduplicatedentries', 'glossary');

        $mform->addElement('selectyesno', 'allowcomments', get_string('allowcomments', 'glossary'));
        $mform->setDefault('allowcomments', $CFG->glossary_allowcomments);
        $mform->addHelpButton('allowcomments', 'allowcomments', 'glossary');

        $mform->addElement('selectyesno', 'allowprintview', get_string('allowprintview', 'glossary'));
        $mform->setDefault('allowprintview', 1);
        $mform->addHelpButton('allowprintview', 'allowprintview', 'glossary');

        $mform->addElement('selectyesno', 'usedynalink', get_string('usedynalink', 'glossary'));
        $mform->setDefault('usedynalink', $CFG->glossary_linkbydefault);
        $mform->addHelpButton('usedynalink', 'usedynalink', 'glossary');

        $mform->addElement('selectyesno', 'defaultapproval', get_string('defaultapproval', 'glossary'));
        $mform->setDefault('defaultapproval', $CFG->glossary_defaultapproval);
        $mform->addHelpButton('defaultapproval', 'defaultapproval', 'glossary');

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
        $mform->addHelpButton('displayformat', 'displayformat', 'glossary');

        $mform->addElement('selectyesno', 'showspecial', get_string('showspecial', 'glossary'));
        $mform->setDefault('showspecial', 1);
        $mform->addHelpButton('showspecial', 'showspecial', 'glossary');

        $mform->addElement('selectyesno', 'showalphabet', get_string('showalphabet', 'glossary'));
        $mform->setDefault('showalphabet', 1);
        $mform->addHelpButton('showalphabet', 'showalphabet', 'glossary');

        $mform->addElement('selectyesno', 'showall', get_string('showall', 'glossary'));
        $mform->setDefault('showall', 1);
        $mform->addHelpButton('showall', 'showall', 'glossary');

        $mform->addElement('selectyesno', 'editalways', get_string('editalways', 'glossary'));
        $mform->setDefault('editalways', 0);
        $mform->addHelpButton('editalways', 'editalways', 'glossary');

        if ($CFG->enablerssfeeds && isset($CFG->glossary_enablerssfeeds) && $CFG->glossary_enablerssfeeds) {
//-------------------------------------------------------------------------------
            $mform->addElement('header', '', get_string('rss'));
            $choices = array();
            $choices[0] = get_string('none');
            $choices[1] = get_string('withauthor', 'glossary');
            $choices[2] = get_string('withoutauthor', 'glossary');
            $mform->addElement('select', 'rsstype', get_string('rsstype'), $choices);
            $mform->addHelpButton('rsstype', 'rsstype', 'glossary');

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
            $mform->addHelpButton('rssarticles', 'rssarticles', 'glossary');
            $mform->disabledIf('rssarticles', 'rsstype', 'eq', 0);
        }

//-------------------------------------------------------------------------------

        $this->standard_grading_coursemodule_elements();

        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function definition_after_data() {
        global $COURSE, $DB;

        parent::definition_after_data();
        $mform    =& $this->_form;
        $mainglossaryel =& $mform->getElement('mainglossary');
        $mainglossary = $DB->get_record('glossary', array('mainglossary'=>1, 'course'=>$COURSE->id));
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
        parent::data_preprocessing($default_values);

        // Set up the completion checkboxes which aren't part of standard data.
        // We also make the default value (if you turn on the checkbox) for those
        // numbers to be 1, this will not apply unless checkbox is ticked.
        $default_values['completionentriesenabled']=
            !empty($default_values['completionentries']) ? 1 : 0;
        if (empty($default_values['completionentries'])) {
            $default_values['completionentries']=1;
        }
    }

    function add_completion_rules() {
        $mform =& $this->_form;

        $group=array();
        $group[] =& $mform->createElement('checkbox', 'completionentriesenabled', '', get_string('completionentries','glossary'));
        $group[] =& $mform->createElement('text', 'completionentries', '', array('size'=>3));
        $mform->setType('completionentries', PARAM_INT);
        $mform->addGroup($group, 'completionentriesgroup', get_string('completionentriesgroup','glossary'), array(' '), false);
        $mform->disabledIf('completionentries','completionentriesenabled','notchecked');

        return array('completionentriesgroup');
    }

    function completion_rule_enabled($data) {
        return (!empty($data['completionentriesenabled']) && $data['completionentries']!=0);
    }

    function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Turn off completion settings if the checkboxes aren't ticked
        $autocompletion = !empty($data->completion) && $data->completion==COMPLETION_TRACKING_AUTOMATIC;
        if (empty($data->completionentriesenabled) || !$autocompletion) {
            $data->completionentries = 0;
        }
        return $data;
    }

}

