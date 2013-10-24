<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/gradelib.php');

/**
 * Default form for editing course section
 *
 * Course format plugins may specify different editing form to use
 */
class editsection_form extends moodleform {

    function definition() {

        $mform  = $this->_form;
        $course = $this->_customdata['course'];

        $mform->addElement('header', 'generalhdr', get_string('general'));

        $elementgroup = array();
        $elementgroup[] = $mform->createElement('text', 'name', '', array('size' => '30', 'maxlength' => '255'));
        $elementgroup[] = $mform->createElement('checkbox', 'usedefaultname', '', get_string('sectionusedefaultname'));
        $mform->addGroup($elementgroup, 'name_group', get_string('sectionname'), ' ', false);
        $mform->addGroupRule('name_group', array('name' => array(array(get_string('maximumchars', '', 255), 'maxlength', 255))));

        $mform->setDefault('usedefaultname', true);
        $mform->setType('name', PARAM_TEXT);
        $mform->disabledIf('name','usedefaultname','checked');

        /// Prepare course and the editor

        $mform->addElement('editor', 'summary_editor', get_string('summary'), null, $this->_customdata['editoroptions']);
        $mform->addHelpButton('summary_editor', 'summary');
        $mform->setType('summary_editor', PARAM_RAW);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // additional fields that course format has defined
        $courseformat = course_get_format($course);
        $formatoptions = $courseformat->section_format_options(true);
        if (!empty($formatoptions)) {
            $elements = $courseformat->create_edit_form_elements($mform, true);
        }

        $mform->_registerCancelButton('cancel');
    }

