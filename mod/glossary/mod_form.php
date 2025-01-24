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
        $mform->addRule('name', get_string('maximumchars', '', 1333), 'maxlength', 1333, 'client');

        $this->standard_intro_elements();

        if (has_capability('mod/glossary:manageentries', context_system::instance())) {
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

        // ----------------------------------------------------------------------
        $mform->addElement('header', 'entrieshdr', get_string('entries', 'glossary'));

        $mform->addElement('selectyesno', 'defaultapproval', get_string('defaultapproval', 'glossary'));
        $mform->setDefault('defaultapproval', $CFG->glossary_defaultapproval);
        $mform->addHelpButton('defaultapproval', 'defaultapproval', 'glossary');

        $mform->addElement('selectyesno', 'editalways', get_string('editalways', 'glossary'));
        $mform->setDefault('editalways', 0);
        $mform->addHelpButton('editalways', 'editalways', 'glossary');

        $mform->addElement('selectyesno', 'allowduplicatedentries', get_string('allowduplicatedentries', 'glossary'));
        $mform->setDefault('allowduplicatedentries', $CFG->glossary_dupentries);
        $mform->addHelpButton('allowduplicatedentries', 'allowduplicatedentries', 'glossary');

        $mform->addElement('selectyesno', 'allowcomments', get_string('allowcomments', 'glossary'));
        $mform->setDefault('allowcomments', $CFG->glossary_allowcomments);
        $mform->addHelpButton('allowcomments', 'allowcomments', 'glossary');

        $mform->addElement('selectyesno', 'usedynalink', get_string('usedynalink', 'glossary'));
        $mform->setDefault('usedynalink', $CFG->glossary_linkbydefault);
        $mform->addHelpButton('usedynalink', 'usedynalink', 'glossary');

        // ----------------------------------------------------------------------
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));

        // Get and update available formats.
        $recformats = glossary_get_available_formats();
        $formats = array();
        foreach ($recformats as $format) {
           $formats[$format->name] = get_string('displayformat'.$format->name, 'glossary');
        }
        asort($formats);
        $mform->addElement('select', 'displayformat', get_string('displayformat', 'glossary'), $formats);
        $mform->setDefault('displayformat', 'dictionary');
        $mform->addHelpButton('displayformat', 'displayformat', 'glossary');

        $displayformats['default'] = get_string('displayformatdefault', 'glossary');
        $displayformats = array_merge($displayformats, $formats);
        $mform->addElement('select', 'approvaldisplayformat', get_string('approvaldisplayformat', 'glossary'), $displayformats);
        $mform->setDefault('approvaldisplayformat', 'default');
        $mform->addHelpButton('approvaldisplayformat', 'approvaldisplayformat', 'glossary');

        $mform->addElement('text', 'entbypage', get_string('entbypage', 'glossary'));
        $mform->setDefault('entbypage', $this->get_default_entbypage());
        $mform->addRule('entbypage', null, 'numeric', null, 'client');
        $mform->setType('entbypage', PARAM_INT);

        $mform->addElement('selectyesno', 'showalphabet', get_string('showalphabet', 'glossary'));
        $mform->setDefault('showalphabet', 1);
        $mform->addHelpButton('showalphabet', 'showalphabet', 'glossary');

        $mform->addElement('selectyesno', 'showall', get_string('showall', 'glossary'));
        $mform->setDefault('showall', 1);
        $mform->addHelpButton('showall', 'showall', 'glossary');

        $mform->addElement('selectyesno', 'showspecial', get_string('showspecial', 'glossary'));
        $mform->setDefault('showspecial', 1);
        $mform->addHelpButton('showspecial', 'showspecial', 'glossary');

        $mform->addElement('selectyesno', 'allowprintview', get_string('allowprintview', 'glossary'));
        $mform->setDefault('allowprintview', 1);
        $mform->addHelpButton('allowprintview', 'allowprintview', 'glossary');

        if ($CFG->enablerssfeeds && isset($CFG->glossary_enablerssfeeds) && $CFG->glossary_enablerssfeeds) {
//-------------------------------------------------------------------------------
            $mform->addElement('header', 'rssheader', get_string('rss'));
            $choices = array();
            $choices[0] = get_string('none');
            $choices[1] = get_string('withauthor', 'glossary');
            $choices[2] = get_string('withoutauthor', 'glossary');
            $mform->addElement('select', 'rsstype', get_string('rsstype', 'glossary'), $choices);
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
            $mform->hideIf('rssarticles', 'rsstype', 'eq', 0);
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

    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        // Fallsback on the default setting if 'Entries shown per page' has been left blank.
        // This prevents the field from being required and expand its section which should not
        // be the case if there is a default value defined.
        if (empty($defaultvalues['entbypage']) || $defaultvalues['entbypage'] < 0) {
            $defaultvalues['entbypage'] = $this->get_default_entbypage();
        }

        $suffix = $this->get_suffix();
        $completionentriesel = 'completionentries' . $suffix;
        $completionentriesenabledel = 'completionentriesenabled' . $suffix;

        // Set up the completion checkboxes which aren't part of standard data.
        // Tick by default if Add mode or if completion entries settings is set to 1 or more.
        if (empty($this->_instance) || !empty($defaultvalues[$completionentriesel])) {
            $defaultvalues[$completionentriesenabledel] = 1;
        } else {
            $defaultvalues[$completionentriesenabledel] = 0;
        }
        if (empty($defaultvalues[$completionentriesel])) {
            $defaultvalues[$completionentriesel] = 1;
        }
    }

    public function add_completion_rules() {
        $mform = $this->_form;
        $suffix = $this->get_suffix();

        $group = [];
        $completionentriesenabledel = 'completionentriesenabled' . $suffix;
        $group[] =& $mform->createElement(
            'checkbox',
            $completionentriesenabledel,
            '',
            get_string('completionentries', 'glossary')
        );
        $completionentriesel = 'completionentries' . $suffix;
        $group[] =& $mform->createElement('text', $completionentriesel, '', ['size' => 3]);
        $mform->setType($completionentriesel, PARAM_INT);
        $completionentriesgroupel = 'completionentriesgroup' . $suffix;
        $mform->addGroup($group, $completionentriesgroupel, '', ' ', false);
        $mform->hideIf($completionentriesel, $completionentriesenabledel, 'notchecked');

        return [$completionentriesgroupel];
    }

    public function completion_rule_enabled($data) {
        $suffix = $this->get_suffix();
        return (!empty($data['completionentriesenabled' . $suffix]) && $data['completionentries' . $suffix] != 0);
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        if (!empty($data->completionunlocked)) {
            // Turn off completion settings if the checkboxes aren't ticked.
            $suffix = $this->get_suffix();
            $completion = $data->{'completion' . $suffix};
            $autocompletion = !empty($completion) && $completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->{'completionentriesenabled' . $suffix}) || !$autocompletion) {
                $data->{'completionentries' . $suffix} = 0;
            }
        }
    }

    /**
     * Returns the default value for 'Entries shown per page'.
     *
     * @return int default for number of entries per page.
     */
    protected function get_default_entbypage() {
        global $CFG;
        return !empty($CFG->glossary_entbypage) ? $CFG->glossary_entbypage : 10;
    }

}
