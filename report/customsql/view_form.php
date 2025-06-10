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
 * Form for viewing/entering parameters for a custom SQL report.
 *
 * @package report_customsql
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/locallib.php');

/**
 * Form for viewing a custom SQL report.
 *
 * @copyright © 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_customsql_view_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'heading', get_string('queryparameters', 'report_customsql'));

        foreach ($this->_customdata as $queryparam => $formparam) {
            $type = report_customsql_get_element_type($queryparam);
            $mform->addElement($type, $formparam, str_replace('_', ' ', $queryparam));
            if ($type == 'text') {
                $mform->setType($formparam, PARAM_RAW);
            }
        }

        $this->add_action_buttons(true, 'Run report');
    }
}
