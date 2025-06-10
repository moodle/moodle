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
 * Adaptivequiz required password form
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

use moodleform;
use html_writer;

class requiredpassword extends moodleform {
    /** @var string $passwordmessage a string containing text for a failed password attempt */
    public $passwordmessage = '';
    /**
     * This method is refactored code from the quiz's password rule add_preflight_check_form_fields() method.
     * It prints a form for the user to enter a password
     */
    protected function definition() {
        $mform = $this->_form;

        foreach ($this->_customdata['hidden'] as $name => $value) {
            if ($name === 'sesskey') {
                continue;
            }
            if ($name === 'cmid' || $name === 'uniqueid') {
                $mform->setType($name, PARAM_INT);
            }
            $mform->addElement('hidden', $name, $value);
        }

        $mform->addElement('header', 'passwordheader', get_string('password'));
        $mform->addElement('static', 'passwordmessage', '', get_string('requirepasswordmessage', 'adaptivequiz'));

        $attr = array('style' => 'color:red;', 'class' => 'wrongpassword');
        $html = html_writer::start_tag('div', $attr);
        $mform->addElement('html', $html);
        $mform->addElement('static', 'message');
        $html = html_writer::end_tag('div');
        $mform->addElement('html', $html);

        // Don't use the 'proper' field name of 'password' since that get's
        // Firefox's password auto-complete over-excited.
        $mform->addElement('password', 'quizpassword', get_string('enterrequiredpassword', 'adaptivequiz'));

        $this->add_action_buttons(true, get_string('continue'));
    }
}
