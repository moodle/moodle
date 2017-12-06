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
 * @package dataformfield
 * @subpackage ratingmdl
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_ratingmdl_form extends mod_dataform\pluginbase\dataformfieldform {

    /**
     *
     */
    protected function field_definition() {

        $mform =& $this->_form;

        // Restrict name to alphanumeric.
        $mform->addRule('name', null, 'alphanumeric', null, 'client');

        // Scale.
        $mform->addElement('modgrade', 'param1', get_string('rating', 'dataformfield_ratingmdl'));
        $mform->setDefault('param1', 0);

        // None zero rating.
        // By default point scales include a 0 value for rating. If set to 'Yes' the 0 value will
        // not be available for rating.
        $mform->addElement('selectyesno', 'param6', get_string('preventzero', 'dataformfield_ratingmdl'));
        $mform->addHelpButton('param6', 'preventzero', 'dataformfield_ratingmdl');

        // Rate label.
        $mform->addElement('text', 'param2', get_string('ratelabel', 'dataformfield_ratingmdl'));
        $mform->setType('param2', PARAM_TEXT);
        $mform->addHelpButton('param2', 'ratelabel', 'dataformfield_ratingmdl');

        // Repetition limit.
        // That is, the max number of time the users in scope can use a certain
        // value for rating an entry. (Each user is still restricted to 1 rating
        // per field).
        $options = array(0 => get_string('unlimited'));
        $range = range(1, 20);
        $options = $options + array_combine($range, $range);
        $mform->addElement('select', 'param3', get_string('repititionlimit', 'dataformfield_ratingmdl'), $options);
        $mform->addHelpButton('param3', 'repititionlimit', 'dataformfield_ratingmdl');

        // Repetition scope.
        // That is, whether the repitition limit
        // applies to each user separately or to all users as a whole.
        $options = array(
            0 => get_string('eachuser', 'dataformfield_ratingmdl'),
            1 => get_string('allusers', 'dataformfield_ratingmdl'),
        );
        $mform->addElement('select', 'param4', get_string('repititionscope', 'dataformfield_ratingmdl'), $options);
        $mform->addHelpButton('param4', 'repititionscope', 'dataformfield_ratingmdl');

        // Force in order.
        // That is, the user cannot use a particular value for rating before
        // preceding values in the scale have been used the "limit" number of
        // times (if applicable).
        $mform->addElement('selectyesno', 'param5', get_string('forceinorder', 'dataformfield_ratingmdl'));
        $mform->addHelpButton('param5', 'forceinorder', 'dataformfield_ratingmdl');
    }

    /**
     * The field default content fieldset.
     * Overriding parent to display no defaults for this field.
     *
     * @return void
     */
    protected function definition_defaults() {
    }

    /**
     *
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Name must be lower case.
        if (strtolower($data['name']) != $data['name']) {
            $errors['name'] = get_string('err_lowername', 'dataform');
        }

        return $errors;
    }
}
