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

namespace core_badges\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');

use html_writer;
use moodleform;
use stdClass;

/**
 * Form to edit backpack initial details.
 *
 */
class backpack extends external_backpack {

    /**
     * Defines the form
     */
    public function definition() {
        global $USER, $PAGE, $OUTPUT, $CFG;
        $mform = $this->_form;

        $mform->addElement('html', html_writer::tag('span', '', array('class' => 'notconnected', 'id' => 'connection-error')));
        $mform->addElement('header', 'backpackheader', get_string('backpackconnection', 'badges'));
        $mform->addHelpButton('backpackheader', 'backpackconnection', 'badges');
        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'externalbackpackid');
        $mform->setType('externalbackpackid', PARAM_INT);
        if (isset($this->_customdata['email'])) {
            // Email will be passed in when we're in the process of verifying the user's email address,
            // so set the connection status, lock the email field, and provide options to resend the verification
            // email or cancel the verification process entirely and start over.
            $mform->hardFreeze();
            $status = html_writer::tag('span', get_string('backpackemailverificationpending', 'badges'),
                array('class' => 'notconnected', 'id' => 'connection-status'));
        } else {
            $status = html_writer::tag('span', get_string('notconnected', 'badges'),
                array('class' => 'notconnected', 'id' => 'connection-status'));
        }
        $mform->addElement('static', 'status', get_string('status'), $status);

        parent::definition();

        $mform->setDefault('backpackemail', $USER->email);
        $mform->setDisableShortforms(false);
    }

    /**
     * Override add_action_buttons
     *
     * @param bool $cancel
     * @param null|text $submitlabel
     */
    public function add_action_buttons($cancel = true, $submitlabel = null) {
        if ($this->_customdata['email']) {
            $mform = $this->_form;
            $buttonarray = [];
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton',
                                                    get_string('backpackconnectionresendemail', 'badges'));
            $buttonarray[] = &$mform->createElement('submit', 'revertbutton',
                                                    get_string('backpackconnectioncancelattempt', 'badges'));
            $mform->addGroup($buttonarray, 'buttonar', '', [''], false);
            $mform->closeHeaderBefore('buttonar');
        } else {
            // Email isn't present, so provide an input element to get it and a button to start the verification process.

            $mform->addElement('static', 'info', get_string('backpackweburl', 'badges'), $sitebackpack->backpackweburl);
            $mform->addElement('hidden', 'backpackid', $sitebackpack->id);
            $mform->setType('backpackid', PARAM_INT);

            $status = html_writer::tag('span', get_string('notconnected', 'badges'),
                array('class' => 'notconnected', 'id' => 'connection-status'));
            $mform->addElement('static', 'status', get_string('status'), $status);
            if (badges_open_badges_backpack_api() != OPEN_BADGES_V2P1) {
                $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
                $mform->addHelpButton('email', 'backpackemail', 'badges');
                $mform->addRule('email', get_string('required'), 'required', null, 'client');
                $mform->setType('email', PARAM_EMAIL);
                if (badges_open_badges_backpack_api() == OPEN_BADGES_V2) {
                    $mform->addElement('passwordunmask', 'backpackpassword', get_string('password'));
                    $mform->setType('backpackpassword', PARAM_RAW);
                } else {
                    $mform->addElement('hidden', 'backpackpassword', '');
                    $mform->setType('backpackpassword', PARAM_RAW);
                }
            }
            parent::add_action_buttons(false, get_string('backpackconnectionconnect', 'badges'));
        }
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        global $CFG;

        $errors = parent::validation($data, $files);
        if (badges_open_badges_backpack_api() == OPEN_BADGES_V2P1) {
            return $errors;
        }
        // We don't need to verify the email address if we're clearing a pending email verification attempt.
        if (!isset($data['revertbutton'])) {
            $check = new stdClass();
            $check->email = $data['backpackemail'];
            $check->password = $data['password'];
            $check->externalbackpackid = $data['externalbackpackid'];

            $bp = new \core_badges\backpack_api((object) $data, $check);
            $result = $bp->authenticate();
            if ($result === false || !empty($result->error)) {
                $errors['backpackemail'] = get_string('backpackconnectionunexpectedresult', 'badges');
                $msg = $bp->get_authentication_error();
                if (!empty($msg)) {
                    $errors['backpackemail'] .= '<br/><br/>';
                    $errors['backpackemail'] .= get_string('backpackconnectionunexpectedmessage', 'badges', $msg);
                }
            }
        }
        return $errors;
    }
}
