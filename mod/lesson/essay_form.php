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
 * Essay grading form
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/**
 * Include formslib if it has not already been included
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Essay grading form
 *
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class essay_grading_form extends moodleform {

    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'formheader', get_string('question', 'lesson'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'attemptid');
        $mform->setType('attemptid', PARAM_INT);

        $mform->addElement('hidden', 'mode', 'update');
        $mform->setType('mode', PARAM_ALPHA);

        $mform->addElement('static', 'question', get_string('question', 'lesson'));
        $mform->addElement('static', 'studentanswer', get_string('studentresponse', 'lesson', fullname($this->_customdata['user'], true)));

        $mform->addElement('textarea', 'response', get_string('comments', 'lesson'), array('rows'=>'15', 'cols'=>'60'));
        $mform->setType('response', PARAM_TEXT);

        $mform->addElement('select', 'score', get_string('essayscore', 'lesson'), $this->_customdata['scoreoptions']);
        $mform->setType('score', PARAM_INT);

        $this->add_action_buttons(get_string('cancel'), get_string('savechanges'));

    }
}
