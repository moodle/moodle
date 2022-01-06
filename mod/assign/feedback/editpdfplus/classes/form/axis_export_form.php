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
 * This file contains the axis_export_form class for the assignfeedback_editpdfplus plugin
 *
 * Form to export an axis
 *
 * @package    assignfeedback_editpdfplus
 * @copyright  2019 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus\form;

require_once("$CFG->libdir/formslib.php");

use moodleform;

/**
 * Form to export an axis
 *
 * @package   mod_audioannotation
 * @copyright  2019 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class axis_export_form extends moodleform {

    const HIDDENTYPE = "hidden";

    protected function definition() {

        $mform = $this->_form;
        $mform->_formName = "axis_export_form";

        $mform->addElement('text', 'label', get_string('adminaxisimport_name', 'assignfeedback_editpdfplus')); // Add elements to your form
        $mform->setType('label', PARAM_TEXT);       //Set type of element
        $mform->addRule('label', null, 'required', null, 'client');

        // Hidden params.
        $mform->addElement('hidden', 'contextid');
        $mform->setType('contextid', PARAM_INT);
        $mform->addElement('hidden', 'axeid');
        $mform->setType('axeid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'savetagexport');
        $mform->setType('action', PARAM_ALPHA);
    }

}
