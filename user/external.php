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


die('this file is being migrated to exxternallib.php right now...');

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
                    $DB->update_record('user', $user);
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


