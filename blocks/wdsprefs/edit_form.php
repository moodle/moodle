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

class wdsprefs_cps_edit_form extends moodleform {

    /// The standard Moodle form stuff.
    public function definition() {
        $mform = $this->_form;

        // Add the days prior item.
        $mform->addElement('text',
            'wdspref_createprior',
            get_string('wdsprefs:cdaysprior', 'block_wdsprefs')
        );
        $mform->setType('wdspref_createprior', PARAM_INT);
        $mform->addRule('wdspref_createprior', null, 'required', null, 'client');

        // Add the create days prior description.
        $mform->addElement('static',
            'wdspref_cdaysprior_desc',
            '',
            get_string('wdsprefs:cdaysprior_desc', 'block_wdsprefs')
        );

        // Add the enroll days prior item.
        $mform->addElement('text',
            'wdspref_enrollprior',
            get_string('wdsprefs:edaysprior', 'block_wdsprefs')
        );
        $mform->setType('wdspref_enrollprior', PARAM_INT);
        $mform->addRule('wdspref_enrollprior', null, 'required', null, 'client');


        // Add the days prior description.
        $mform->addElement('static',
            'wdspref_edaysprior_desc',
            '',
            get_string('wdsprefs:edaysprior_desc', 'block_wdsprefs')
        );

        // Add the enroll days prior item.
        $mform->addElement('text',
            'wdspref_courselimit',
            get_string('wdsprefs:courselimit', 'block_wdsprefs')
        );
        $mform->setType('wdspref_courselimit', PARAM_INT);
        $mform->addRule('wdspref_courselimit', null, 'required', null, 'client');


        // Add the days prior description.
        $mform->addElement('static',
            'wdspref_courselimit_desc',
            '',
            get_string('wdsprefs:courselimit_desc', 'block_wdsprefs')
        );

        // Add the action buttons.
        $this->add_action_buttons(true);
    }

    // Set the data from the preferences.
    public function set_data_from_preferences($userid) {

        // Build out the object.
        $data = new stdClass();

        // Get the workdaystudents default settings.
        $s = get_config('enrol_workdaystudent');

        // Build a defaults object for future use.
        $defaults = new stdClass();

        // Get the defaults form config if they're there.
        $defaults->createprior = isset($s->createprior) ? (int) $s->createprior : 14;
        $defaults->enrollprior = isset($s->enrollprior) ? (int) $s->enrollprior : 7;
        $defaults->courselimit = isset($s->numberthreshold) ? (int) $s->numberthreshold : 7;

        // Build out the createprior item with default from wds.
        $data->wdspref_createprior = get_user_preferences(
            'wdspref_createprior',
            $defaults->createprior,
            $userid
        );

        // Build out the enrollprior item with default from wds.
        $data->wdspref_enrollprior = get_user_preferences(
            'wdspref_enrollprior',
            $s->enrollprior,
            $userid
        );

        // Build out the courselimit item with default from wds.
        $data->wdspref_courselimit = get_user_preferences(
            'wdspref_courselimit',
            $s->numberthreshold,
            $userid
        );

        // Set the data.
        $this->set_data($data);
    }
}
