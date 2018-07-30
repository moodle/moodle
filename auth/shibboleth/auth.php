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
 * Authentication Plugin: Shibboleth Authentication
 * Authentication using Shibboleth.
 *
 * Distributed under GPL (c)Markus Hagman 2004-2006
 *
 * @package auth_shibboleth
 * @author Martin Dougiamas
 * @author Lukas Haemmerle
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Shibboleth authentication plugin.
 */
class auth_plugin_shibboleth extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'shibboleth';
        $this->config = get_config('auth_shibboleth');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_shibboleth() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
       global $SESSION;

        // If we are in the shibboleth directory then we trust the server var
        if (!empty($_SERVER[$this->config->user_attribute])) {
            // Associate Shibboleth session with user for SLO preparation
            $sessionkey = '';
            if (isset($_SERVER['Shib-Session-ID'])){
                // This is only available for Shibboleth 2.x SPs
                $sessionkey = $_SERVER['Shib-Session-ID'];
            } else {
                // Try to find out using the user's cookie
                foreach ($_COOKIE as $name => $value){
                    if (preg_match('/_shibsession_/i', $name)){
                        $sessionkey = $value;
                    }
                }
            }

            // Set shibboleth session ID for logout
            $SESSION->shibboleth_session_id  = $sessionkey;

            return (strtolower($_SERVER[$this->config->user_attribute]) == strtolower($username));
        } else {
            // If we are not, the user has used the manual login and the login name is
            // unknown, so we return false.
            return false;
        }
    }



    /**
     * Returns the user information for 'external' users. In this case the
     * attributes provided by Shibboleth
     *
     * @return array $result Associative array of user data
     */
    function get_userinfo($username) {
    // reads user information from shibboleth attributes and return it in array()
        global $CFG;

        // Check whether we have got all the essential attributes
        if ( empty($_SERVER[$this->config->user_attribute]) ) {
            print_error( 'shib_not_all_attributes_error', 'auth_shibboleth' , '', "'".$this->config->user_attribute."' ('".$_SERVER[$this->config->user_attribute]."'), '".$this->config->field_map_firstname."' ('".$_SERVER[$this->config->field_map_firstname]."'), '".$this->config->field_map_lastname."' ('".$_SERVER[$this->config->field_map_lastname]."') and '".$this->config->field_map_email."' ('".$_SERVER[$this->config->field_map_email]."')");
        }

        $attrmap = $this->get_attributes();

        $result = array();
        $search_attribs = array();

        foreach ($attrmap as $key=>$value) {
            // Check if attribute is present
            if (!isset($_SERVER[$value])){
                $result[$key] = '';
                continue;
            }

            // Make usename lowercase
            if ($key == 'username'){
                $result[$key] = strtolower($this->get_first_string($_SERVER[$value]));
            } else {
                $result[$key] = $this->get_first_string($_SERVER[$value]);
            }
        }

         // Provide an API to modify the information to fit the Moodle internal
        // data representation
        if (
              $this->config->convert_data
              && $this->config->convert_data != ''
              && is_readable($this->config->convert_data)
            ) {

            // Include a custom file outside the Moodle dir to
            // modify the variable $moodleattributes
            include($this->config->convert_data);
        }

        return $result;
    }

    /**
     * Returns array containg attribute mappings between Moodle and Shibboleth.
     *
     * @return array
     */
    function get_attributes() {
        $configarray = (array) $this->config;

        $moodleattributes = array();
        $userfields = array_merge($this->userfields, $this->get_custom_user_profile_fields());
        foreach ($userfields as $field) {
            if (isset($configarray["field_map_$field"])) {
                $moodleattributes[$field] = $configarray["field_map_$field"];
            }
        }
        $moodleattributes['username'] = $configarray["user_attribute"];

        return $moodleattributes;
    }

    function prevent_local_passwords() {
        return true;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return false;
    }

    /**
     * Whether shibboleth users can change their password or not.
     *
     * Shibboleth auth requires password to be changed on shibboleth server directly.
     * So it is required to have  password change url set.
     *
     * @return bool true if there's a password url or false otherwise.
     */
    function can_change_password() {
        if (!empty($this->config->changepasswordurl)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get password change url.
     *
     * @return moodle_url|null Returns URL to change password or null otherwise.
     */
    function change_password_url() {
        if (!empty($this->config->changepasswordurl)) {
            return new moodle_url($this->config->changepasswordurl);
        } else {
            return null;
        }
    }

     /**
     * Hook for login page
     *
     */
    function loginpage_hook() {
        global $SESSION, $CFG;

        // Prevent username from being shown on login page after logout
        $CFG->nolastloggedin = true;

        return;
    }

     /**
     * Hook for logout page
     *
     */
    function logoutpage_hook() {
        global $SESSION, $redirect;

        // Only do this if logout handler is defined, and if the user is actually logged in via Shibboleth
        $logouthandlervalid = isset($this->config->logout_handler) && !empty($this->config->logout_handler);
        if (isset($SESSION->shibboleth_session_id) && $logouthandlervalid ) {
            // Check if there is an alternative logout return url defined
            if (isset($this->config->logout_return_url) && !empty($this->config->logout_return_url)) {
                // Set temp_redirect to alternative return url
                $temp_redirect = $this->config->logout_return_url;
            } else {
                // Backup old redirect url
                $temp_redirect = $redirect;
            }

            // Overwrite redirect in order to send user to Shibboleth logout page and let him return back
            $redirecturl = new moodle_url($this->config->logout_handler, array('return' => $temp_redirect));
            $redirect = $redirecturl->out();
        }
    }

    /**
     * Cleans and returns first of potential many values (multi-valued attributes)
     *
     * @param string $string Possibly multi-valued attribute from Shibboleth
     */
    function get_first_string($string) {
        $list = explode( ';', $string);
        $clean_string = rtrim($list[0]);

        return $clean_string;
    }

    /**
     * Test if settings are correct, print info to output.
     */
    public function test_settings() {
        global $OUTPUT;

        if (!isset($this->config->user_attribute) || empty($this->config->user_attribute)) {
            echo $OUTPUT->notification(get_string("shib_not_set_up_error", "auth_shibboleth"), 'notifyproblem');
            return;
        }
        if ($this->config->convert_data and $this->config->convert_data != '' and !is_readable($this->config->convert_data)) {
            echo $OUTPUT->notification(get_string("auth_shib_convert_data_warning", "auth_shibboleth"), 'notifyproblem');
            return;
        }
        if (isset($this->config->organization_selection) && empty($this->config->organization_selection) &&
                isset($this->config->alt_login) && $this->config->alt_login == 'on') {

            echo $OUTPUT->notification(get_string("auth_shib_no_organizations_warning", "auth_shibboleth"), 'notifyproblem');
            return;
        }
    }

    /**
     * Return a list of identity providers to display on the login page.
     *
     * @param string $wantsurl The requested URL.
     * @return array List of arrays with keys url, iconurl and name.
     */
    public function loginpage_idp_list($wantsurl) {
        $config = get_config('auth_shibboleth');
        $result = [];

        // Before displaying the button check that Shibboleth is set-up correctly.
        if (empty($config->user_attribute)) {
            return $result;
        }

        $url = new moodle_url('/auth/shibboleth/index.php');
        $iconurl = moodle_url::make_pluginfile_url(context_system::instance()->id,
                                                   'auth_shibboleth',
                                                   'logo',
                                                   null,
                                                   '/',
                                                   $config->auth_logo);
        $result[] = ['url' => $url, 'iconurl' => $iconurl, 'name' => $config->login_name];
        return $result;
    }
}


    /**
     * Sets the standard SAML domain cookie that is also used to preselect
     * the right entry on the local wayf
     *
     * @param IdP identifiere
     */
    function set_saml_cookie($selectedIDP) {
        if (isset($_COOKIE['_saml_idp']))
        {
            $IDPArray = generate_cookie_array($_COOKIE['_saml_idp']);
        }
        else
        {
            $IDPArray = array();
        }
        $IDPArray = appendCookieValue($selectedIDP, $IDPArray);
        setcookie ('_saml_idp', generate_cookie_value($IDPArray), time() + (100*24*3600));
    }

     /**
     * Prints the option elements for the select element of the drop down list
     *
     */
    function print_idp_list(){
        $config = get_config('auth_shibboleth');

        $IdPs = get_idp_list($config->organization_selection);
        if (isset($_COOKIE['_saml_idp'])){
            $idp_cookie = generate_cookie_array($_COOKIE['_saml_idp']);
            do {
                $selectedIdP = array_pop($idp_cookie);
            } while (!isset($IdPs[$selectedIdP]) && count($idp_cookie) > 0);

        } else {
            $selectedIdP = '-';
        }

        foreach($IdPs as $IdP => $data){
            if ($IdP == $selectedIdP){
                echo '<option value="'.$IdP.'" selected="selected">'.$data[0].'</option>';
            } else {
                echo '<option value="'.$IdP.'">'.$data[0].'</option>';
            }
        }
    }


     /**
     * Generate array of IdPs from Moodle Shibboleth settings
     *
     * @param string Text containing tuble/triple of IdP entityId, name and (optionally) session initiator
     * @return array Identifier of IdPs and their name/session initiator
     */

    function get_idp_list($organization_selection) {
        $idp_list = array();

        $idp_raw_list = explode("\n",  $organization_selection);

        foreach ($idp_raw_list as $idp_line){
            $idp_data = explode(',', $idp_line);
            if (isset($idp_data[2]))
            {
                $idp_list[trim($idp_data[0])] = array(trim($idp_data[1]),trim($idp_data[2]));
            }
            elseif(isset($idp_data[1]))
            {
                $idp_list[trim($idp_data[0])] = array(trim($idp_data[1]));
            }
        }

        return $idp_list;
    }

    /**
     * Generates an array of IDPs using the cookie value
     *
     * @param string Value of SAML domain cookie
     * @return array Identifiers of IdPs
     */
    function generate_cookie_array($value) {

        // Decodes and splits cookie value
        $CookieArray = explode(' ', $value);
        $CookieArray = array_map('base64_decode', $CookieArray);

        return $CookieArray;
    }

    /**
     * Generate the value that is stored in the cookie using the list of IDPs
     *
     * @param array IdP identifiers
     * @return string SAML domain cookie value
     */
    function generate_cookie_value($CookieArray) {

        // Merges cookie content and encodes it
        $CookieArray = array_map('base64_encode', $CookieArray);
        $value = implode(' ', $CookieArray);
        return $value;
    }

    /**
     * Append a value to the array of IDPs
     *
     * @param string IdP identifier
     * @param array IdP identifiers
     * @return array IdP identifiers with appended IdP
     */
    function appendCookieValue($value, $CookieArray) {

        array_push($CookieArray, $value);
        $CookieArray = array_reverse($CookieArray);
        $CookieArray = array_unique($CookieArray);
        $CookieArray = array_reverse($CookieArray);

        return $CookieArray;
    }



