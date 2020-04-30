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
 * Strings for component 'tool_checklearningrecords', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    tool
 * @subpackage checklearningrecords
 * @copyright  2020 E-Learn Design https://www.e-learndesign
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Site wide search-checklearningrecords form.
 */
class tool_checklearningrecords_form extends moodleform {

    function __construct($actionurl, $brokenlicenses, $brokencompletions, $missingcompletions) {
        $this->brokenlicenses = $brokenlicenses;
        $this->brokencompletions = $brokencompletions;
        $this->missingcompletions = $missingcompletions;

        parent::__construct($actionurl);
    }

    function definition() {
        $mform = $this->_form;

        $mform->addElement('header', '', get_string('pluginname', 'tool_checklearningrecords'));

        $mform->addElement('static', 'brokenlicenses', get_string('brokenlicenses', 'tool_checklearningrecords', $this->brokenlicenses));
        $mform->addElement('static', 'brokencompletions', get_string('brokencompletions', 'tool_checklearningrecords', $this->brokencompletions));
        $mform->addElement('static', 'missingcompletions', get_string('missingcompletions', 'tool_checklearningrecords', $this->missingcompletions));

        $this->add_action_buttons(false, get_string('doit', 'tool_checklearningrecords'));
    }
}
