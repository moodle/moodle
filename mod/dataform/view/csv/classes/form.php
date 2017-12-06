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
 * @subpackage csv
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/csvlib.class.php");

class dataformview_csv_form extends dataformview_aligned_form {

    /**
     *
     */
    protected function definition_view_specific() {
        // View template.
        $this->definition_view_template();

        // Entry template.
        $this->definition_entry_template();

        // Entry template.
        $this->definition_expimp_settings();

        // Submission.
        $this->definition_view_submission();
    }

    /**
     *
     */
    protected function definition_expimp_settings() {

        $mform =& $this->_form;

        $mform->addElement('header', 'expimpsettingshdr', get_string('expimpsettings', 'dataformview_csv'));

        // Enable import  (param4).
        $mform->addElement('advcheckbox', 'param4',  get_string('export', 'grades'), get_string('enable'), null, array(0, 1));

        // Enable import  (param5).
        $mform->addElement('advcheckbox', 'param5',  get_string('import'), get_string('enable'), null, array(0, 1));

        // Allow update existing entries  (param4)
        $mform->addElement('advcheckbox', 'param6',  null, get_string('allowupdateexisting', 'dataformview_csv'), null, array(0, 1));
        $mform->disabledIf('param6', 'importenable', 'eq', 0);

        // Delimiter.
        $delimiters = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter', get_string('csvdelimiter', 'dataform'), $delimiters);
        $mform->setDefault('delimiter', 'comma');

        // Enclosure.
        $mform->addElement('text', 'enclosure', get_string('csvenclosure', 'dataform'), array('size' => '10'));
        $mform->setType('enclosure', PARAM_NOTAGS);
        $mform->setDefault('enclosure', '');

        // Encoding.
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'grades'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        parent::data_preprocessing($data);
        // CSV settings.
        $csvsettings = !empty($data->param1) ? $data->param1 : $this->_view->get_default_csv_settings();
        list(
            $data->delimiter,
            $data->enclosure,
            $data->encoding
        ) = explode(',', $csvsettings);

        // Enable Import and Export when adding a new instance.
        if (empty($data->id)) {
            $data->param4 = 1;
            $data->param5 = 1;
        }

        // BC for exporttype stored in param3.
        if (!empty($data->param3)) {
            $data->exportto = $data->param3;
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
            // CSV settings.
            $defaultsettings = $this->_view->get_default_csv_settings();
            $currentsettings = "$data->delimiter,$data->enclosure,$data->encoding";
            if ($defaultsettings != $currentsettings) {
                $data->param1 = $currentsettings;
            } else {
                $data->param1 = '';
            }
        }
        return $data;
    }
}