    public function definition_after_data() {
        global $CFG, $DB;

        $mform  = $this->_form;
        $course = $this->_customdata['course'];
        $context = context_course::instance($course->id);

        if (!empty($CFG->enableavailability)) {
            $mform->addElement('header', 'availabilityconditions', get_string('availabilityconditions', 'condition'));
            $mform->setExpanded('availabilityconditions', false);
            // String used by conditions more than once
            $strcondnone = get_string('none', 'condition');
            // Grouping conditions - only if grouping is enabled at site level
            if (!empty($CFG->enablegroupmembersonly)) {
                $options = array();
                $options[0] = get_string('none');
                if ($groupings = $DB->get_records('groupings', array('courseid' => $course->id))) {
                    foreach ($groupings as $grouping) {
                        $options[$grouping->id] = format_string(
                                $grouping->name, true, array('context' => $context));
                    }
                }
                $mform->addElement('select', 'groupingid', get_string('groupingsection', 'group'), $options);
                $mform->addHelpButton('groupingid', 'groupingsection', 'group');
            }

            // Available from/to defaults to midnight because then the display
            // will be nicer where it tells users when they can access it (it
            // shows only the date and not time).
            $date = usergetdate(time());
            $midnight = make_timestamp($date['year'], $date['mon'], $date['mday']);

            // Date and time conditions.
            $mform->addElement('date_time_selector', 'availablefrom',
                    get_string('availablefrom', 'condition'),
                    array('optional' => true, 'defaulttime' => $midnight));
            $mform->addElement('date_time_selector', 'availableuntil',
                    get_string('availableuntil', 'condition'),
                    array('optional' => true, 'defaulttime' => $midnight));

            // Conditions based on grades
            $gradeoptions = array();
            $items = grade_item::fetch_all(array('courseid' => $course->id));
            $items = $items ? $items : array();
            foreach ($items as $id => $item) {
                $gradeoptions[$id] = $item->get_name();
            }
            asort($gradeoptions);
            $gradeoptions = array(0 => $strcondnone) + $gradeoptions;

            $grouparray = array();
            $grouparray[] = $mform->createElement('select', 'conditiongradeitemid', '', $gradeoptions);
            $grouparray[] = $mform->createElement('static', '', '',
                    ' ' . get_string('grade_atleast', 'condition').' ');
            $grouparray[] = $mform->createElement('text', 'conditiongrademin', '', array('size' => 3));
            $grouparray[] = $mform->createElement('static', '', '',
                    '% ' . get_string('grade_upto', 'condition') . ' ');
            $grouparray[] = $mform->createElement('text', 'conditiongrademax', '', array('size' => 3));
            $grouparray[] = $mform->createElement('static', '', '', '%');
            $group = $mform->createElement('group', 'conditiongradegroup',
                    get_string('gradecondition', 'condition'), $grouparray);

            // Get full version (including condition info) of section object
            $ci = new condition_info_section($this->_customdata['cs']);
            $fullcs = $ci->get_full_section();
            $count = count($fullcs->conditionsgrade) + 1;

            // Grade conditions
            $this->repeat_elements(array($group), $count, array(
                'conditiongradegroup[conditiongrademin]' => array('type' => PARAM_RAW),
                'conditiongradegroup[conditiongrademax]' => array('type' => PARAM_RAW)
                ), 'conditiongraderepeats', 'conditiongradeadds', 2, get_string('addgrades', 'condition'), true);
            $mform->addHelpButton('conditiongradegroup[0]', 'gradecondition', 'condition');

            // Conditions based on user fields
            $operators = condition_info::get_condition_user_field_operators();
            $useroptions = condition_info::get_condition_user_fields(array('context' => $context));
            asort($useroptions);

            $useroptions = array(0 => $strcondnone) + $useroptions;
            $grouparray = array();
            $grouparray[] =& $mform->createElement('select', 'conditionfield', '', $useroptions);
            $grouparray[] =& $mform->createElement('select', 'conditionfieldoperator', '', $operators);
            $grouparray[] =& $mform->createElement('text', 'conditionfieldvalue');
            $group = $mform->createElement('group', 'conditionfieldgroup', get_string('userfield', 'condition'), $grouparray);

            $fieldcount = count($fullcs->conditionsfield) + 1;

            $this->repeat_elements(array($group), $fieldcount,  array(
                'conditionfieldgroup[conditionfieldvalue]' => array('type' => PARAM_RAW)),
                'conditionfieldrepeats', 'conditionfieldadds', 2, get_string('adduserfields', 'condition'), true);
            $mform->addHelpButton('conditionfieldgroup[0]', 'userfield', 'condition');

            // Conditions based on completion
            $completion = new completion_info($course);
            if ($completion->is_enabled()) {
                $completionoptions = array();
                $modinfo = get_fast_modinfo($course);
                foreach ($modinfo->cms as $id => $cm) {
                    // Add each course-module if it:
                    // (a) has completion turned on
                    // (b) does not belong to current course-section
                    if ($cm->completion && ($fullcs->id != $cm->section)) {
                        $completionoptions[$id] = $cm->name;
                    }
                }
                asort($completionoptions);
                $completionoptions = array(0 => $strcondnone) +
                        $completionoptions;

                $completionvalues = array(
                    COMPLETION_COMPLETE => get_string('completion_complete', 'condition'),
                    COMPLETION_INCOMPLETE => get_string('completion_incomplete', 'condition'),
                    COMPLETION_COMPLETE_PASS => get_string('completion_pass', 'condition'),
                    COMPLETION_COMPLETE_FAIL => get_string('completion_fail', 'condition'));

                $grouparray = array();
                $grouparray[] = $mform->createElement('select', 'conditionsourcecmid', '',
                        $completionoptions);
                $grouparray[] = $mform->createElement('select', 'conditionrequiredcompletion', '',
                        $completionvalues);
                $group = $mform->createElement('group', 'conditioncompletiongroup',
                        get_string('completioncondition', 'condition'), $grouparray);

                $count = count($fullcs->conditionscompletion) + 1;
                $this->repeat_elements(array($group), $count, array(),
                        'conditioncompletionrepeats', 'conditioncompletionadds', 2,
                        get_string('addcompletions', 'condition'), true);
                $mform->addHelpButton('conditioncompletiongroup[0]',
                        'completionconditionsection', 'condition');
            }

            // Availability conditions - set up form values
            if (!empty($CFG->enableavailability)) {
                $num = 0;
                foreach ($fullcs->conditionsgrade as $gradeitemid => $minmax) {
                    $groupelements = $mform->getElement(
                            'conditiongradegroup[' . $num . ']')->getElements();
                    $groupelements[0]->setValue($gradeitemid);
                    $groupelements[2]->setValue(is_null($minmax->min) ? '' :
                            format_float($minmax->min, 5, true, true));
                    $groupelements[4]->setValue(is_null($minmax->max) ? '' :
                            format_float($minmax->max, 5, true, true));
                    $num++;
                }

                $num = 0;
                foreach ($fullcs->conditionsfield as $fieldid => $data) {
                    $groupelements = $mform->getElement(
                            'conditionfieldgroup[' . $num . ']')->getElements();
                    $groupelements[0]->setValue($fieldid);
                    $groupelements[1]->setValue(is_null($data->operator) ? '' :
                            $data->operator);
                    $groupelements[2]->setValue(is_null($data->value) ? '' :
                            $data->value);
                    $num++;
                }

                if ($completion->is_enabled()) {
                    $num = 0;
                    foreach ($fullcs->conditionscompletion as $othercmid => $state) {
                        $groupelements = $mform->getElement('conditioncompletiongroup[' . $num . ']')->getElements();
                        $groupelements[0]->setValue($othercmid);
                        $groupelements[1]->setValue($state);
                        $num++;
                    }
                }
            }

            // Do we display availability info to students?
            $showhide = array(
                CONDITION_STUDENTVIEW_SHOW => get_string('showavailabilitysection_show', 'condition'),
                CONDITION_STUDENTVIEW_HIDE => get_string('showavailabilitysection_hide', 'condition'));
            $mform->addElement('select', 'showavailability',
                    get_string('showavailabilitysection', 'condition'), $showhide);
        }

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // Conditions: Don't let them set dates which make no sense
        if (array_key_exists('availablefrom', $data) &&
                $data['availablefrom'] && $data['availableuntil'] &&
                $data['availablefrom'] >= $data['availableuntil']) {
            $errors['availablefrom'] = get_string('badavailabledates', 'condition');
        }

        // Conditions: Verify that the grade conditions are numbers, and make sense.
        if (array_key_exists('conditiongradegroup', $data)) {
            foreach ($data['conditiongradegroup'] as $i => $gradedata) {
                if ($gradedata['conditiongrademin'] !== '' &&
                        !is_numeric(unformat_float($gradedata['conditiongrademin']))) {
                    $errors["conditiongradegroup[{$i}]"] = get_string('gradesmustbenumeric', 'condition');
                    continue;
                }
                if ($gradedata['conditiongrademax'] !== '' &&
                        !is_numeric(unformat_float($gradedata['conditiongrademax']))) {
                    $errors["conditiongradegroup[{$i}]"] = get_string('gradesmustbenumeric', 'condition');
                    continue;
                }
                if ($gradedata['conditiongrademin'] !== '' && $gradedata['conditiongrademax'] !== '' &&
                        unformat_float($gradedata['conditiongrademax']) <= unformat_float($gradedata['conditiongrademin'])) {
                    $errors["conditiongradegroup[{$i}]"] = get_string('badgradelimits', 'condition');
                    continue;
                }
                if ($gradedata['conditiongrademin'] === '' && $gradedata['conditiongrademax'] === '' &&
                        $gradedata['conditiongradeitemid']) {
                    $errors["conditiongradegroup[{$i}]"] = get_string('gradeitembutnolimits', 'condition');
                    continue;
                }
                if (($gradedata['conditiongrademin'] !== '' || $gradedata['conditiongrademax'] !== '') &&
                        !$gradedata['conditiongradeitemid']) {
                    $errors["conditiongradegroup[{$i}]"] = get_string('gradelimitsbutnoitem', 'condition');
                    continue;
                }
            }
        }

        // Conditions: Verify that the user profile field has not been declared more than once
        if (array_key_exists('conditionfieldgroup', $data)) {
            // Array to store the existing fields
            $arrcurrentfields = array();
            // Error message displayed if any condition is declared more than once. We use lang string because
            // this way we don't actually generate the string unless there is an error.
            $stralreadydeclaredwarning = new lang_string('fielddeclaredmultipletimes', 'condition');
            foreach ($data['conditionfieldgroup'] as $i => $fielddata) {
                if ($fielddata['conditionfield'] == 0) { // Don't need to bother if none is selected
                    continue;
                }
                if (in_array($fielddata['conditionfield'], $arrcurrentfields)) {
                    $errors["conditionfieldgroup[{$i}]"] = $stralreadydeclaredwarning->out();
                }
                // Add the field to the array
                $arrcurrentfields[] = $fielddata['conditionfield'];
            }
        }

        return $errors;
    }

    /**
     * Load in existing data as form defaults
     *
     * @param stdClass|array $default_values object or array of default values
     */
    function set_data($default_values) {
        if (!is_object($default_values)) {
            // we need object for file_prepare_standard_editor
            $default_values = (object)$default_values;
        }
        $editoroptions = $this->_customdata['editoroptions'];
        $default_values = file_prepare_standard_editor($default_values, 'summary', $editoroptions,
                $editoroptions['context'], 'course', 'section', $default_values->id);
        $default_values->usedefaultname = (is_null($default_values->name));
        parent::set_data($default_values);
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    function get_data() {
        $data = parent::get_data();
        if ($data !== null) {
            $editoroptions = $this->_customdata['editoroptions'];
            if (!empty($data->usedefaultname)) {
                $data->name = null;
            }
            $data = file_postupdate_standard_editor($data, 'summary', $editoroptions,
                    $editoroptions['context'], 'course', 'section', $data->id);
            $course = $this->_customdata['course'];
            foreach (course_get_format($course)->section_format_options() as $option => $unused) {
                // fix issue with unset checkboxes not being returned at all
                if (!isset($data->$option)) {
                    $data->$option = null;
                }
            }
        }
        return $data;
    }
}
