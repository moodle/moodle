<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_assignment_mod_form extends moodleform_mod {
    protected $_assignmentinstance = null;

    function definition() {
        global $CFG, $DB, $PAGE, $COURSE;
        $mform =& $this->_form;

        // this hack is needed for different settings of each subtype
        if (!empty($this->_instance)) {
            if($ass = $DB->get_record('assignment', array('id'=>$this->_instance))) {
                $type = $ass->assignmenttype;
            } else {
                print_error('invalidassignment', 'assignment');
            }
        } else {
            $type = required_param('type', PARAM_ALPHA);
        }
        $mform->addElement('hidden', 'assignmenttype', $type);
        $mform->setType('assignmenttype', PARAM_ALPHA);
        $mform->setDefault('assignmenttype', $type);
        $mform->addElement('hidden', 'type', $type);
        $mform->setType('type', PARAM_ALPHA);
        $mform->setDefault('type', $type);

        $classfile = $CFG->dirroot.'/mod/assignment/type/'.$type.'/assignment.class.php';
        if (!file_exists($classfile)) {
            throw new moodle_exception('unsupportedsubplugin', 'assignment', new moodle_url('/course/view.php', array('id' => $COURSE->id)), $type);
        }
        require_once($classfile);
        $assignmentclass = 'assignment_'.$type;
        $assignmentinstance = new $assignmentclass();

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

//        $mform->addElement('static', 'statictype', get_string('assignmenttype', 'assignment'), get_string('type'.$type,'assignment'));

        $mform->addElement('text', 'name', get_string('assignmentname', 'assignment'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->add_intro_editor(true, get_string('description', 'assignment'));

        $mform->addElement('date_time_selector', 'timeavailable', get_string('availabledate', 'assignment'), array('optional'=>true));
        $mform->setDefault('timeavailable', time());
        $mform->addElement('date_time_selector', 'timedue', get_string('duedate', 'assignment'), array('optional'=>true));
        $mform->setDefault('timedue', time()+7*24*3600);

        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));

        if ($assignmentinstance->supports_lateness()) {
            $mform->addElement('select', 'preventlate', get_string('preventlate', 'assignment'), $ynoptions);
            $mform->setDefault('preventlate', 0);
        }

        // hack to support pluggable assignment type titles
        if (get_string_manager()->string_exists('type'.$type, 'assignment')) {
            $typetitle = get_string('type'.$type, 'assignment');
        } else {
            $typetitle  = get_string('type'.$type, 'assignment_'.$type);
        }

        $this->standard_grading_coursemodule_elements();

        $mform->addElement('header', 'typedesc', $typetitle);

        $assignmentinstance->setup_elements($mform);

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();

        // Add warning popup/noscript tag, if grades are changed by user.
        if ($mform->elementExists('grade') && !empty($this->_instance) && $DB->record_exists_select('assignment_submissions', 'assignment = ? AND grade <> -1', array($this->_instance))) {
            $module = array(
                'name' => 'mod_assignment',
                'fullpath' => '/mod/assignment/assignment.js',
                'requires' => array('node', 'event'),
                'strings' => array(array('changegradewarning', 'mod_assignment'))
                );
            $PAGE->requires->js_init_call('M.mod_assignment.init_grade_change', null, false, $module);

            // Add noscript tag in case
            $noscriptwarning = $mform->createElement('static', 'warning', null,  html_writer::tag('noscript', get_string('changegradewarning', 'mod_assignment')));
            $mform->insertElementBefore($noscriptwarning, 'grade');
        }
    }

    // Needed by plugin assignment types if they include a filemanager element in the settings form
    function has_instance() {
        return ($this->_instance != NULL);
    }

    // Needed by plugin assignment types if they include a filemanager element in the settings form
    function get_context() {
        return $this->context;
    }

    protected function get_assignment_instance() {
        global $CFG, $DB;

        if ($this->_assignmentinstance) {
            return $this->_assignmentinstance;
        }
        if (!empty($this->_instance)) {
            if($ass = $DB->get_record('assignment', array('id'=>$this->_instance))) {
                $type = $ass->assignmenttype;
            } else {
                print_error('invalidassignment', 'assignment');
            }
        } else {
            $type = required_param('type', PARAM_ALPHA);
        }
        require_once($CFG->dirroot.'/mod/assignment/type/'.$type.'/assignment.class.php');
        $assignmentclass = 'assignment_'.$type;
        $this->assignmentinstance = new $assignmentclass();
        return $this->assignmentinstance;
    }


    function data_preprocessing(&$default_values) {
        // Allow plugin assignment types to preprocess form data (needed if they include any filemanager elements)
        $this->get_assignment_instance()->form_data_preprocessing($default_values, $this);
    }


    function validation($data, $files) {
        // Allow plugin assignment types to do any extra validation after the form has been submitted
        $errors = parent::validation($data, $files);
        $errors = array_merge($errors, $this->get_assignment_instance()->form_validation($data, $files));
        return $errors;
    }
}

