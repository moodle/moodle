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
class dataformfilterform_advanced extends dataformfilterform {
    /*
     *
     */
    public function definition() {

        $mform = &$this->_form;
        $filter = $this->_filter;
        $view = $this->_customdata['view'];
        $fields = $view->get_fields(array('exclude' => array(-1)));

        // General definition.
        $this->definition_general();
        $mform->disabledIf('visible', 'name', 'neq', '');
        $mform->disabledIf('visible', 'name', 'eq', '');

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
        // Save as new.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton_new', get_string('savenewbutton', 'dataform'));
        // Continue.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton_continue', get_string('continue'));
        // Cancel.
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
        $mform->closeHeaderBefore('buttonar');
    }

}
