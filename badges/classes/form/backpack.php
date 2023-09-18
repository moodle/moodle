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
        $this->_customdata['userbackpack'] = 1;

        $mform->addElement('html', html_writer::tag('span', '', array('class' => 'notconnected', 'id' => 'connection-error')));
        $mform->addElement('header', 'backpackheader', get_string('backpackconnection', 'badges'));
        $mform->addHelpButton('backpackheader', 'backpackconnection', 'badges');
        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);
        $freeze = [];
        $status = null;
        if (isset($this->_customdata['email'])) {
            // Email will be passed in when we're in the process of verifying the user's email address,
            // so set the connection status, lock the email field, and provide options to resend the verification
            // email or cancel the verification process entirely and start over.
            $freeze = ['backpackemail'];
            $mform->addElement('hidden', 'password', $this->_customdata['backpackpassword']);
            $mform->setType('password', PARAM_RAW);
            $mform->addElement('hidden', 'externalbackpackid', $this->_customdata['backpackid']);
            $mform->setType('externalbackpackid', PARAM_INT);
            $status = html_writer::tag('span', get_string('backpackemailverificationpending', 'badges'),
                array('class' => 'notconnected', 'id' => 'connection-status'));
        } else {
            $sitebackpacks = badges_get_site_backpacks();
            $choices = [
                '' => get_string('choosedots'),
            ];
            $restrictedoptions = [''];
            foreach ($sitebackpacks as $backpack) {
                $choices[$backpack->id] = $backpack->backpackweburl;
                if ($backpack->apiversion == OPEN_BADGES_V2P1) {
                    $restrictedoptions[] = $backpack->id;
                }
            }
            $mform->addElement('select', 'externalbackpackid', get_string('backpackprovider', 'badges'), $choices);
            $mform->setType('externalbackpackid', PARAM_INT);
            $mform->addRule('externalbackpackid', get_string('required'), 'required');
            $mform->hideIf('password', 'externalbackpackid', 'in', $restrictedoptions);
            $mform->hideIf('backpackemail', 'externalbackpackid', 'in', $restrictedoptions);

            // Static form element can't be used because they don't support hideIf. This is a workaround until MDL-66251 is fixed.
            $group = [];
            $group[] = $mform->createElement('static', 'loginbackpackgroup', '', get_string('loginbackpacktitle', 'badges'));
            $mform->addGroup($group, 'loginbackpackgroup', '', '', false);
            $mform->hideIf('loginbackpackgroup', 'externalbackpackid', 'in', $restrictedoptions);
        }

        if ($status) {
            // Only display the status if it's set.
            $mform->addElement('static', 'status', get_string('status'), $status);
        }

        $this->add_auth_fields($this->_customdata['email'] ?? $USER->email, !isset($this->_customdata['email']));
        // Only display email and password when the user has selected a backpack.
        $mform->hideIf('backpackemail', 'externalbackpackid', 'eq', '');
        $mform->hideIf('password', 'externalbackpackid', 'eq', '');

        $mform->setDisableShortforms(false);

        // Freeze any elemnts after definition.
        if ($freeze) {
            $mform->freeze($freeze);
        }
        $this->add_action_buttons();
    }

    /**
     * Override add_action_buttons
     *
     * @param bool $cancel
     * @param null|text $submitlabel
     */
    public function add_action_buttons($cancel = true, $submitlabel = null) {
        $mform = $this->_form;
        if (isset($this->_customdata['email'])) {
            $buttonarray = [];
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton',
                                                    get_string('backpackconnectionresendemail', 'badges'));
            $buttonarray[] = &$mform->createElement('submit', 'revertbutton',
                                                    get_string('backpackconnectioncancelattempt', 'badges'));
            $mform->addGroup($buttonarray, 'buttonar', '', [''], false);
            $mform->closeHeaderBefore('buttonar');
        } else {
            // Email isn't present, so provide an input element to get it and a button to start the verification process.
            parent::add_action_buttons(false, get_string('backpackconnectionconnect', 'badges'));
        }
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        // Verify that the user has selected a backpack.
        if (empty($data['externalbackpackid'])) {
            $errors['externalbackpackid'] = get_string('externalbackpack_required', 'badges');
            return $errors;
        }

        // We don't need to verify anything for OBv2.1.
        if (badges_open_badges_backpack_api() == OPEN_BADGES_V2P1) {
            return [];
        }

        // We don't need to verify the email address if we're clearing a pending email verification attempt.
        if (isset($data['revertbutton'])) {
            return [];
        }

        $errors = [];
        // Email and password can't be blank.
        if (empty($data['backpackemail'])) {
            $errors['backpackemail'] = get_string('backpackemail_required', 'badges');
        }
        if (empty($data['password'])) {
            $errors['password'] = get_string('password_required', 'badges');
        }
        if (!empty($errors)) {
            return $errors;
        }

        // Check the given credentials (email and password) are valid for this backpack.
        $check = new stdClass();
        $check->email = $data['backpackemail'];
        $check->password = $data['password'];
        $sitebackpack = badges_get_site_backpack($data['externalbackpackid']);
        $bp = new \core_badges\backpack_api($sitebackpack, $check);

        $result = $bp->authenticate();
        if ($result === false || !empty($result->error)) {
            $msg = $bp->get_authentication_error();
            $errors['backpackemail'] = get_string('backpackconnectionunexpectedresult', 'badges', $msg);
        }
        return $errors;
    }
}
