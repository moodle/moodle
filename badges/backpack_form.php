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
        $mform->addElement('static', 'url', get_string('url'), BADGE_BACKPACKURL);

        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);

        if (isset($this->_customdata['email'])) {
            // Email will be passed in when we're in the process of verifying the user's email address,
            // so set the connection status, lock the email field, and provide options to resend the verification
            // email or cancel the verification process entirely and start over.
            $status = html_writer::tag('span', get_string('backpackemailverificationpending', 'badges'),
                array('class' => 'notconnected', 'id' => 'connection-status'));
            $mform->addElement('static', 'status', get_string('status'), $status);
            $mform->addElement('hidden', 'email', $this->_customdata['email']);
            $mform->setType('email', PARAM_EMAIL);
            $mform->hardFreeze(['email']);
            $emailverify = html_writer::tag('span', s($this->_customdata['email']), []);
            $mform->addElement('static', 'emailverify', get_string('email'), $emailverify);
            $buttonarray = [];
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton',
                                                    get_string('backpackconnectionresendemail', 'badges'));
            $buttonarray[] = &$mform->createElement('submit', 'revertbutton',
                                                    get_string('backpackconnectioncancelattempt', 'badges'));
            $mform->addGroup($buttonarray, 'buttonar', '', [''], false);
            $mform->closeHeaderBefore('buttonar');
        } else {
            // Email isn't present, so provide an input element to get it and a button to start the verification process.
            $status = html_writer::tag('span', get_string('notconnected', 'badges'),
                array('class' => 'notconnected', 'id' => 'connection-status'));
            $mform->addElement('static', 'status', get_string('status'), $status);
            $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
            $mform->addHelpButton('email', 'backpackemail', 'badges');
            $mform->addRule('email', get_string('required'), 'required', null, 'client');
            $mform->setType('email', PARAM_EMAIL);
            $this->add_action_buttons(false, get_string('backpackconnectionconnect', 'badges'));
        }
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // We don't need to verify the email address if we're clearing a pending email verification attempt.
        if (!isset($data['revertbutton'])) {
            $check = new stdClass();
            $check->backpackurl = BADGE_BACKPACKURL;
            $check->email = $data['email'];

            $bp = new OpenBadgesBackpackHandler($check);
            $request = $bp->curl_request('user');
            if (isset($request->status) && $request->status == 'missing') {
                $errors['email'] = get_string('error:nosuchuser', 'badges');
            } else if (!isset($request->status) || $request->status !== 'okay') {
                $errors['email'] = get_string('backpackconnectionunexpectedresult', 'badges');
            }
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
        $mform->addElement('static', 'url', get_string('url'), BADGE_BACKPACKURL);

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
