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
 * @subpackage grid
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformview_grid_form extends mod_dataform\pluginbase\dataformviewform {

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

        $view = $this->_view;
        $editoroptions = $view->editoroptions;
        $editorattr = array('cols' => 40, 'rows' => 12);

        $mform = &$this->_form;

        // Header.
        $mform->addElement('header', 'entrytemplatehdr', get_string('entrytemplate', 'dataform'));
        $mform->addHelpButton('entrytemplatehdr', 'entrytemplate', 'dataform');

        // Template editor (param2).
        $mform->addElement('editor', 'param2_editor', get_string('entrytemplate', 'dataform'), $editorattr, $editoroptions);
        $this->add_patterns_selectors('param2_editor', array('view', 'field'));

        // Cols (param3).
        $range = range(2, 50);
        $options = array('' => get_string('choosedots')) + array_combine($range, $range);
        $mform->addElement('select', 'cols', get_string('cols', 'dataformview_grid'), $options);

        // Rows  (param3).
        $mform->addElement('selectyesno', 'rows', get_string('rows', 'dataformview_grid'));
        $mform->disabledIf('rows', 'cols', 'eq', '');

    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        parent::data_preprocessing($data);
        // Grid layout.
        if (!empty($data->param3)) {
            list(
                $data->cols,
                $data->rows,
            ) = explode(' ', $data->param3);
        }
    }

    /**
     *
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Grid layout.
            if (!empty($data->cols)) {
                $data->param3 = $data->cols. ' '. (int) !empty($data->rows);
            } else {
                $data->param3 = '';
            }
        }
        return $data;
    }

}
