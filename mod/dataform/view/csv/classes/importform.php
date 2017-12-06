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
 * This file is part of the Dataform module for Moodle - http://moodle.org/.
 *
 * @package dataformview
 * @subpackage csv
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/csvlib.class.php");

/**
 *
 */
class dataformview_csv_importform extends moodleform {
    protected $_view;

    public function __construct($view, $action = null, $customdata = null, $method = 'post', $target = '', $attributes = null, $editable = true) {
        $this->_view = $view;

        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }

    public function definition() {

        $view = $this->_view;
        $mform = &$this->_form;

        // Csv content.
        $this->definition_csv_content();

        // Import options.
        $this->definition_import_options();

        // Csv settings.
        $this->definition_csv_settings();

        // Field settings.
        $this->definition_field_settings();

        // Action buttons.
        $this->add_action_buttons();
    }

    /**
     *
     */
    public function add_action_buttons($cancel = true, $submit = null) {
        $mform = &$this->_form;

        $grp = array();
        // Test.
        $grp[] = &$mform->createElement('submit', 'submitbutton_test', get_string('test', 'dataformview_csv'));
        // Import.
        $grp[] = &$mform->createElement('submit', 'submitbutton', get_string('import'));
        // Cancel.
        $grp[] = &$mform->createElement('cancel', 'cancel');
        $mform->addGroup($grp, 'buttongrp', '', ' ', false);
        $mform->closeHeaderBefore('buttongrp');
    }

    /**
     *
     */
    protected function definition_field_settings() {
        $view = $this->_view;
        $df = $view->get_df();
        $mform = &$this->_form;

        $mform->addElement('header', 'fieldsettingshdr', get_string('importfields', 'dataformview_csv'));
        $columns = $view->get_columns();
        foreach ($columns as $column) {
            list($pattern, $header, ) = $column;
            $patternname = trim($pattern, '[]');
            $header = $header ? $header : $patternname;

            if (!$fieldid = $view->get_pattern_fieldid($pattern)) {
                continue;
            }

            if (!$field = $df->field_manager->get_field_by_id($fieldid)) {
                continue;
            }

            list($grp, $labels) = $field->renderer->get_pattern_import_settings($mform, $patternname, $header);
            if ($grp) {
                $mform->addGroup($grp, "grp$patternname", $header, $labels, false);
            }
        }
    }

    /**
     *
     */
    protected function definition_csv_settings() {
        $mform = &$this->_form;
        $view = $this->_view;

        $mform->addElement('header', 'csvsettingshdr', get_string('csvsettings', 'dataformview_csv'));

        // Delimiter.
        $delimiters = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter', get_string('csvdelimiter', 'dataform'), $delimiters);

        // Enclosure.
        $mform->addElement('text', 'enclosure', get_string('csvenclosure', 'dataform'), array('size' => '10'));
        $mform->setType('enclosure', PARAM_NOTAGS);

        // Encoding.
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'grades'), $choices);
    }

    /**
     *
     */
    protected function definition_csv_content() {
        $mform = &$this->_form;
        $view = $this->_view;

        // CSV content header.
        $mform->addElement('header', 'csvcontenthdr', get_string('csvcontent', 'dataformview_csv'));

        // Upload file.
        $mform->addElement('filepicker', 'importfile', get_string('uploadfile', 'dataformview_csv'));

        // Upload text.
        $mform->addElement('textarea', 'csvtext', get_string('uploadtext', 'dataformview_csv'), array('wrap' => 'virtual', 'rows' => '5', 'style' => 'width:100%;'));
    }

    /**
     *
     */
    protected function definition_import_options() {
        $mform = &$this->_form;
        $view = $this->_view;

        $mform->addElement('header', 'importoptionshdr', get_string('importoptions', 'dataformview_csv'));
        $mform->setExpanded('importoptionshdr');

        // Add per participant.
        $mform->addElement('selectyesno', 'addperparticipant', get_string('addperparticipant', 'dataformview_csv'));

        // Update existing entries.
        if ($view->param6) {
            $mform->addElement('selectyesno', 'updateexisting', get_string('updateexisting', 'dataformview_csv'));
        }

        // Edit after import.
        // $mform->addElement('selectyesno', 'editafter', get_string('importeditimported', 'dataformview_csv'));.
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        $view = $this->_view;
        // CSV settings.
        $csvsettings = $view->param1 ? $view->param1 : $view->get_default_csv_settings();
        list(
            $data->delimiter,
            $data->enclosure,
            $data->encoding
        ) = explode(',', $csvsettings);
    }

    /**
     *
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

}
