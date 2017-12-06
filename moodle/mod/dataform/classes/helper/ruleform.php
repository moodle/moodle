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
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Dataform rule form helper
 */
class ruleform {

    /**
     *
     */
    public static function general_definition($mform, $dataformid, $prefix = null) {
        global $CFG;

        $paramtext = (!empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEAN);

        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Name.
        $mform->addElement('text', $prefix. 'name', get_string('name'), array('size' => '32'));
        $mform->addRule($prefix. 'name', null, 'required', null, 'client');
        $mform->setType($prefix. 'name', $paramtext);

        // Description.
        $mform->addElement('text', $prefix. 'description', get_string('description'), array('size' => '64'));
        $mform->setType($prefix. 'description', PARAM_CLEAN);

        // Enabled.
        $mform->addElement('selectyesno', $prefix. 'enabled', get_string('ruleenabled', 'dataform'));
        $mform->setDefault($prefix. 'enabled', 1);

        // Time from.
        $mform->addElement('date_time_selector', $prefix. 'timefrom', get_string('from'), array('optional' => true));

        // Time to.
        $mform->addElement('date_time_selector', $prefix. 'timeto', get_string('to'), array('optional' => true));

        // Views.
        $options = array(0 => get_string('all'), 1 => get_string('selected', 'form'));
        $mform->addElement('select', $prefix. 'viewselection', get_string('views', 'dataform'), $options);

        $items = array();
        if ($items = \mod_dataform_view_manager::instance($dataformid)->views_menu) {
            $items = array_combine($items, $items);
        }
        $select = &$mform->addElement('select', $prefix. 'views', null, $items);
        $select->setMultiple(true);
        $mform->disabledIf($prefix. 'views', $prefix. 'viewselection', 'eq', 0);
    }

    /**
     *
     */
    public static function fields_selection_definition($mform, $dataformid, $prefix = null) {
        $options = array(0 => get_string('all'), 1 => get_string('selected', 'form'));
        $mform->addElement('select', $prefix. 'fieldselection', get_string('fields', 'dataform'), $options);

        $items = array();
        if ($items = \mod_dataform_field_manager::instance($dataformid)->fields_menu) {
            $items = array_combine($items, $items);
        }
        $select = &$mform->addElement('select', $prefix. 'fields', null, $items);
        $select->setMultiple(true);
        $mform->disabledIf($prefix. 'fields', $prefix. 'fieldselection', 'eq', 0);
    }

    /**
     *
     */
    public static function general_validation($data, $files, $prefix = null) {
        $errors = array();

        // Time from and time to.
        if (!empty($data[$prefix. 'timefrom']) and !empty($data[$prefix. 'timeto']) and $data[$prefix. 'timeto'] <= $data[$prefix. 'timefrom']) {
            $errors[$prefix. 'timeto'] = get_string('errorinvalidtimeto', 'dataform');
        }

        return $errors;
    }

}
