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

namespace qbank_managecategories\form;

use moodleform;
use qbank_managecategories\helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');


/**
 * Defines the form for editing question categories.
 *
 * Form for editing questions categories (name, description, etc.)
 *
 * @package    qbank_managecategories
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_edit_form extends moodleform {

    /**
     * Build the form definition.
     *
     * This adds all the form fields that the manage categories feature needs.
     * @throws \coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        $contexts = $this->_customdata['contexts'];
        $currentcat = $this->_customdata['currentcat'];

        $mform->addElement('header', 'categoryheader', get_string('addcategory', 'question'));

        $mform->addElement('questioncategory', 'parent', get_string('parentcategory', 'question'),
                ['contexts' => $contexts, 'top' => true, 'currentcat' => $currentcat, 'nochildrenof' => $currentcat]);
        $mform->setType('parent', PARAM_SEQUENCE);
        if (helper::question_is_only_child_of_top_category_in_context($currentcat)) {
            $mform->hardFreeze('parent');
        }
        $mform->addHelpButton('parent', 'parentcategory', 'question');

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="254" size="50"');
        $mform->setDefault('name', '');
        $mform->addRule('name', get_string('categorynamecantbeblank', 'question'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('editor', 'info', get_string('categoryinfo', 'question'),
                ['rows' => 10], ['noclean' => 1]);
        $mform->setDefault('info', '');
        $mform->setType('info', PARAM_RAW);

        $mform->addElement('text', 'idnumber', get_string('idnumber', 'question'), 'maxlength="100"  size="10"');
        $mform->addHelpButton('idnumber', 'idnumber', 'question');
        $mform->setType('idnumber', PARAM_RAW);

        $this->add_action_buttons(true, get_string('addcategory', 'question'));

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
    }

    /**
     * Set data method.
     *
     * Add additional information to current data.
     * @param \stdClass|array $current Object or array of default current data.
     */
    public function set_data($current) {
        if (is_object($current)) {
            $current = (array) $current;
        }
        if (!empty($current['info'])) {
            $current['info'] = ['text' => $current['info'], 'infoformat' => $current['infoformat']];
        } else {
            $current['info'] = ['text' => '', 'infoformat' => FORMAT_HTML];
        }
        parent::set_data($current);
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     * @throws \dml_exception|\coding_exception
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Add field validation check for duplicate idnumber.
        list($parentid, $contextid) = explode(',', $data['parent']);
        if (((string) $data['idnumber'] !== '') && !empty($contextid)) {
            $conditions = 'contextid = ? AND idnumber = ?';
            $params = [$contextid, $data['idnumber']];
            if (!empty($data['id'])) {
                $conditions .= ' AND id <> ?';
                $params[] = $data['id'];
            }
            if ($DB->record_exists_select('question_categories', $conditions, $params)) {
                $errors['idnumber'] = get_string('idnumbertaken', 'error');
            }
        }

        return $errors;
    }
}
