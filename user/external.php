<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   user
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */
require_once(dirname(dirname(__FILE__)) . '/user/lib.php');

/**
 * users webservice api
 *
 * @author Jerome Mouneyrac
 */
final class user_external {

    /**
     * Retrieve all user
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params->search - the string to search
     * @return object $return
     * @subreturn integer $return:user->id
     * @subreturn integer $return:user->auth
     * @subreturn integer $return:user->confirmed
     * @subreturn string $return:user->username
     * @subreturn string $return:user->idnumber
     * @subreturn string $return:user->firstname
     * @subreturn string $return:user->lastname
     * @subreturn string $return:user->email
     * @subreturn string $return:user->emailstop
     * @subreturn string $return:user->lang
     * @subreturn string $return:user->theme
     * @subreturn string $return:user->timezone
     * @subreturn string $return:user->mailformat
     */
    static function get_users($params) {
        global $USER;

        $params['search'] = clean_param($params['search'], PARAM_ALPHANUM);

        if (has_capability('moodle/user:viewdetails', get_context_instance(CONTEXT_SYSTEM))) {
            return get_users(true, $params['search'], false, null, 'firstname ASC','', '', '', 1000, 'id, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat, city, description, country');
        }
        else {
            throw new moodle_exception('wscouldnotvieweusernopermission');
        }
    }

     /**
     * Create multiple users
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params:user->username
     * @subparam string $params:user->firstname
     * @subparam string $params:user->lastname
     * @subparam string $params:user->email
     * @subparam string $params:user->password
     * @return array $return ids of new user
     * @subreturn integer $return:id user id
     */
    static function create_users($params) {
        global $USER;
        if (has_capability('moodle/user:create', get_context_instance(CONTEXT_SYSTEM))) {
            $userids = array();
            foreach ($params as $userparams) {

                $user = new stdClass();
                if (array_key_exists('email', $userparams)) {
                    $user->email =  clean_param($userparams['email'], PARAM_NOTAGS);
                }

                if (array_key_exists('password', $userparams)) {
                    $user->password =  clean_param($userparams['password'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('idnumber', $userparams)) {
                    $user->idnumber =  clean_param($userparams['idnumber'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('description', $userparams)) {
                    $user->description =  clean_param($userparams['description'], PARAM_TEXT);
                }

                if (array_key_exists('username', $userparams)) {
                    $user->username =  clean_param($userparams['username'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('auth', $userparams)) {
                    $user->auth =  clean_param($userparams['auth'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('confirmed', $userparams)) {
                    $user->confirmed =  clean_param($userparams['confirmed'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('firstname', $userparams)) {
                    $user->firstname =  clean_param($userparams['firstname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('lastname', $userparams)) {
                    $user->lastname =  clean_param($userparams['lastname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('emailstop', $userparams)) {
                    $user->emailstop =  clean_param($userparams['emailstop'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('lang', $userparams)) {
                    $user->lang =  clean_param($userparams['lang'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('theme', $userparams)) {
                    $user->theme =  clean_param($userparams['theme'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('timezone', $userparams)) {
                    $user->timezone =  clean_param($userparams['timezone'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('city', $userparams)) {
                    $user->city =  clean_param($userparams['city'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('country', $userparams)) {
                    $user->country =  clean_param($userparams['country'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('mailformat', $userparams)) {
                    $user->mailformat =  clean_param($userparams['mailformat'], PARAM_ALPHANUMEXT);
                }

                try {
                    $userids[$userparams['username']] = create_user($user);
                }
                catch (dml_write_exception $e) {
                    throw new moodle_exception('wscouldnotcreateeuserindb');
                }
            }
            return $userids;
        }
        else {
            throw new moodle_exception('wscouldnotcreateeusernopermission');
        }
    }

     /**
     * Delete multiple users
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params:user->username
     * @return boolean result true if success
     */
    static function delete_users($params) {
        global $DB,$USER;
        $deletionsuccessfull = true;
        if (has_capability('moodle/user:delete', get_context_instance(CONTEXT_SYSTEM))) {
            foreach ($params as $userparams) {

                $username  = clean_param($userparams['username'], PARAM_ALPHANUMEXT);

                $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>1));

                if (empty($user)) {
                    throw new moodle_exception('wscouldnotdeletenoexistinguser');
                }

                if (!delete_user($user)) {
                    $deletionsuccessfull = false; //this function is in moodlelib.php
                }
            }
            return $deletionsuccessfull;
        }
        else {
            throw new moodle_exception('wscouldnotdeleteusernopermission');
        }
    }

     /**
     * Update some users information
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params:user->username
     * @subparam string $params:user->newusername
     * @subparam string $params:user->firstname
     * @return boolean result true if success
     */
    static function update_users($params) {
        global $DB,$USER;
        if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM))) {
            $updatesuccessfull = true;

            foreach ($params as $userparams) {
                if (array_key_exists('username', $userparams)) {
                    $username =  clean_param($userparams['username'], PARAM_NOTAGS);
                }

                $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>1));

                if (empty($user)) {
                    throw new moodle_exception('wscouldnotupdatenoexistinguser');
                }

                if (array_key_exists('email', $userparams)) {
                    $user->email =  clean_param($userparams['email'], PARAM_NOTAGS);
                }

                if (array_key_exists('description', $userparams)) {
                    $user->description =  clean_param($userparams['description'], PARAM_TEXT);
                }

                if (array_key_exists('newusername', $userparams)) {
                    $user->username =  clean_param($userparams['newusername'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('auth', $userparams)) {
                    $user->auth =  clean_param($userparams['auth'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('confirmed', $userparams)) {
                    $user->confirmed =  clean_param($userparams['confirmed'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('firstname', $userparams)) {
                    $user->firstname =  clean_param($userparams['firstname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('lastname', $userparams)) {
                    $user->lastname =  clean_param($userparams['lastname'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('emailstop', $userparams)) {
                    $user->emailstop =  clean_param($userparams['emailstop'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('lang', $userparams)) {
                    $user->lang =  clean_param($userparams['lang'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('theme', $userparams)) {
                    $user->theme =  clean_param($userparams['theme'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('timezone', $userparams)) {
                    $user->timezone =  clean_param($userparams['timezone'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('city', $userparams)) {
                    $user->city =  clean_param($userparams['city'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('country', $userparams)) {
                    $user->country =  clean_param($userparams['country'], PARAM_ALPHANUMEXT);
                }

                if (array_key_exists('mailformat', $userparams)) {
                    $user->mailformat =  clean_param($userparams['mailformat'], PARAM_ALPHANUMEXT);
                }

                try {
                    if( !update_user($user)) {
                        $updatesuccessfull = false;
                    }
                }
                catch (dml_write_exception $e) {
                    throw new moodle_exception('wscouldnotupdateuserindb');
                }
            }
            return $updatesuccessfull;
        }
        else {
            throw new moodle_exception('wscouldnotupdateusernopermission');
        }

    }

}

?>
