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
 * This file contains the tool_form class for the assignfeedback_editpdfplus plugin
 *
 * Form to add and edit a tool
 *
 * @package    assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus\form;

require_once("$CFG->libdir/formslib.php");

use moodleform;

class tool_form extends moodleform {

    protected function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'colors', 'Couleur'); // Add elements to your form
        $mform->setType('label', PARAM_TEXT);            //Set type of element
        $mform->addElement('hidden', 'toolid', '');      // Add elements to your form
        $mform->setType('hidden', PARAM_INT);            //Set type of element
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

}
