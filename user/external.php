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
require_once(dirname(dirname(__FILE__)) . '/lib/moodleexternal.php');
require_once(dirname(dirname(__FILE__)) . '/user/lib.php');

/**
 * users webservice api
 *
 * @author Jerome Mouneyrac
 */
final class user_external extends moodle_external {

/**
 * Constructor - We set the description of this API in order to be access by Web service
 */
    function __construct () {
        $this->descriptions = array();

        $user = new object();
        $user->password = PARAM_ALPHANUMEXT;
        $user->auth = PARAM_ALPHANUMEXT;
        $user->confirmed = PARAM_NUMBER;
        $user->username = PARAM_ALPHANUMEXT;
        $user->idnumber = PARAM_ALPHANUMEXT;
        $user->firstname = PARAM_ALPHANUMEXT;
        $user->lastname = PARAM_ALPHANUMEXT;
        $user->email = PARAM_NOTAGS;
        $user->emailstop = PARAM_NUMBER;
        $user->lang = PARAM_ALPHA;
        $user->theme = PARAM_ALPHANUM;
        $user->timezone = PARAM_ALPHANUMEXT;
        $user->mailformat = PARAM_ALPHA;
        $user->description = PARAM_TEXT;
        $user->city = PARAM_ALPHANUMEXT;
        $user->country = PARAM_ALPHANUMEXT;
        $params = new object();
        $params->users = array($user);
        $return = new object();
        $return->userids = array(PARAM_NUMBER);
        $this->descriptions['create_users']   = array( 'params' => $params,
            'optionalinformation' => 'Username, password, firstname, and username are the only mandatory',
            'return' => $return,
            'service' => 'user',
            'requiredlogin' => 0);

        $user = new object();
        $user->id = PARAM_NUMBER;
        $user->auth = PARAM_ALPHANUMEXT;
        $user->confirmed = PARAM_NUMBER;
        $user->username = PARAM_ALPHANUMEXT;
        $user->idnumber = PARAM_ALPHANUMEXT;
        $user->firstname = PARAM_ALPHANUMEXT;
        $user->lastname = PARAM_ALPHANUMEXT;
        $user->email = PARAM_NOTAGS;
        $user->emailstop = PARAM_NUMBER;
        $user->lang = PARAM_ALPHA;
        $user->theme = PARAM_ALPHANUM;
        $user->timezone = PARAM_ALPHANUMEXT;
        $user->mailformat = PARAM_ALPHA;
        $user->description = PARAM_TEXT;
        $user->city = PARAM_ALPHANUMEXT;
        $user->country = PARAM_ALPHANUMEXT;
        $params = new object();
        $params->search = PARAM_ALPHANUM;
        $return = new object();
        $return->users = array($user);
        $this->descriptions['get_users']     = array( 'params' => $params,
            'optionalparams' => 'All params are not mandatory',
            'return' => $return,
            'service' => 'user',
            'requiredlogin' => 0);

        $params = new object();
        $params->usernames = array(PARAM_ALPHANUMEXT);
        $return = new object();
        $return->result = PARAM_BOOL;
        $this->descriptions['delete_users']   = array( 'params' => $params,
            'optionalparams' => 'All params are not mandatory',
            'return' => $return,
            'service' => 'user',
            'requiredlogin' => 0);

        $user->newusername = PARAM_ALPHANUMEXT;
        $params = new object();
        $params->users = array($user);
        $this->descriptions['update_users']   = array( 'params' => $params,
            'optionalparams' => 'All params are not mandatory',
            'return' => $return,
            'service' => 'user',
            'requiredlogin' => 0);
    }

    /**
     * Retrieve all user
     * @param object|struct $params - need to be define as struct for XMLRPC
     * @return object $return
     */
    public function get_users($params) {
        global $USER;

        $this->clean_function_params('get_users', $params);

        if (has_capability('moodle/user:viewdetails', get_context_instance(CONTEXT_SYSTEM))) {
            return get_users(true, $params->search, false, null, 'firstname ASC','', '', '', 1000, 'id, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat, city, description, country');
        }
        else {
            throw new moodle_exception('wscouldnotvieweusernopermission');
        }
    }

    /**
     * Create multiple users
     * @param object|struct $params - need to be define as struct for XMLRPC
     * @return object $return
     */
    public function create_users($params) {
        global $USER;
        if (has_capability('moodle/user:create', get_context_instance(CONTEXT_SYSTEM))) {
            $userids = array();
            $this->clean_function_params('create_users', $params);
            foreach ($params->users as $user) {
                try {
                    $userids[$user->username] = create_user($user);
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
     * @param object|struct $params - need to be define as struct for XMLRPC
     * @return boolean result true if success
     */
    public function delete_users($params) {
        global $DB,$USER;
        $deletionsuccessfull = true;
        if (has_capability('moodle/user:delete', get_context_instance(CONTEXT_SYSTEM))) {

            $this->clean_function_params('delete_users', $params);
            
            foreach ($params->usernames as $username) {
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
     * @param object|struct $params - need to be define as struct for XMLRPC
     * @return boolean result true if success
     */
    public function update_users($params) {
        global $DB,$USER;
        if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM))) {
            $updatesuccessfull = true;

            $this->clean_function_params('update_users', $params);

            foreach ($params->users as $paramuser) {

                $user = $DB->get_record('user', array('username'=> $paramuser->username, 'mnethostid'=>1));

                if (empty($user)) {
                    throw new moodle_exception('wscouldnotupdatenoexistinguser');
                }
                $user->username = $paramuser->newusername;
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
