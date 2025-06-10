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
 * This file contains the form used to upload a csv attendance file to automatically update attendance records.
 *
 * @package   mod_attendance
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later */
namespace mod_attendance\form\import;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

use core_text;
use moodleform;
require_once($CFG->libdir.'/formslib.php');

/**
 * Mark attendance sessions confirm csv upload.
 *
 * @package   mod_attendance
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marksessions_confirm extends moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        $params = $this->_customdata;
        $importer = $this->_customdata['importer'];

        $mform = $this->_form;
        $mform->addElement('hidden', 'confirm', 1);
        $mform->setType('confirm', PARAM_BOOL);

        $foundheaders = $importer->list_found_headers();

        // Add user mapping.
        $mform->addElement('select', 'userfrom', get_string('userimportfield', 'attendance'), $foundheaders);
        $mform->addHelpButton('userfrom', 'userimportfield', 'attendance');
        // This allows the user to choose which field in the user database the identifying column will map to.
        $useroptions = array(
            'id'       => get_string('userid', 'attendance'),
            'username'     => get_string('username'),
            'idnumber' => get_string('idnumber'),
            'email'    => get_string('email')
        );
        $mform->addElement('select', 'userto', get_string('userimportto', 'attendance'), $useroptions);

        // Check if we can set an easy default value.
        foreach (array_keys($useroptions) as $o) {
            if (in_array($o, $foundheaders)) {
                $mform->setDefault('userto', $o);
                $mform->setDefault('userfrom', $o);
                break;
            }
        }

        $mform->addHelpButton('userto', 'userimportto', 'attendance');

        // Below options need a "none" option in the headers.
        $foundheaders[- 1] = get_string('notset', 'mod_attendance');
        ksort($foundheaders);

        // Add scan time mapping.
        $mform->addElement('select', 'scantime', get_string('scantime', 'attendance'), $foundheaders);
        $mform->addHelpButton('scantime', 'scantime', 'attendance');
        $mform->setDefault('scantime', -1);

        // Add status mapping.
        $mform->addElement('select', 'status', get_string('importstatus', 'attendance'), $foundheaders);
        $mform->addHelpButton('status', 'importstatus', 'attendance');
        $mform->disabledif('status', 'scantime', 'noteq', -1);
        $mform->disabledif('scantime', 'status', 'noteq', -1);

        // Try to set a useful default value for scantime or status.
        $key = array_search('status', $foundheaders);

        if ($key !== false) {
            // Status is passed in CSV - set that as default.
            $mform->setDefault('status', $key);
            $mform->setDefault('scantime', -1);
        } else {
            $keyscan = array_search('scantime', $foundheaders);
            if ($keyscan !== false) {
                // The Scantime var exists in the csv.
                $mform->setDefault('status', -1);
                $mform->setDefault('scantime', $keyscan);
            } else {
                $mform->setDefault('status', -1);
                $mform->setDefault('scantime', -1);
            }

        }
        foreach (array_keys($useroptions) as $o) {
            if (in_array($o, $foundheaders)) {
                $mform->setDefault('userto', $o);
                $mform->setDefault('userfrom', $o);
                break;
            }
        }

        $mform->addElement('hidden', 'id', $params['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'sessionid', $params['sessionid']);
        $mform->setType('sessionid', PARAM_INT);
        $mform->addElement('hidden', 'grouptype', $params['grouptype']);
        $mform->setType('grouptype', PARAM_INT);
        $mform->addElement('hidden', 'importid', $importer->get_importid());
        $mform->setType('importid', PARAM_INT);
        $mform->setConstant('importid', $importer->get_importid());

        $this->add_action_buttons(true, get_string('uploadattendance', 'attendance'));
    }
}
