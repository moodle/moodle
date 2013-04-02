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
 * Form class for mybackpack.php
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Form to edit backpack initial details.
 *
 */
class edit_backpack_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $USER;
        $mform = $this->_form;

        $mform->addElement('header', 'backpackheader', get_string('backpackdetails', 'badges'));
        $mform->addElement('static', 'url', get_string('url'), 'http://backpack.openbadges.org');

        $mform->addElement('text', 'email', get_string('email'), array('size' => '50'));
        $mform->setDefault('email', $USER->email);
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', get_string('required'), 'required', null , 'client');
        $mform->addRule('email', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'backpackurl', 'http://backpack.openbadges.org');
        $mform->setType('backpackurl', PARAM_URL);

        $this->add_action_buttons();
    }
}

/**
 * Form to select backpack group options.
 *
 */
class edit_backpack_group_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $USER;
        $mform = $this->_form;
        $data = $this->_customdata['data'];
        $groups = $this->_customdata['groups'];
        $uid = $this->_customdata['backpackuid'];

        $selet = array();
        foreach ($groups as $group) {
            $select[$group->groupId] = $group->name;
        }

        $mform->addElement('header', 'groupheader', get_string('backpackdetails', 'badges'));
        $mform->addElement('static', 'url', get_string('url'), 'http://backpack.openbadges.org');

        $mform->addElement('text', 'email', get_string('email'), array('size' => '50'));
        $mform->setDefault('email', $data->email);
        $mform->freeze(array('email'));

        $mform->addElement('select', 'backpackgid', get_string('selectgroup', 'badges'), $select);
        $mform->addRule('backpackgid', get_string('required'), 'required', null , 'client');
        if (isset($data->backpackgid)) {
            $mform->setDefault('backpackgid', $data->backpackgid);
        }

        $mform->addElement('hidden', 'userid', $data->userid);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'backpackurl', 'http://backpack.openbadges.org');
        $mform->setType('backpackurl', PARAM_URL);

        $mform->addElement('hidden', 'backpackuid', $uid);
        $mform->setType('backpackuid', PARAM_INT);

        $this->add_action_buttons();
    }
}