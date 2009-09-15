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
 * External user API
 *
 * @package    moodlecore
 * @subpackage webservice
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");

class moodle_user_external extends external_api {

    public static function get_users($params) {
        $context = get_context_instance(CONTEXT_SYSTEM);
        requier_capability('moodle/user:viewdetails', $context);
        self::validate_context($context);

        $search = validate_param($params['search'], PARAM_RAW);

        //TODO: this search is probably useless for external systems because it is not exact
        //      1/ we should specify multiple search parameters including the mnet host id
        //      2/ custom profile fileds not inlcuded

        return get_users(true, $search, false, null, 'firstname ASC','', '', '', 1000, 'id, mnethostid, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat, city, description, country');
    }

    public static function create_users($params) {
        global $CFG, $DB;

        $context = get_context_instance(CONTEXT_SYSTEM);
        requier_capability('moodle/user:create', $context);
        self::validate_context($context);

        //TODO: this list is incomplete - we have preferences and custom fields too
        $accepted = array(
            'password' => PARAM_RAW,
            'auth' => PARAM_SAFEDIR,
            'username' => PARAM_RAW,
            'idnumber' => PARAM_RAW,
            'firstname' => PARAM_CLEAN,
            'lastname' => PARAM_CLEAN,
            'email' => PARAM_EMAIL,
            'emailstop' => PARAM_BOOL,
            'lang' => PARAM_SAFEDIR, // validate using list of available langs - ignored if wrong
            'theme' => PARAM_SAFEDIR,
            'timezone' => PARAM_ALPHANUMEXT,
            'mailformat' => PARAM_ALPHA,
            'description' => PARAM_RAW,
            'city' => PARAM_CLEAN,
            'country' => PARAM_ALPHANUMEXT,
        );

        $required = array('username', 'firstname', 'lastname', 'email', 'password'); //TODO: password may not be required in some cases
        $langs = get_list_of_languages();

        // verify data first, only then start creating records
        $users = array();
        foreach ($params as $data) {
            $user = array();
            foreach ($accepted as $key=>$type) {
                if (array_key_exists($key, $data)) {
                    $user[$key] = validate_param($data[$key], $type);
                    unset($data[$key]);
                }
            }
            if (!empty($data)) {
                throw new invalid_parameter_exception('Unsupported parameters in user array');
            }
            foreach ($required as $req) {
                if (!array_key_exists($req, $user) or empty($user[$req])) {
                    throw new invalid_parameter_exception("$req is required in user array");
                }
            }
            if (!isset($user['auth'])) {
                $user['auth'] = 'manual';
            }
            if (!exists_auth_plugin($user['auth'])) {
                throw new invalid_parameter_exception($user['auth']." is not valid authentication plugin");
            }

            if (isset($user['lang']) and !isset($langs[$user['lang']])) {
                unset($user['lang']);
            }

            //TODO: add more param validations here: username, etc.

            if ($DB->get_record('user', array('username'=>$user['username'], 'mnethostid'=>$CFG->mnet_localhost_id))) {
                throw new invalid_parameter_exception($user['username']." username is already taken, sorry");
            }

            if (isset($users[$user['username']])) {
                throw new invalid_parameter_exception("multiple users with the same username requested");
            }
            $users[$user['username']] = $user;
        }

        $result = array();

        foreach ($users as $user) {
            $record = create_user_record($user['username'], $user['password'], $user['auth']);
            unset($user['username']);
            unset($user['password']);
            unset($user['auth']);

            // now override the default (or external) values
            foreach ($user as $key=>$value) {
                $record->$key = $value;
            }
            $DB->update_record('user', $record);

            unset($record->password); // lets keep this as a secret ;-)
            $result[$record->id] = $record;
        }

        return $result;
    }


    public static function delete_users($params) {
        //TODO
    }


    public static function update_users($params) {
        //TODO
    }
}