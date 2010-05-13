<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->libdir.'/formslib.php');
/**
 * This class adds extra methods to form wrapper specific to be used for module
 * add / update forms (mod/{modname}.mod_form.php replaces deprecated mod/{modname}/mod.html
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
    /**
     * List of modform features
     */
    var $_features;

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
     * Each module which defines definition_after_data() must call this method using parent::definition_after_data();
     */
    function definition_after_data() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        if ($id = $mform->getElementValue('update')) {
            $modulename = $mform->getElementValue('modulename');
            $instance   = $mform->getElementValue('instance');

            if ($this->_features->gradecat) {
                $gradecat = false;
                if (!empty($CFG->enableoutcomes) and $this->_features->outcomes) {
                    if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
                        $gradecat = true;
                    }
                }
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
                    foreach ($items as $item) {
                        if (is_bool($gradecat)) {
                            $gradecat = $item->categoryid;
                            continue;
                        }
                        if ($gradecat != $item->categoryid) {
                            //mixed categories
                            $gradecat = false;
                            break;
                        }
                    }
                }

                if ($gradecat === false) {
                    // items and outcomes in different categories - remove the option
                    // TODO: it might be better to add a "Mixed categories" text instead
                    if ($mform->elementExists('gradecat')) {
                        $mform->removeElement('gradecat');
                    }
                }
            }
        }

        if ($COURSE->groupmodeforce) {
            if ($mform->elementExists('groupmode')) {
                $mform->hardFreeze('groupmode'); // groupmode can not be changed if forced from course settings
            }
        }

        if ($mform->elementExists('groupmode') and !$mform->elementExists('groupmembersonly') and empty($COURSE->groupmodeforce)) {
            $mform->disabledIf('groupingid', 'groupmode', 'eq', NOGROUPS);

        } else if (!$mform->elementExists('groupmode') and $mform->elementExists('groupmembersonly')) {
            $mform->disabledIf('groupingid', 'groupmembersonly', 'notchecked');

        } else if (!$mform->elementExists('groupmode') and !$mform->elementExists('groupmembersonly')) {
            // groupings have no use without groupmode or groupmembersonly
            if ($mform->elementExists('groupingid')) {
                $mform->removeElement('groupingid');
            }
        }
    }

    // form verification
    function validation($data, $files) {
        global $COURSE;
        $errors = parent::validation($data, $files);

        $mform =& $this->_form;

        $errors = array();

        if ($mform->elementExists('name')) {
            $name = trim($data['name']);
            if ($name == '') {
                $errors['name'] = get_string('required');
            }
        }

        $grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$data['modulename'],
                     'iteminstance'=>$data['instance'], 'itemnumber'=>0, 'courseid'=>$COURSE->id));
        if ($data['coursemodule']) {
            $cm = get_record('course_modules', 'id', $data['coursemodule']);
        } else {
            $cm = null;
        }

        if ($mform->elementExists('cmidnumber')) {
            // verify the idnumber
            if (!grade_verify_idnumber($data['cmidnumber'], $COURSE->id, $grade_item, $cm)) {
                $errors['cmidnumber'] = get_string('idnumbertaken');
            }
        }

        return $errors;
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
     * @param mixed array or object describing supported features - groups, groupings, groupmembersonly, etc.
     */
    function standard_coursemodule_elements($features=null){
        global $COURSE, $CFG;
        $mform =& $this->_form;

        // deal with legacy $supportgroups param
        if ($features === true or $features === false) {
            $groupmode = $features;
            $this->_features = new object();
            $this->_features->groups = $groupmode;

        } else if (is_array($features)) {
            $this->_features = (object)$features;

        } else if (empty($features)) {
            $this->_features = new object();

        } else {
            $this->_features = $features;
        }

        if (!isset($this->_features->groups)) {
            $this->_features->groups = true;
        }

        if (!isset($this->_features->groupings)) {
            $this->_features->groupings = false;
        }

        if (!isset($this->_features->groupmembersonly)) {
            $this->_features->groupmembersonly = false;
        }

        if (!isset($this->_features->outcomes)) {
            $this->_features->outcomes = true;
        }

        if (!isset($this->_features->gradecat)) {
            $this->_features->gradecat = true;
        }

        if (!isset($this->_features->idnumber)) {
            $this->_features->idnumber = true;
        }

        $outcomesused = false;
        if (!empty($CFG->enableoutcomes) and $this->_features->outcomes) {
            if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
                $outcomesused = true;
                $mform->addElement('header', 'modoutcomes', get_string('outcomes', 'grades'));
                foreach($outcomes as $outcome) {
                    $mform->addElement('advcheckbox', 'outcome_'.$outcome->id, $outcome->get_name());
                }
            }
        }

        $mform->addElement('header', 'modstandardelshdr', get_string('modstandardels', 'form'));
        if ($this->_features->groups) {
            $options = array(NOGROUPS       => get_string('groupsnone'),
                             SEPARATEGROUPS => get_string('groupsseparate'),
                             VISIBLEGROUPS  => get_string('groupsvisible'));
            $mform->addElement('select', 'groupmode', get_string('groupmode'), $options, NOGROUPS);
            $mform->setHelpButton('groupmode', array('groupmode', get_string('groupmode')));
        }

        if (!empty($CFG->enablegroupings)) {
            if ($this->_features->groupings or $this->_features->groupmembersonly) {
                //groupings selector - used for normal grouping mode or also when restricting access with groupmembersonly
                $options = array();
                $options[0] = get_string('none');
                if ($groupings = get_records('groupings', 'courseid', $COURSE->id)) {
                    foreach ($groupings as $grouping) {
                        $options[$grouping->id] = format_string($grouping->name);
                    }
                }
                $mform->addElement('select', 'groupingid', get_string('grouping', 'group'), $options);
                $mform->setHelpButton('groupingid', array('grouping', get_string('grouping', 'group')));
                $mform->setAdvanced('groupingid');
            }

            if ($this->_features->groupmembersonly) {
                $mform->addElement('checkbox', 'groupmembersonly', get_string('groupmembersonly', 'group'));
                $mform->setHelpButton('groupmembersonly', array('groupmembersonly', get_string('groupmembersonly', 'group')));
                $mform->setAdvanced('groupmembersonly');
            }
        }

        $mform->addElement('modvisible', 'visible', get_string('visible'));

        if ($this->_features->idnumber) {
            $mform->addElement('text', 'cmidnumber', get_string('idnumbermod'));
            $mform->setHelpButton('cmidnumber', array('cmidnumber', get_string('idnumbermod')), true);
        }

        if ($this->_features->gradecat) {
            $categories = grade_get_categories_menu($COURSE->id, $outcomesused);
            $mform->addElement('select', 'gradecat', get_string('gradecategory', 'grades'), $categories);
        }

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
     * Overriding formslib's add_action_buttons() method, to add an extra submit "save changes and return" button.
     *
     * @param bool $cancel show cancel button
     * @param string $submitlabel null means default, false means none, string is label text
     * @param string $submit2label  null means default, false means none, string is label text
     * @return void
     */
    function add_action_buttons($cancel=true, $submitlabel=null, $submit2label=null) {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('savechangesanddisplay');
        }

        if (is_null($submit2label)) {
            $submit2label = get_string('savechangesandreturntocourse');
        }

        $mform =& $this->_form;

        // elements in a row need a group
        $buttonarray = array();

        if ($submit2label !== false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', $submit2label);
        }

        if ($submitlabel !== false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        }

        if ($cancel) {
            $buttonarray[] = &$mform->createElement('cancel');
        }

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');
    }
}

?>
