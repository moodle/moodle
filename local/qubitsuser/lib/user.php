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
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');

require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

class qubits_user {
    public static function create( $data ) {
        global $DB, $CFG, $USER;

        $cohortid = $data->cohortid;
        $defaults = array(
            "country" => "US", "timezone" => 99, "lang" => "en", "htmleditor" => 1
        );
        $user = (object) array_merge( (array) $defaults, (array) $data);
        $user->username = $data->email;
        $user->mnethostid = $CFG->mnet_localhost_id;

        if ($user->sendnewpasswordemails && !$user->preference_auth_forcepasswordchange) {
            throw new Exception(get_string('cannotemailnontemporarypasswords', 'local_iomad'));
        }
        
        $sendemail = $user->sendnewpasswordemails;

        if (empty($user->auth)) {
            $user->auth = 'qubitsmanual';
        }

        $authplugin = get_auth_plugin($user->auth);
        if ($authplugin->is_internal()) {
            $passwordentered = !empty($user->newpassword);
            $createpassword = !$passwordentered;
            $forcepasswordchange = $user->preference_auth_forcepasswordchange;
            // Store temp password unless password was entered and it's not going to be send by
            // email nor is it going to be forced to change.
            $storetemppassword = !( $passwordentered && !$sendemail && !$forcepasswordchange );

            if ($passwordentered) {
                $user->password = $user->newpassword;   // Don't hash it, user_create_user will do that.
            }
        } else {
            $createpassword = false;
            $forcepasswordchange = false;
            $storetemppassword = false;
            unset($user->password);
        }
        $user->confirmed = 1;
        $user->maildisplay = 0;

        // Create user record and return id.
        $id = user_create_user($user);
        $user->id = $id;

        if ($createpassword) {
            set_user_preference('create_password', 1, $user->id);
            $user->newpassword = generate_password();
            $sendemail = false;
        }

        if ($forcepasswordchange) {
            set_user_preference('auth_forcepasswordchange', 1, $user->id);
        }

        if ($createpassword) {
            $DB->set_field('user', 'password', hash_internal_user_password($user->newpassword),
                            array('id' => $user->id));
        }

        cohort_add_member($cohortid, $user->id);
        return $user->id;
    }

    public static function assign( $data ){
        global $DB, $CFG;

        $cohortid = $data->cohortid;
        $user = $DB->get_record('user', array('email'=> $data->email, 'mnethostid'=>$CFG->mnet_localhost_id));
        $user->auth = "qubitsmanual";
        $DB->update_record('user', $user);
        cohort_add_member($cohortid, $user->id);
        return $user->id;
    }

}