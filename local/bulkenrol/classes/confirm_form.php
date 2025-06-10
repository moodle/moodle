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
 * Local plugin "bulkenrol" - Confirmation form
 *
 * @package   local_bulkenrol
 * @copyright 2017 Soon Systems GmbH on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_bulkenrol;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/formslib.php');

/**
 * Class confirm_form
 * @package local_bulkenrol
 * @copyright 2017 Soon Systems GmbH on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirm_form extends moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        global $SESSION;

        $localbulkenrolkey = $this->_customdata['local_bulkenrol_key'];
        $courseid = $this->_customdata['courseid'];

        $mform = $this->_form;

        $mform->addElement('hidden', 'key');
        $mform->setType('key', PARAM_RAW);
        $mform->setDefault('key', $localbulkenrolkey);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $courseid);

        // Check if we want to show the enrol user button.
        $showenrolebutton = true;
        $checkedmails = null;
        if (isset($SESSION->local_bulkenrol) && array_key_exists($localbulkenrolkey, $SESSION->local_bulkenrol)) {
            $checkedmails = $SESSION->local_bulkenrol[$localbulkenrolkey];
            if (isset($checkedmails->validemailfound) && empty($checkedmails->validemailfound)) {
                $showenrolebutton = false;
            }
        }

        // Only show the enrol user button if necessary.
        if ($showenrolebutton) {
            $this->add_action_buttons(true, get_string('enrol_users', 'local_bulkenrol'));
        }
    }
}
