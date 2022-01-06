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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Description of reportform
 *
 * @author Admin
 */
class pdfannotator_reportform extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!
        // Pass contextual parameters to the form (via set_data() in controller.php)
        // Course module id.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // Course id.
        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);
        // Pdf id.
        $mform->addElement('hidden', 'pdfannotatorid');
        $mform->setType('pdfannotatorid', PARAM_INT);
        // Pdfname.
        $mform->addElement('hidden', 'pdfname');
        $mform->setType('pdfname', PARAM_TEXT);
        // Comment id.
        $mform->addElement('hidden', 'commentid');
        $mform->setType('commentid', PARAM_INT);
        // Action = 'report'.
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);

        // Add a headline.
        $mform->addElement('header', 'bookwithmessagetitle', get_string('subtitleforreportcommentform', 'pdfannotator'));

        // Add a textarea for explaining the reason of complaint.
        $mform->addElement('textarea', 'introduction', get_string('reason', 'pdfannotator'), 'wrap="virtual" rows="5'
                . '" cols="109"');

        // Add submit and cancel buttons.
        $this->add_action_buttons($cancel = true, get_string('reportsendbutton', 'pdfannotator'));
    }

    public function display() {
        $this->_form->display();
    }

}
