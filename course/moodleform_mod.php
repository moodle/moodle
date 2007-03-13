<?php
require_once ($CFG->libdir.'/formslib.php');
/**
 * This class adds extra methods to form wrapper specific to be used for module
 * add / update forms (mod/{modname}.mod_form.php replaces deprecared mod/{modname}/mod.html
 *
 */
class moodleform_mod extends moodleform {
    /**
     * Instance of the module that is being updated. This is the id of the {prefix}{modulename}
     * record. Can be used in form definition. Will be "" if this is an 'add' form and not an
     * update one.
     *
     * @var mixed
     */
    var $_instance;
    /**
     * Section of course that module instance will be put in or is in.
     * This is always the section number itself.
     *
     * @var mixed
     */
    var $_section;
    /**
     * Coursemodle record of the module that is being updated. Will be null if this is an 'add' form and not an
     * update one.
      *
     * @var mixed
     */
    var $_cm;

    function moodleform_mod($instance, $section, $cm) {
        $this->_instance = $instance;
        $this->_section = $section;
        $this->_cm = $cm;
        parent::moodleform('modedit.php');
    }
    /**
     * Only available on moodleform_mod.
     *
     * @param array $default_values passed by reference
     */
    function data_preprocessing(&$default_values){
    }
    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param mixed $default_values object or array of default values
     */
    function set_data($default_values) {
        if (is_object($default_values)) {
            $default_values = (array)$default_values;
        }
        $this->data_preprocessing($default_values);
        parent::set_data($default_values + $this->standard_coursemodule_elements_settings());//never slashed for moodleform_mod
    }
    /**
     * Adds all the standard elements to a form to edit the settings for an activity module.
     *
     * @param bool $supportsgroups does this module support groups?
     */
    function standard_coursemodule_elements($supportsgroups=true){
        $mform =& $this->_form;
        $mform->addElement('header', 'modstandardelshdr', get_string('modstandardels', 'form'));
        if ($supportsgroups){
            $mform->addElement('modgroupmode', 'groupmode', get_string('groupmode'));
        }
        $mform->addElement('modvisible', 'visible', get_string('visible'));

        $this->standard_hidden_coursemodule_elements();
    }

    function standard_hidden_coursemodule_elements(){
        $mform =& $this->_form;
        $mform->addElement('hidden', 'course', 0);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'coursemodule', 0);
        $mform->setType('coursemodule', PARAM_INT);

        $mform->addElement('hidden', 'section', 0);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'module', 0);
        $mform->setType('module', PARAM_INT);

        $mform->addElement('hidden', 'modulename', '');
        $mform->setType('modulename', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'instance', 0);
        $mform->setType('instance', PARAM_INT);

        $mform->addElement('hidden', 'add', 0);
        $mform->setType('add', PARAM_ALPHA);

        $mform->addElement('hidden', 'update', 0);
        $mform->setType('update', PARAM_INT);

        $mform->addElement('hidden', 'return', 0);
        $mform->setType('return', PARAM_BOOL);
    }

    /**
     * This function is called by course/modedit.php to setup defaults for standard form
     * elements.
     *
     * @param object $course
     * @param object $cm
     * @param integer $section
     * @return unknown
     */
    function standard_coursemodule_elements_settings(){
        return ($this->modgroupmode_settings() + $this->modvisible_settings());
    }
    /**
     * This is called from modedit.php to load the default for the groupmode element.
     *
     * @param object $course
     * @param object $cm
     */
    function modgroupmode_settings(){
        global $COURSE;
        return array('groupmode'=>groupmode($COURSE, $this->_cm));
    }
    /**
     *  This is called from modedit.php to set the default for modvisible form element.
     *
     * @param object $course
     * @param object $cm
     * @param integer $section section is a db id when updating a activity config
     *                   or the section no when adding a new activity
     */
    function modvisible_settings(){
        global $COURSE;
        $cm=$this->_cm;
        $section=$this->_section;
        if ($cm) {
            $visible = $cm->visible;
        } else {
            $visible = 1;
        }

        $hiddensection = !get_field('course_sections', 'visible', 'section', $section, 'course', $COURSE->id);
        if ($hiddensection) {
            $visible = 0;
        }
        return array('visible'=>$visible);
    }

}

?>