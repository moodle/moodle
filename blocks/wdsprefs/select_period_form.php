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
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");

class select_period_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Add the step = assign crap.
        $this->_form->addElement('hidden', 'step', 'period');
        $this->_form->setType('step', PARAM_TEXT);

        // Get the periods.
        $periods = $this->_customdata['periods'] ?? [];

        // Add the header.
        $mform->addElement('header',
            'wdsprefs:selectperiodsheader',
            get_string('wdsprefs:selectperiodsheader', 'block_wdsprefs'));

        // Build the radio array.
        $radioarray = [];
        // Set the class.
        $parms = ['class' => 'wdsprefsradio'];

        // Loop through the periods to build out the radio array.
        foreach ($periods as $periodid => $periodname) {
            $radioarray[] = $mform->createElement('radio', 'periodid', '', $periodname, $periodid, $parms);
        }

        $mform->addGroup($radioarray, 'periodid_group', get_string('wdsprefs:selectperiod', 'block_wdsprefs'), '<br>', false);
        $mform->addRule('periodid_group', null, 'required', null, 'client');
        $mform->setType('periodid', PARAM_ALPHANUMEXT);

        // Add the action buttons.
        $this->add_action_buttons(true, get_string('continue'));
    }
}
