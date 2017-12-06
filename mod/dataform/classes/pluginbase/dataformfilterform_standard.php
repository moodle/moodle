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
 * @package mod_dataform
 * @category filter
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

defined('MOODLE_INTERNAL') or die;

/*
 *
 */
class dataformfilterform_standard extends dataformfilterform {

    /*
     *
     */
    public function definition() {

        $mform = &$this->_form;
        $filter = $this->_filter;
        $df = \mod_dataform_dataform::instance($filter->dataid);
        $fields = $df->field_manager->get_fields(array('exclude' => array(-1)));

        $mform->addElement('html', get_string('filterurlquery', 'dataform'). ': '. $this->get_url_query($fields));

        // Buttons.
        $this->add_action_buttons(true);

        // General definition.
        $this->definition_general();

        // Sort options.
        $this->custom_sort_definition($filter->customsort, $fields, true);

        // Search options.
        $this->custom_search_definition($filter->customsearch, $fields, true);

        // Buttons.
        $this->add_action_buttons(true);
    }

    /**
     *
     */
    public function add_action_buttons($cancel = true, $submit = null) {
        $mform = &$this->_form;

        $buttonarray = array();
        // Save changes.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        // Continue.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton_continue', get_string('continue'));
        // Cancel.
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
        $mform->closeHeaderBefore('buttonar');
    }

    /*
     *
     */
    public function validation($data, $files) {
        if (!$errors = parent::validation($data, $files)) {

            $filter = $this->_filter;
            $df = \mod_dataform_dataform::instance($filter->dataid);

            // Validate unique name.
            if (empty($data['name']) or $df->name_exists('filters', $data['name'], $filter->id)) {
                $errors['name'] = get_string('invalidname', 'dataform', get_string('filter', 'dataform'));
            }
        }

        return $errors;
    }
}
