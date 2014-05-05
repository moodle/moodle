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
            $mform->addElement('header', 'availabilityconditions',
                    get_string('restrictaccess', 'availability'));
            $mform->setExpanded('availabilityconditions', false);

            // Availability field. This is just a textarea; the user interface
            // interaction is all implemented in JavaScript. The field is named
            // availabilityconditionsjson for consistency with moodleform_mod.
            $mform->addElement('textarea', 'availabilityconditionsjson',
                    get_string('accessrestrictions', 'availability'));
            \core_availability\frontend::include_all_javascript($course, null,
                    $this->_customdata['cs']);
        }

        $this->add_action_buttons();
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

    public function validation($data, $files) {
        global $CFG;
        $errors = array();

        // Availability: Check availability field does not have errors.
        if (!empty($CFG->enableavailability)) {
            \core_availability\frontend::report_validation_errors($data, $errors);
        }

        return $errors;
    }
}
