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
 * Form class for editing badges preferences.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2013 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir . '/formslib.php');

class badges_preferences_form extends moodleform {
    public function definition() {
        global $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'badgeprivacy', get_string('badgeprivacysetting', 'badges'));
        $mform->addElement('advcheckbox', 'badgeprivacysetting', '', get_string('badgeprivacysetting_str', 'badges'));
        $mform->setType('badgeprivacysetting', PARAM_INT);
        $mform->setDefault('badgeprivacysetting', 1);
        $mform->addHelpButton('badgeprivacy', 'badgeprivacysetting', 'badges');

        $this->add_action_buttons();
    }
}
