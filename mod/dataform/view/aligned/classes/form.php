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
 * @package dataformview
 * @subpackage aligned
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformview_aligned_form extends mod_dataform\pluginbase\dataformviewform {

    /**
     *
     */
    protected function definition_view_specific() {
        // View template.
        $this->definition_view_template();

        // Entry template.
        $this->definition_entry_template();

        // Submission settings.
        $this->definition_view_submission();
    }

    /**
     *
     */
    protected function definition_entry_template() {
        $mform = &$this->_form;

        // Header.
        $mform->addElement('header', 'entrytemplatehdr', get_string('entrytemplate', 'dataform'));
        $mform->addHelpButton('entrytemplatehdr', 'entrytemplate', 'dataform');

        // Template editor (param2).
        $mform->addElement('textarea', 'param2', get_string('entrytemplate', 'dataform'), array('cols' => 40, 'rows' => 12));
        $mform->addHelpButton('param2', 'entrytemplate', 'dataformview_aligned');
        $this->add_patterns_selectors('param2', array('view', 'field'));
    }

}
