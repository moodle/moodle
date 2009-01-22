<?php
/**
 * Created on 05/03/2008
 *
 * users webservice api
 *
 * @author Jerome Mouneyrac
 */
require_once(dirname(dirname(__FILE__)) . '/lib/moodleexternal.php');
require_once(dirname(dirname(__FILE__)) . '/user/api.php');

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
          $this->descriptions['tmp_create_user'] = array( 'wsparams' => array('username'=> PARAM_RAW, 'firstname'=> PARAM_RAW, 'lastname'=> PARAM_RAW, 'email'=> PARAM_RAW, 'password'=> PARAM_RAW),
                                                      'return' => array('userid' => PARAM_RAW));

          $this->descriptions['tmp_get_users']   = array( 'wsparams' => array('search'=> PARAM_ALPHANUM),
                                                      'return' => array('user' => array('id' => PARAM_RAW, 'auth' => PARAM_RAW, 'confirmed' => PARAM_RAW, 'username' => PARAM_RAW, 'idnumber' => PARAM_RAW,
                                                                                    'firstname' => PARAM_RAW, 'lastname' => PARAM_RAW, 'email' => PARAM_RAW, 'emailstop' => PARAM_RAW,
                                                                                    'lang' => PARAM_RAW, 'theme' => PARAM_RAW, 'timezone' => PARAM_RAW, 'mailformat' => PARAM_RAW)));
    
          $this->descriptions['tmp_delete_user']   = array( 'wsparams' => array('username'=> PARAM_ALPHANUM, 'mnethostid'=> PARAM_NUMBER),
                                                      'return' => array('result' => PARAM_BOOL));

          $this->descriptions['tmp_update_user']   = array( 'wsparams' => array('username'=> PARAM_ALPHANUM, 'mnethostid'=> PARAM_NUMBER, 'newusername' => PARAM_ALPHANUM, 'firstname' => PARAM_ALPHANUM),
                                                      'return' => array('result' => PARAM_BOOL));
    }

    /**
     * Retrieve all user
     * @param string $search
     * @return object user
     */
    static function tmp_get_users($search) {
        $selectioncriteria = new stdClass();
        $selectioncriteria->search = $search;
        return user_api::tmp_get_users('firstname ASC', 999999, 0, 'id, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat', $selectioncriteria);
    }

    /**
     * Create a user
     * @param string $username
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @return integer id of new user
     */
    static function tmp_create_user($username, $firstname, $lastname, $email, $password) {
        $user = array();
        $user['username'] = $username;
        $user['firstname'] = $firstname;
        $user['lastname'] = $lastname;
        $user['email'] = $email;
        $user['password'] = $password;
        return user_api::tmp_create_user($user);    
    }

    /**
     * Delete a user
     * @global object $DB
     * @param string $username
     * @param integer $mnethostid
     * @return boolean true if success
     */
    static function tmp_delete_user($username, $mnethostid) {
        global $DB;
        $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$mnethostid));
    /// PLEASE UNCOMMENT HERE ONCE AUTHENTICATION IS IMPLEMENTED - $USER/context need to be set here
        //if (require_capability('moodle/user:delete', get_context_instance(CONTEXT_SYSTEM))) {
            return delete_user($user); //this function is in moodlelib.php
        //}
        //else {
        //    throw new moodle_exception('couldnotdeleteuser');
        //}
    }

    /**
     * Update some user information
     * @global object $DB
     * @param string $username
     * @param integer $mnethostid
     * @param string $newusername
     * @param string $firstname
     * @return boolean true if success
     */
    static function tmp_update_user($username, $mnethostid, $newusername, $firstname) {
        global $DB;
        $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$mnethostid));
        $user->username = $newusername;
        $user->firstname = $firstname;
       
        return user_api::tmp_update_user($user);
    }

}

?>
