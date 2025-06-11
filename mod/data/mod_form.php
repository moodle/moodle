<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_data_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB, $OUTPUT;

        $mform =& $this->_form;

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements(get_string('intro', 'data'));

        // ----------------------------------------------------------------------
        $mform->addElement('header', 'entrieshdr', get_string('entries', 'data'));

        $mform->addElement('selectyesno', 'approval', get_string('requireapproval', 'data'));
        $mform->addHelpButton('approval', 'requireapproval', 'data');

        $mform->addElement('selectyesno', 'manageapproved', get_string('manageapproved', 'data'));
        $mform->addHelpButton('manageapproved', 'manageapproved', 'data');
        $mform->setDefault('manageapproved', 1);
        $mform->hideIf('manageapproved', 'approval', 'eq', 0);

        $mform->addElement('selectyesno', 'comments', get_string('allowcomments', 'data'));
        if (empty($CFG->usecomments)) {
            $mform->hardFreeze('comments');
            $mform->setConstant('comments', 0);
        }

        $countoptions = array(0=>get_string('none'))+
                        (array_combine(range(1, DATA_MAX_ENTRIES), // Keys.
                                        range(1, DATA_MAX_ENTRIES))); // Values.
        /*only show fields if there are legacy values from
         *before completionentries was added*/
        if (!empty($this->current->requiredentries)) {
            $group = array();
            $group[] = $mform->createElement('select', 'requiredentries',
                    get_string('requiredentries', 'data'), $countoptions);
            $mform->addGroup($group, 'requiredentriesgroup', get_string('requiredentries', 'data'), array(''), false);
            $mform->addHelpButton('requiredentriesgroup', 'requiredentries', 'data');
            $mform->addElement('html', $OUTPUT->notification( get_string('requiredentrieswarning', 'data')));
        }

        $mform->addElement('select', 'requiredentriestoview', get_string('requiredentriestoview', 'data'), $countoptions);
        $mform->addHelpButton('requiredentriestoview', 'requiredentriestoview', 'data');

        $mform->addElement('select', 'maxentries', get_string('maxentries', 'data'), $countoptions);
        $mform->addHelpButton('maxentries', 'maxentries', 'data');

        // ----------------------------------------------------------------------
        $mform->addElement('header', 'availibilityhdr', get_string('availability'));

        $mform->addElement('date_time_selector', 'timeavailablefrom', get_string('availablefromdate', 'data'),
                           array('optional' => true));

        $mform->addElement('date_time_selector', 'timeavailableto', get_string('availabletodate', 'data'),
                           array('optional' => true));

        $mform->addElement('date_time_selector', 'timeviewfrom', get_string('viewfromdate', 'data'),
                           array('optional' => true));

        $mform->addElement('date_time_selector', 'timeviewto', get_string('viewtodate', 'data'),
                           array('optional' => true));

        // ----------------------------------------------------------------------
        if ($CFG->enablerssfeeds && $CFG->data_enablerssfeeds) {
            $mform->addElement('header', 'rsshdr', get_string('rss'));
            $mform->addElement('select', 'rssarticles', get_string('numberrssarticles', 'data') , $countoptions);
        }

        $this->standard_grading_coursemodule_elements();

        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
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
        if ($data['timeavailablefrom'] && $data['timeavailableto'] &&
                $data['timeavailableto'] < $data['timeavailablefrom']) {
            $errors['timeavailableto'] = get_string('availabletodatevalidation', 'data');
        }
        if ($data['timeviewfrom'] && $data['timeviewto'] &&
                $data['timeviewto'] < $data['timeviewfrom']) {
            $errors['timeviewto'] = get_string('viewtodatevalidation', 'data');
        }

        return $errors;
    }

    /**
     * Display module-specific activity completion rules.
     * Part of the API defined by moodleform_mod
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        $mform = & $this->_form;
        $group = [];

        $suffix = $this->get_suffix();
        $completionentriesenabledel = 'completionentriesenabled' . $suffix;
        $group[] = $mform->createElement(
            'checkbox',
            $completionentriesenabledel,
            '',
            get_string('completionentriescount', 'data')
        );
        $completionentriesel = 'completionentries' . $suffix;
        $group[] = $mform->createElement(
            'text',
            $completionentriesel,
            get_string('completionentriescount', 'data'),
            ['size' => '1']
        );

        $completionentriesgroupel = 'completionentriesgroup' . $suffix;
        $mform->addGroup(
            $group,
            $completionentriesgroupel,
            '',
            [' '],
            false
        );
        $mform->hideIf($completionentriesel, $completionentriesenabledel, 'notchecked');
        $mform->setDefault($completionentriesel, 1);
        $mform->setType($completionentriesel, PARAM_INT);
        /* This ensures the elements are disabled unless completion rules are enabled */
        return [$completionentriesgroupel];
    }

    /**
     * Called during validation. Indicates if a module-specific completion rule is selected.
     *
     * @param array $data
     * @return bool True if one or more rules is enabled, false if none are.
     */
    public function completion_rule_enabled($data) {
        $suffix = $this->get_suffix();
        return (!empty($data['completionentriesenabled' . $suffix]) && $data['completionentries' . $suffix] != 0);
    }

      /**
       * Set up the completion checkbox which is not part of standard data.
       *
       * @param array $defaultvalues
       *
       */
    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        $suffix = $this->get_suffix();
        $completionentriesenabledel = 'completionentriesenabled' . $suffix;
        $completionentriesel = 'completionentries' . $suffix;
        $defaultvalues[$completionentriesenabledel] = !empty($defaultvalues[$completionentriesel]) ? 1 : 0;
        if (empty($defaultvalues[$completionentriesel])) {
            $defaultvalues[$completionentriesel] = 1;
        }
    }

    /**
     * Allows modules to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        if (!empty($data->completionunlocked)) {
            $suffix = $this->get_suffix();
            $completionel = 'completion' . $suffix;
            $completionentriesenabledel = 'completionentriesenabled' . $suffix;
            $autocompletion = !empty($data->{$completionel}) && $data->{$completionel} == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->{$completionentriesenabledel}) || !$autocompletion) {
                $completionentriesel = 'completionentries' . $suffix;
                $data->{$completionentriesel} = 0;
            }
        }
    }

}
