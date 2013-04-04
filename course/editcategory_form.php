<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->libdir.'/coursecatlib.php');
class editcategory_form extends moodleform {

    // form definition
    function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;
        $category = $this->_customdata['category'];
        $editoroptions = $this->_customdata['editoroptions'];

        // get list of categories to use as parents, with site as the first one
        $options = array();
        if (has_capability('moodle/category:manage', get_system_context()) || $category->parent == 0) {
            $options[0] = get_string('top');
        }
        if ($category->id) {
            // Editing an existing category.
            $options += coursecat::make_categories_list('moodle/category:manage', $category->id);
            if (empty($options[$category->parent])) {
                $options[$category->parent] = $DB->get_field('course_categories', 'name', array('id'=>$category->parent));
            }
            $strsubmit = get_string('savechanges');
        } else {
            // Making a new category
            $options += coursecat::make_categories_list('moodle/category:manage');
            $strsubmit = get_string('createcategory');
        }

        $mform->addElement('select', 'parent', get_string('parentcategory'), $options);
        $mform->addElement('text', 'name', get_string('categoryname'), array('size'=>'30'));
        $mform->addRule('name', get_string('required'), 'required', null);
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('text', 'idnumber', get_string('idnumbercoursecategory'),'maxlength="100"  size="10"');
        $mform->addHelpButton('idnumber', 'idnumbercoursecategory');
        $mform->setType('idnumber', PARAM_RAW);
        $mform->addElement('editor', 'description_editor', get_string('description'), null, $editoroptions);
        $mform->setType('description_editor', PARAM_RAW);
        if (!empty($CFG->allowcategorythemes)) {
            $themes = array(''=>get_string('forceno'));
            $allthemes = get_list_of_themes();
            foreach ($allthemes as $key=>$theme) {
                if (empty($theme->hidefromselector)) {
                    $themes[$key] = get_string('pluginname', 'theme_'.$theme->name);
                }
            }
            $mform->addElement('select', 'theme', get_string('forcetheme'), $themes);
        }

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $category->id);

        $this->add_action_buttons(true, $strsubmit);
    }

    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!empty($data['idnumber'])) {
            if ($existing = $DB->get_record('course_categories', array('idnumber' => $data['idnumber']))) {
                if (!$data['id'] || $existing->id != $data['id']) {
                    $errors['idnumber']= get_string('idnumbertaken');
                }
            }
        }

        return $errors;
    }
}

