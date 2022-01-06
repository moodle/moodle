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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Extends moodleform to create a form for editing a user.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class useredit_form extends \moodleform {
    /**
     * Define the layout and content of the form.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'manual');
        $mform->setType('manual', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ACTION);

        $mform->addElement('passwordunmask', 'password', get_string('password'));
        $mform->addRule('password', null, 'required');

        $this->add_action_buttons();
    }

    /**
     * Perform minimal validation on the settings form.
     *
     * @param array  $data Array of data from the form.
     * @param array  $files Array of files from the form.
     * @return array  Validation errors.
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // We are going to check the user authentication.
        $user = $DB->get_record('user', array('id' => $data['id']));
        $webexuser = user::load_for_user($user, false);
        if (!$webexuser) {
            throw new \coding_exception('An unknown error occurred while trying to reload the user');
        }
        $webexuser->password = $data['password'];
        if (!$webexuser->check_user_auth()) {
            $errors['password'] = get_string('badpassword', 'webexactivity');
        }

        return $errors;
    }
}
