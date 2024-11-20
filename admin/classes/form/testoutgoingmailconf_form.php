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
 * Testing outgoing mail configuration form
 *
 * @package    core
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_admin\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Test mail form
 *
 * @package    core
 * @copyright 2019 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testoutgoingmailconf_form extends \moodleform {
    /**
     * Add elements to form
     */
    public function definition() {
        $mform = $this->_form;

        // Recipient.
        $options = ['maxlength' => '100', 'size' => '25', 'autocomplete' => 'email'];
        $mform->addElement('text', 'recipient', get_string('testoutgoingmailconf_toemail', 'admin'), $options);
        $mform->setType('recipient', PARAM_EMAIL);
        $mform->addRule('recipient', get_string('required'), 'required');

        // From user.
        $options = ['maxlength' => '100', 'size' => '25'];
        $mform->addElement('text', 'from', get_string('testoutgoingmailconf_fromemail', 'admin'), $options);
        $mform->setType('from', PARAM_TEXT);
        $mform->addHelpButton('from', 'testoutgoingmailconf_fromemail', 'admin');

        // Additional subject text.
        $options = ['size' => '25'];
        $mform->addElement('text', 'additionalsubject', get_string('testoutgoingmailconf_subjectadditional', 'admin'), $options);
        $mform->setType('additionalsubject', PARAM_TEXT);

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'send', get_string('testoutgoingmailconf_sendtest', 'admin'));
        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    /**
     * Validate Form field, should be a valid email format or a username that matches with a Moodle user.
     *
     * @param array $data
     * @param array $files
     * @return array
     * @throws \dml_exception|\coding_exception
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (isset($data['from']) && $data['from']) {
            $userfrom = \core_user::get_user_by_username($data['from']);

            if (!$userfrom && !validate_email($data['from'])) {
                $errors['from'] = get_string('testoutgoingmailconf_fromemail_invalid', 'admin');
            }
        }

        return $errors;
    }
}
