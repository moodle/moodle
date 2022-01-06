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

require('../../config.php');

$id = optional_param('id', $USER->id, PARAM_INT); // User ID.
$action = optional_param('action', false, PARAM_ALPHA);
$view = optional_param('view', false, PARAM_ALPHA);

$user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);

// Security.
require_login();
$context = context_user::instance($id);


// Page setup.
$returnurl = new moodle_url('/mod/webexactivity/index.php', array('id' => $id));
$PAGE->set_url($returnurl);
$PAGE->set_pagelayout('incourse');
$PAGE->set_context($context);
$PAGE->set_title(get_string('modulename', 'webexactivity'));

switch($action) {
    case 'useredit':
        $view = 'useredit';

        // Load the form for recording editing.
        $mform = new \mod_webexactivity\useredit_form();

        if ($mform->is_cancelled()) {
            \mod_webexactivity\webex::password_return_redirect();
        } else if ($fromform = $mform->get_data()) {
            $webexuser = \mod_webexactivity\user::load_for_user($user, false);
            if (!$webexuser) {
                throw new coding_exception('An unknown error occurred while trying to reload the user');
            }
            $webexuser->password = $fromform->password;
            $webexuser->save_to_db();

            \mod_webexactivity\webex::password_return_redirect();
        } else {
            $webexuser = false;
            try {
                $webexuser = \mod_webexactivity\user::load_for_user($user);
            } catch (\mod_webexactivity\local\exception\webexactivity_exception $e) {
                // TODO: Should not be needed after 0.2.0.
                $webexuser = \mod_webexactivity\user::create();
                $webexuser->moodleuserid = $id;
                $webexuser->email = $user->email;
                if ($webexuser->update_from_webex()) {
                    $webexuser->save_to_db();
                } else {
                    $webexuser = false;
                }
            }

            if ($webexuser) {
                $data = new stdClass();
                $data->id = $id;
                $data->action = 'useredit';
                if (isset($webexuser->password)) {
                    $data->password = $webexuser->password;
                } else {
                    $data->password = '';
                }

                $mform->set_data($data);
            } else {
                print_error('usereditunabletoload', 'webexactivity');
            }
            break;
        }

        break;
}


echo $OUTPUT->header();
echo $OUTPUT->heading(format_string(get_string('modulenameplural', 'webexactivity')));

if (!isset($webexuser) || !$webexuser) {
    print_error('usereditbad', 'webexactivity');
} else if (!$webexuser->manual) {
    print_error('usereditauto', 'webexactivity');
} else if ($view === 'useredit') {
    $params = array('email' => $webexuser->email, 'username' => $webexuser->webexid);
    echo get_string('userexistsexplanation', 'webexactivity', $params);
    $mform->display();
} else {
    // Could not load any user.
    print_error('usereditbad', 'webexactivity');
}

echo $OUTPUT->footer();
