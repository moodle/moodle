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
 * Defines the editing form for the random question type.
 *
 * @package    qtype
 * @subpackage random
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * random editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_random_edit_form extends question_edit_form {
    /**
     * Build the form definition.
     *
     * This adds all the form files that the default question type supports.
     * If your question type does not support all these fields, then you can
     * override this method and remove the ones you don't want with $mform->removeElement().
     */
    protected function definition() {
        $mform = $this->_form;

        // Standard fields at the start of the form.
        $mform->addElement('header', 'generalheader', get_string("general", 'form'));

        $mform->addElement('questioncategory', 'category', get_string('category', 'question'),
                array('contexts' => $this->contexts->having_cap('moodle/question:useall'), 'top' => true));

        $mform->addElement('advcheckbox', 'questiontext[text]',
                get_string('includingsubcategories', 'qtype_random'), null, null, array(0, 1));

        $tops = question_get_top_categories_for_contexts(array_column($this->contexts->all(), 'id'));
        $mform->hideIf('questiontext[text]', 'category', 'in', $tops);

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_ALPHA);

        $this->add_hidden_fields();

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    public function set_data($question) {
        $question->questiontext = array('text' => $question->questiontext);
        // We don't want the complex stuff in the base class to run.
        moodleform::set_data($question);
    }

    public function validation($fromform, $files) {
        // Validation of category is not relevant for this question type.

        return array();
    }

    public function qtype() {
        return 'random';
    }
}
