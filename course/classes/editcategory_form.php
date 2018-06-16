<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit category form.
 *
 * @package core_course
 * @copyright 2002 onwards Martin Dougiamas (http://dougiamas.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

/**
 * Edit category form.
 *
 * @package core_course
 * @copyright 2002 onwards Martin Dougiamas (http://dougiamas.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_editcategory_form extends moodleform {

    /**
     * The form definition.
     */
    public function definition() {
        global $CFG, $DB;
        $mform = $this->_form;
        $categoryid = $this->_customdata['categoryid'];
        $parent = $this->_customdata['parent'];

        // Get list of categories to use as parents, with site as the first one.
        $options = array();
        if (has_capability('moodle/category:manage', context_system::instance()) || $parent == 0) {
            $options[0] = get_string('top');
        }
        if ($categoryid) {
            // Editing an existing category.
            $options += core_course_category::make_categories_list('moodle/category:manage', $categoryid);
            if (empty($options[$parent])) {
                // Ensure the the category parent has been included in the options.
                $options[$parent] = $DB->get_field('course_categories', 'name', array('id'=>$parent));
            }
            $strsubmit = get_string('savechanges');
        } else {
            // Making a new category.
            $options += core_course_category::make_categories_list('moodle/category:manage');
            $strsubmit = get_string('createcategory');
        }

        $mform->addElement('select', 'parent', get_string('parentcategory'), $options);

        $mform->addElement('text', 'name', get_string('categoryname'), array('size' => '30'));
        $mform->addRule('name', get_string('required'), 'required', null);
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'idnumber', get_string('idnumbercoursecategory'), 'maxlength="100" size="10"');
        $mform->addHelpButton('idnumber', 'idnumbercoursecategory');
        $mform->setType('idnumber', PARAM_RAW);

        $mform->addElement('editor', 'description_editor', get_string('description'), null,
            $this->get_description_editor_options());

        if (!empty($CFG->allowcategorythemes)) {
            $themes = array(''=>get_string('forceno'));
            $allthemes = get_list_of_themes();
            foreach ($allthemes as $key => $theme) {
                if (empty($theme->hidefromselector)) {
                    $themes[$key] = get_string('pluginname', 'theme_'.$theme->name);
                }
            }
            $mform->addElement('select', 'theme', get_string('forcetheme'), $themes);
        }

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $categoryid);

        $this->add_action_buttons(true, $strsubmit);
    }

    /**
     * Returns the description editor options.
     * @return array
     */
    public function get_description_editor_options() {
        global $CFG;
        $context = $this->_customdata['context'];
        $itemid = $this->_customdata['itemid'];
        return array(
            'maxfiles'  => EDITOR_UNLIMITED_FILES,
            'maxbytes'  => $CFG->maxbytes,
            'trusttext' => true,
            'context'   => $context,
            'subdirs'   => file_area_contains_subdirs($context, 'coursecat', 'description', $itemid),
        );
    }

    /**
     * Validates the data submit for this form.
     *
     * @param array $data An array of key,value data pairs.
     * @param array $files Any files that may have been submit as well.
     * @return array An array of errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!empty($data['idnumber'])) {
            if ($existing = $DB->get_record('course_categories', array('idnumber' => $data['idnumber']))) {
                if (!$data['id'] || $existing->id != $data['id']) {
                    $errors['idnumber'] = get_string('categoryidnumbertaken', 'error');
                }
            }
        }
        return $errors;
    }
}
