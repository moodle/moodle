<?php
/**
 * Created on 05/03/2008
 *
 * users webservice api
 *
 * @author Jerome Mouneyrac
 */
require_once(dirname(dirname(__FILE__)) . '/lib/moodleexternal.php');
require_once(dirname(dirname(__FILE__)) . '/user/lib.php');

/**
 * WORK IN PROGRESS, DO NOT USE IT
 */
final class user_external extends moodle_external {

    /**
     * Constructor - We set the description of this API in order to be access by Web service
     */
    function __construct () {
          $this->descriptions = array();
       ///The desciption of the web service
       ///
       ///'wsparams' and 'return' are used to described the web services to the end user (can build WSDL file from these information)
       ///
       ///Note: web services param names have not importance. However 'paramorder' must match the function params order.
       ///And all web services param names defined into 'wsparams' should be included into 'paramorder' (otherwise they will not be used)
          $this->descriptions['tmp_create_user']   = array( 'params' => array('username'=> PARAM_RAW, 'firstname'=> PARAM_RAW, 'lastname'=> PARAM_RAW, 'email'=> PARAM_RAW, 'password'=> PARAM_RAW),
                                                            'optionalparams' => array( ),
                                                            'return' => array('userid' => PARAM_RAW));

          $this->descriptions['tmp_get_users']     = array( 'params' => array('search'=> PARAM_ALPHANUM),
                                                            'optionalparams' => array( ),
                                                            'return' => array('user' => array('id' => PARAM_RAW, 'auth' => PARAM_RAW, 'confirmed' => PARAM_RAW, 'username' => PARAM_RAW, 'idnumber' => PARAM_RAW,
                                                                                    'firstname' => PARAM_RAW, 'lastname' => PARAM_RAW, 'email' => PARAM_RAW, 'emailstop' => PARAM_RAW,
                                                                                    'lang' => PARAM_RAW, 'theme' => PARAM_RAW, 'timezone' => PARAM_RAW, 'mailformat' => PARAM_RAW)));
    
          $this->descriptions['tmp_delete_user']   = array( 'params' => array('username'=> PARAM_ALPHANUM, 'mnethostid'=> PARAM_NUMBER),
                                                            'optionalparams' => array( ),
                                                            'return' => array('result' => PARAM_BOOL));

          $this->descriptions['tmp_update_user']   = array( 'params' => array('username'=> PARAM_ALPHANUM, 'mnethostid'=> PARAM_NUMBER),
                                                            'optionalparams' => array( 'newusername' => PARAM_ALPHANUM, 'firstname' => PARAM_ALPHANUM),
                                                            'return' => array('result' => PARAM_BOOL));
    }

    /**
     * Retrieve all user
     * @param array $params
     *  ->search string
     * @return object user
     */
    static function tmp_get_users($params) {
        global $USER;
        if (has_capability('moodle/user:viewdetails', get_context_instance(CONTEXT_SYSTEM))) {
            return get_users(true, $params['search'], false, null, 'firstname ASC','', '', '', '', 'id, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat');

        }
        else {
            throw new moodle_exception('couldnotvieweuser');
        }
    }

    /**
     * Create a user
     * @param array $params
     * @param  $username string
     *  ->firstname string
     *  ->lastname string
     *  ->email string
     *  ->password string
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
            return user_lib::tmp_create_user($user);
        }
        else {
            throw new moodle_exception('couldnotcreateeuser');
        }    
    }

    /**
     * Delete a user
     * @global object $DB
     * @param array $params
     *  ->username      string
     *  ->mnethostid    integer
     * @return boolean true if success
     */
    static function tmp_delete_user($params) {
        global $DB,$USER;

        $user = $DB->get_record('user', array('username'=>$params['username'], 'mnethostid'=>$params['mnethostid']));
        if (has_capability('moodle/user:delete', get_context_instance(CONTEXT_SYSTEM))) {
            return delete_user($user); //this function is in moodlelib.php
        }
        else {
            throw new moodle_exception('couldnotdeleteuser');
        }
    }


    /**
     * Update some user information
     * @global object $DB
     * @param array $params
     *  ->username      string
     *  ->mnethostid    integer
     *  ->newusername   string
     *  ->firstname     string
     * @return bool true if success
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
            return user_lib::tmp_update_user($user);
        }
        else {
            throw new moodle_exception('couldnotupdateuser');
        }
       
    }

}

?>
