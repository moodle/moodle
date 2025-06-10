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
 * Plugin setup form for plagiarism_turnitin component
 *
 * @package   plagiarism_turnitin
 * @copyright 2018 Turnitin
 * @author    David Winn <dwinn@turnitin.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class turnitin_defaultsettingsform extends moodleform {

    // Define the form.
    public function definition () {
        global $CFG;

        $mform = $this->_form;

        require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_view.class.php');

        $turnitinview = new turnitin_view();
        $turnitinview->add_elements_to_settings_form($mform, array(), "defaults");

        $this->add_action_buttons(true);
    }
}