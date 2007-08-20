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
     * This is always the section number itself (column 'section' from 'course_sections' table).
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

    function definition_after_data() {
        global $COURSE;
        $mform =& $this->_form;

        if ($id = $mform->getElementValue('update')) {
            $modulename = $mform->getElementValue('modulename');
            $instance   = $mform->getElementValue('instance');

            if ($items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename,
                                               'iteminstance'=>$instance, 'courseid'=>$COURSE->id))) {
                foreach ($items as $item) {
                    if (!empty($item->outcomeid)) {
                        $elname = 'outcome_'.$item->outcomeid;
                        if ($mform->elementExists($elname)) {
                            $mform->hardFreeze($elname); // prevent removing of existing outcomes
                        }
                    }
                }
            }
        }

        if ($mform->elementExists('groupmode')) {
            if ($COURSE->groupmodeforce) {
                $mform->hardFreeze('groupmode'); // groupmode can not be changed if forced from course settings
            }
        }

        // groupings have no use without groupmode or groupmembersonly
        if (!$mform->elementExists('groupmode') and !$mform->elementExists('groupmembersonly')) {
            if ($mform->elementExists('groupingid')) {
                $mform->removeElement('groupingid');
            }
        }
    }

    // form verification
    function validation($data) {
        global $COURSE;

        $errors = array();

        $name = trim($data['name']);
        if ($name == '') {
            $errors['name'] = get_string('required');
        }

        $grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$data['modulename'],
                     'iteminstance'=>$data['instance'], 'itemnumber'=>0, 'courseid'=>$COURSE->id));
        if ($data['coursemodule']) {
            $cm = get_record('course_modules', 'id', $data['coursemodule']);
        } else {
            $cm = null;
        }

        // verify the idnumber
        if (!grade_verify_idnumber($data['cmidnumber'], $grade_item, $cm)) {
            $errors['cmidnumber'] = get_string('idnumbertaken');
        }

        if (count($errors) == 0) {
            return true;
        } else {
            return $errors;
        }
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
        parent::set_data($default_values); //never slashed for moodleform_mod
    }

    /**
     * Adds all the standard elements to a form to edit the settings for an activity module.
     *
     * @param mixed array or object describing supported features - groups, groupings, groupmembersonly
     */
    function standard_coursemodule_elements($features=null){
        global $COURSE, $CFG;
        $mform =& $this->_form;

        // deal with legacy $supportgroups param
        if ($features === true or $features === false) {
            $groupmode = $features;
            $features = new object();
            $features->groups = $groupmode;

        } else if (is_array($features)) {
            $features = (object)$features;

        } else if (empty($features)) {
            $features = new object();
        }

        if (!isset($features->groups)) {
            $features->groups = true;
        }

        if (!isset($features->groupings)) {
            $features->groupings = false;
        }

        if (!isset($features->groupmembersonly)) {
            $features->groupmembersonly = false;
        }

        if (!empty($CFG->enableoutcomes)) {
            if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
                $mform->addElement('header', 'modoutcomes', get_string('outcomes', 'grades'));
                foreach($outcomes as $outcome) {
                    $mform->addElement('advcheckbox', 'outcome_'.$outcome->id, $outcome->get_name());
                }
            }
        }

        $mform->addElement('header', 'modstandardelshdr', get_string('modstandardels', 'form'));
        if ($features->groups){
            $mform->addElement('modgroupmode', 'groupmode', get_string('groupmode'));
        }

        if (!empty($CFG->enablegroupings)) {
            if ($features->groupings or $features->groupmembersonly) {
                //groupings selector - used for normal grouping mode or also when restricting access with groupmembersonly
                $options = array();
                $options[0] = get_string('none');
                if ($groupings = get_records('groupings', 'courseid', $COURSE->id)) {
                    foreach ($groupings as $grouping) {
                        $options[$grouping->id] = format_string($grouping->name);
                    }
                }
                $mform->addElement('select', 'groupingid', get_string('grouping', 'group'), $options);
                $mform->setAdvanced('groupingid');
            }

            if ($features->groupmembersonly) {
                $mform->addElement('advcheckbox', 'groupmembersonly', get_string('groupmembersonly', 'group'));
                $mform->setAdvanced('groupmembersonly');
            }
        }

        $mform->addElement('modvisible', 'visible', get_string('visible'));
        $mform->addElement('text', 'cmidnumber', get_string('idnumber'));

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

}

?>
