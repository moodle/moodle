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
 * Allows the admin to configure a list of profile fields that are sent/recieved
 *
 * @package    core
 * @subpackage mnet
 * @copyright  2010 onwards Penny Leach <penny@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

/**
 * small form to allow the administrator to configure (override) which profile fields are sent/imported over mnet
 */
class mnet_profile_form extends moodleform {

    function definition() {
        global $CFG;
        $mform =& $this->_form;

        $mnetprofileimportfields = '';
        if (isset($CFG->mnetprofileimportfields)) {
            $mnetprofileimportfields = str_replace(',', ', ', $CFG->mnetprofileimportfields);
        }

        $mnetprofileexportfields = '';
        if (isset($CFG->mnetprofileexportfields)) {
            $mnetprofileexportfields = str_replace(',', ', ', $CFG->mnetprofileexportfields);
        }

        $mform->addElement('hidden', 'hostid', $this->_customdata['hostid']);
        $mform->setType('hostid', PARAM_INT);

        $fields = mnet_profile_field_options();

        // Fields to import ----------------------------------------------------
        $mform->addElement('header', 'import', get_string('importfields', 'mnet'));

        $select = $mform->addElement('select', 'importfields', get_string('importfields', 'mnet'), $fields['optional']);
        $select->setMultiple(true);

        $mform->addElement('checkbox', 'importdefault', get_string('leavedefault', 'mnet'), $mnetprofileimportfields);

        // Fields to export ----------------------------------------------------
        $mform->addElement('header', 'export', get_string('exportfields', 'mnet'));

        $select = $mform->addElement('select', 'exportfields', get_string('exportfields', 'mnet'), $fields['optional']);
        $select->setMultiple(true);

        $mform->addElement('checkbox', 'exportdefault', get_string('leavedefault', 'mnet'), $mnetprofileexportfields);

        $this->add_action_buttons();
    }
}
