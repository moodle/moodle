<?php
/**
 * Created on 05/03/2008
 *
 * users webservice api
 *
 * @author Jerome Mouneyrac
 */
require_once(dirname(dirname(__FILE__)) . '/user/lib.php');

/**
 * WORK IN PROGRESS, DO NOT USE IT
 */
final class user_external {

    /**
     * This docblock has a right syntax but it does not match the real function parameters - except @ param and @ return
     * I just keep it for a while till we implement a real ws function using complex blockdoc syntax as this one
     * Understand, this dockblock is a example...
     * @global object $USER
     * @param array|struct $params
     * @subparam string $params:searches->search - the string to search
     * @subparam string $params:searches->search2 optional - the string to search
     * @subparam string $params:searches->search3 - the string to search
     * @subparam string $params:airport->planes:plane->company->employees:employee->name - name of a employee of a company of a plane of an airport
     * @return array users  
     * @subreturn integer $users:user->id
     * @subreturn integer $users:user->auth
     * @subreturn integer $users:user->confirmed
     * @subreturn string $users:user->username
     * @subreturn string $users:user->idnumber
     * @subreturn string $users:user->firstname
     * @subreturn string $users:user->lastname
     * @subreturn string $users:user->email
     * @subreturn string $users:user->emailstop
     * @subreturn string $users:user->lang
     * @subreturn string $users:user->theme
     * @subreturn string $users:user->timezone
     * @subreturn string $users:user->mailformat
     */
    static function tmp_do_multiple_user_searches($params) {
        global $USER;
        if (has_capability('moodle/user:viewdetails', get_context_instance(CONTEXT_SYSTEM))) {
            $users = array();
            foreach($params as $searchparams) {
                $searchusers = get_users(true, $searchparams['search'], false, null, 'firstname ASC','', '', '', 1000, 'id, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat');
                foreach ($searchusers as $user) {
                    $users[] = $user;
                }
            }
            return $users;
        }
        else {
            throw new moodle_exception('wscouldnotvieweuser');
        }
    }
    
    /**
     * Retrieve all user
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params->search - the string to search
     * @return object users 
     * @subreturn integer $users:user->id
     * @subreturn integer $users:user->auth
     * @subreturn integer $users:user->confirmed
     * @subreturn string $users:user->username
     * @subreturn string $users:user->idnumber
     * @subreturn string $users:user->firstname
     * @subreturn string $users:user->lastname
     * @subreturn string $users:user->email
     * @subreturn string $users:user->emailstop
     * @subreturn string $users:user->lang
     * @subreturn string $users:user->theme
     * @subreturn string $users:user->timezone
     * @subreturn string $users:user->mailformat
     */
    static function tmp_get_users($params) {
        global $USER;

        $params['search'] = clean_param($params['search'], PARAM_ALPHANUM);

        if (has_capability('moodle/user:viewdetails', get_context_instance(CONTEXT_SYSTEM))) {
           // return "toto";
            return get_users(true, $params['search'], false, null, 'firstname ASC','', '', '', 1000, 'id, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat');
        }
        else {
            throw new moodle_exception('wscouldnotvieweuser');
        }
    }

    /**
     * Create a user
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params->username
     * @subparam string $params->firstname
     * @subparam string $params->lastname
     * @subparam string $params->email
     * @subparam string $params->password
     * @return integer id of new user
     */
    static function tmp_create_user($params) {
        global $USER;
        if (has_capability('moodle/user:create', get_context_instance(CONTEXT_SYSTEM))) {
            $user = array();
            $user['username'] = $params['username'];
            $user['firstname'] = $params['firstname'];
            $user['lastname'] = $params['lastname'];
            $user['email'] = $params['email'];
            $user['password'] = $params['password'];
            return tmp_create_user($user);
        }
        else {
            throw new moodle_exception('wscouldnotcreateeuser');
        }    
    }

    /**
     * Delete a user
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params->username
     * @subparam integer $params->mnethostid
     * @return boolean result true if success
     */
    static function tmp_delete_user($params) {
        global $DB,$USER;
        if (has_capability('moodle/user:delete', get_context_instance(CONTEXT_SYSTEM))) {
            $user = $DB->get_record('user', array('username'=>$params['username'], 'mnethostid'=>$params['mnethostid']));
            return delete_user($user); //this function is in moodlelib.php
        }
        else {
            throw new moodle_exception('wscouldnotdeleteuser');
        }
    }


    /**
     * Update some user information
     * @global object $DB
     * @param array|struct $params - need to be define as struct for XMLRPC
     * @subparam string $params->username
     * @subparam integer $params->mnethostid
     * @subparam string $params->newusername
     * @subparam string $params->firstname
     * @return boolean result true if success
     */
    static function tmp_update_user($params) {
        global $DB,$USER;
        if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM))) {
            $user = $DB->get_record('user', array('username'=>$params['username'], 'mnethostid'=>$params['mnethostid']));

            if (!empty($params['newusername'])) {
                $user->username = $params['newusername'];
            }
            if (!empty($params['firstname'])) {
                $user->firstname = $params['firstname'];
            }
            return tmp_update_user($user);
        }
        else {
            throw new moodle_exception('wscouldnotupdateuser');
        }
       
    }

}

?>
