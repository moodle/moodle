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
 * Defines the form that limits student's access to attempt a quiz.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');


/**
 * A form that limits student's access to attempt a quiz.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_preflight_check_form extends moodleform {

    protected function definition() {
        $mform = $this->_form;

        foreach ($this->_customdata['hidden'] as $name => $value) {
            if ($name === 'sesskey') {
                continue;
            }
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_INT);
        }

        foreach ($this->_customdata['rules'] as $rule) {
            if ($rule->is_preflight_check_required($this->_customdata['attemptid'])) {
                $rule->add_preflight_check_form_fields($this, $mform,
                        $this->_customdata['attemptid']);
            }
        }

        $this->add_action_buttons(true, get_string('continue'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        foreach ($this->_customdata['rules'] as $rule) {
            if ($rule->is_preflight_check_required($this->_customdata['attemptid'])) {
                $errors = $rule->validate_preflight_check($data, $files, $errors,
                        $this->_customdata['attemptid']);
            }
        }

        return $errors;
    }
}
