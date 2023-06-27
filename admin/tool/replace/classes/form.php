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
 * Site wide search-replace form.
 *
 * @package    tool_replace
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Site wide search-replace form.
 */
class tool_replace_form extends moodleform {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $mform->addElement('header', 'searchhdr', get_string('pluginname', 'tool_replace'));
        $mform->setExpanded('searchhdr', true);

        $mform->addElement('text', 'search', get_string('searchwholedb', 'tool_replace'), 'size="50"');
        $mform->setType('search', PARAM_RAW);
        $mform->addElement('static', 'searchst', '', get_string('searchwholedbhelp', 'tool_replace'));
        $mform->addRule('search', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'replace', get_string('replacewith', 'tool_replace'), 'size="50"', PARAM_RAW);
        $mform->addElement('static', 'replacest', '', get_string('replacewithhelp', 'tool_replace'));
        $mform->setType('replace', PARAM_RAW);

        $mform->addElement('textarea', 'additionalskiptables', get_string("additionalskiptables", "tool_replace"),
            array('rows' => 5, 'cols' => 50));
        $mform->addElement('static', 'additionalskiptables_desc', '', get_string('additionalskiptables_desc', 'tool_replace'));
        $mform->setType('additionalskiptables', PARAM_RAW);
        $mform->setDefault('additionalskiptables', '');

        $mform->addElement('checkbox', 'shorten', get_string('shortenoversized', 'tool_replace'));
        $mform->addRule('replace', get_string('required'), 'required', null, 'client');

        $mform->addElement('header', 'confirmhdr', get_string('confirm'));
        $mform->setExpanded('confirmhdr', true);
        $mform->addElement('checkbox', 'sure', get_string('disclaimer', 'tool_replace'));
        $mform->addRule('sure', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons(false, get_string('doit', 'tool_replace'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['shorten']) and core_text::strlen($data['search']) < core_text::strlen($data['replace'])) {
            $errors['shorten'] = get_string('required');
        }

        return $errors;
    }
}
