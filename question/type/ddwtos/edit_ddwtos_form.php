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
 * Defines the editing form for the drag-and-drop words into sentences question type.
 *
 * @package qtype_ddwtos
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/type/gapselect/edit_form_base.php');


/**
 * Drag-and-drop words into sentences editing form definition.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddwtos_edit_form extends qtype_gapselect_edit_form_base {

    function qtype() {
        return 'ddwtos';
    }

    protected function default_values_from_feedback_field($feedback, $key){
        $feedback = unserialize($feedback);
        $draggroup = $feedback->draggroup;
        $infinite = $feedback->infinite;

        $default_values = array();
        $default_values['choices['.$key.'][draggroup]'] = $draggroup;
        $default_values['choices['.$key.'][infinite]'] = $infinite;
        return $default_values;
    }

    protected function repeated_options(){
        $repeatedoptions = array();
        $repeatedoptions['draggroup']['default'] = '1';
        $repeatedoptions['infinite']['default'] = 0;
        return $repeatedoptions;
    }

    protected function choice_group(&$mform, $grouparray){
        $options = array();
        for ($i = 1; $i <= 8; $i += 1) {
            $options[$i] = $i;
        }
        $grouparray[] =& $mform->createElement('select', 'draggroup', get_string('group', 'qtype_ddwtos'), $options);
        $grouparray[] =& $mform->createElement('checkbox', 'infinite', ' ', get_string('infinite', 'qtype_ddwtos'), null, array('size'=>1, 'class'=>'tweakcss'));
        return $grouparray;
    }
}
