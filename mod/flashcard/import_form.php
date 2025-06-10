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
 * A form to import textual cards from a csv file
 *
 * @package mod_flashcard
 * @category mod
 * @author Valery Fremaux (valery.fremaux@gmail.com) http://www.mylearningfactory.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class flashcard_import_form extends moodleform {

    public function definition() {

        $mform =& $this->_form;

        // Course module id.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_TEXT);

        // Current active view.
        $mform->addElement('hidden', 'view');
        $mform->setType('view', PARAM_TEXT);

        // MVC Action keyword.
        $mform->addElement('hidden', 'what');
        $mform->setType('what', PARAM_TEXT);

        $mform->addElement('header', 'cardimport', '');

        $fieldsepoptions[0] = ',';
        $fieldsepoptions[1] = ':';
        $fieldsepoptions[2] = ';';
        $mform->addElement('select', 'fieldsep', get_string('fieldsep', 'flashcard'), $fieldsepoptions);

        $mform->addElement('textarea', 'import', get_string('imported', 'flashcard'), array('rows' => 20, 'cols' => 60));
        $mform->setType('import', PARAM_TEXT);

        $mform->addElement('checkbox', 'confirm', get_string('confirm', 'flashcard'), get_string('importadvice', 'flashcard'));

        $this->add_action_buttons(true, get_string('import', 'flashcard'));
    }
}
