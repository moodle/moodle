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
        global $USER, $PAGE, $OUTPUT;
        $mform = $this->_form;

        $mform->addElement('html', html_writer::tag('span', '', array('class' => 'notconnected', 'id' => 'connection-error')));
        $mform->addElement('header', 'backpackheader', get_string('backpackconnection', 'badges'));
        $mform->addHelpButton('backpackheader', 'backpackconnection', 'badges');
        $mform->addElement('static', 'url', get_string('url'), 'http://' . BADGE_BACKPACKURL);
        $status = html_writer::tag('span', get_string('notconnected', 'badges'),
            array('class' => 'notconnected', 'id' => 'connection-status'));
        $mform->addElement('static', 'status', get_string('status'), $status);

        $nojs = html_writer::tag('noscript', get_string('error:personaneedsjs', 'badges'),
            array('class' => 'notconnected'));
        $personadiv = $OUTPUT->container($nojs, null, 'persona-container');

        $mform->addElement('static', 'persona', '', $personadiv);
        $mform->addHelpButton('persona', 'personaconnection', 'badges');

        $PAGE->requires->js(new moodle_url('https://login.persona.org/include.js'));
        $PAGE->requires->js('/badges/backpack.js');
        $PAGE->requires->js_init_call('badges_init_persona_login_button', null, false);
        $PAGE->requires->strings_for_js(array('error:backpackloginfailed', 'signinwithyouremail',
            'error:noassertion', 'error:connectionunknownreason', 'error:badjson', 'connecting',
            'notconnected'), 'badges');

        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'backpackurl', 'http://' . BADGE_BACKPACKURL);
        $mform->setType('backpackurl', PARAM_URL);

    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        $check = new stdClass();
        $check->backpackurl = $data['backpackurl'];
        $check->email = $data['email'];

        $bp = new OpenBadgesBackpackHandler($check);
        $request = $bp->curl_request('user');
        if (isset($request->status) && $request->status == 'missing') {
            $errors['email'] = get_string('error:nosuchuser', 'badges');
        }
        return $errors;
    }
}

/**
 * Form to select backpack collections.
 *
 */
class edit_collections_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $USER;
        $mform = $this->_form;
        $email = $this->_customdata['email'];
        $bid = $this->_customdata['backpackid'];
        $selected = $this->_customdata['selected'];

        if (isset($this->_customdata['groups'])) {
            $groups = $this->_customdata['groups'];
            $nogroups = null;
        } else {
            $groups = null;
            $nogroups = $this->_customdata['nogroups'];
        }

        $mform->addElement('header', 'backpackheader', get_string('backpackconnection', 'badges'));
        $mform->addHelpButton('backpackheader', 'backpackconnection', 'badges');
        $mform->addElement('static', 'url', get_string('url'), 'http://' . BADGE_BACKPACKURL);

        $status = html_writer::tag('span', get_string('connected', 'badges'), array('class' => 'connected'));
        $mform->addElement('static', 'status', get_string('status'), $status);
        $mform->addElement('static', 'email', get_string('email'), $email);
        $mform->addHelpButton('email', 'backpackemail', 'badges');
        $mform->addElement('submit', 'disconnect', get_string('disconnect', 'badges'));

        $mform->addElement('header', 'collectionheader', get_string('backpackimport', 'badges'));
        $mform->addHelpButton('collectionheader', 'backpackimport', 'badges');

        if (!empty($groups)) {
            $mform->addElement('static', 'selectgroup', '', get_string('selectgroup_start', 'badges'));
            foreach ($groups as $group) {
                $name = $group->name . ' (' . $group->badges . ')';
                $mform->addElement('advcheckbox', 'group[' . $group->groupId . ']', null, $name, array('group' => 1), array(false, $group->groupId));
                if (in_array($group->groupId, $selected)) {
                    $mform->setDefault('group[' . $group->groupId . ']', $group->groupId);
                }
            }
            $mform->addElement('static', 'selectgroup', '', get_string('selectgroup_end', 'badges'));
        } else {
            $mform->addElement('static', 'selectgroup', '', $nogroups);
        }

        $mform->addElement('hidden', 'backpackid', $bid);
        $mform->setType('backpackid', PARAM_INT);

        $this->add_action_buttons();
    }
}
