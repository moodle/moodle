<?php
/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: CAS Authentication
 *
 * Authentication using CAS (Central Authentication Server).
 *
 * 2006-08-28  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * CAS authentication plugin.
 */
class auth_plugin_cas extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_cas() {
        $this->authtype = 'cas';
        $this->config = get_config('auth/cas');
    }

    /**
     * Authenticates user againt CAS with LDAP.
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        if (! function_exists('ldap_connect')) {
            print_error('auth_casnotinstalled','mnet');
            return false;
        }

        global $CFG;

        // don't allow blank usernames or passwords
        if (!$username or !$password) {
            return false;
        }

        // CAS specific
        if ($CFG->auth == "cas" and !empty($this->config->enabled)) {
            if ($this->config->create_user == '0') {
                if (record_exists('user', 'username', $username)) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return true;
            }
        }

        $ldap_connection = ldap_connect();

        if ($ldap_connection) {
            $ldap_user_dn = auth_ldap_find_userdn($ldap_connection, $username);

            // if ldap_user_dn is empty, user does not exist
            if (!$ldap_user_dn) {
                ldap_close($ldap_connection);
                return false;
            }

            // Try to bind with current username and password
            $ldap_login = ldap_bind($ldap_connection, $ldap_user_dn, $password);
            ldap_close($ldap_connection);
            if ($ldap_login) {
               if ($this->config->create_user=='0') {  //cas specific
                  if (record_exists('user', 'username', $username, 'mnethostid', $CFG->mnet_localhost_id)) {
                    return true;
                  }else{
                    return false;
                  }
               }else{
                  return true;
               }
            }
        } else {
            ldap_close($ldap_connection);
            print_error('auth_cas_cantconnect', 'auth', $CFG->ldap_host_url);
        }
        return false;
    }

    /**
     * Authenticates user against CAS from screen login
     * the user doesn't have a CAS Ticket yet.
     *
     * Returns an object user if the username and password work
     * and nothing if they don't
     *
     * @param string  $username
     * @param string  $password
     *
    */
    function authenticate_user_login ($username, $password) {

        // TODO: fix SOMEOTHER::

        global $CFG;
        // FIX ME: $cas_validate is not global
        $cas_validate = true;
        phpCAS::client($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri);
        phpCAS::setLang($this->config->language);
        phpCAS::forceAuthentication();
        if ($this->config->create_user == '0') {
            if (record_exists('user', 'username', phpCAS::getUser(), 'mnethostid', $CFG->mnet_localhost_id)) {
                // TODO::SOMEOTHER::
                $user = authenticate_user_login(phpCAS::getUser(), 'cas');
            }
            else {
                // login as guest if CAS but not Moodle and not automatic creation
                if ($CFG->guestloginbutton) {
                    // TODO::SOMEOTHER::
                    $user = authenticate_user_login('guest', 'guest');
                }
                else {
                    // TODO::SOMEOTHER::
                    $user = authenticate_user_login(phpCAS::getUser(), 'cas');
                }
            }
        }
        else {
            // TODO::SOMEOTHER::
            $user = authenticate_user_login(phpCAS::getUser(), 'cas');
        }
        return $user;
    }

    /**
     * Authenticates user against CAS when first call of Moodle
     * if already in CAS (cookie with the CAS ticket), don't have to log again (SSO)
     *
     * Returns an object user if the username and password work
     * and nothing if they don't
     *
     * @param object $user
     *
    */
    function automatic_authenticate ($user='') {

        // TODO: fix SOMEOTHER::

        global $CFG;
        // FIX ME: $cas_validate is not global, but it works anyway ;-)
        if (!$cas_validate) {
            $cas_validate = true;
            phpCAS::client($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri);
            phpCAS::setLang($this->config->language);
            $cas_user_exist = phpCAS::checkAuthentication();
            if (!$cas_user_exist and !$CFG->guestloginbutton) {
                $cas_user_exist=phpCAS::forceAuthentication();
            }
            if ($cas_user_exist) {
                if ($this->config->create_user == '0') {
                    if (record_exists('user', 'username', phpCAS::getUser(), 'mnethostid', $CFG->mnet_localhost_id)) {
                        // TODO::SOMEOTHER::
                        $user = authenticate_user_login(phpCAS::getUser(), 'cas');
                    }
                    else {
                        // login as guest if CAS but not Moodle and not automatic creation
                        if ($CFG->guestloginbutton) {
                            // TODO::SOMEOTHER::
                            $user = authenticate_user_login('guest', 'guest');
                        }
                        else {
                            // TODO::SOMEOTHER::
                            $user = authenticate_user_login(phpCAS::getUser(), 'cas');
                        }
                    }
                }
                else {
                    // TODO::SOMEOTHER::
                    $user = authenticate_user_login(phpCAS::getUser(), 'cas');
                }
                return $user;
            }
            else {
                return;
            }
        }
        else {
            return $user;
        }
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
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return !empty($this->config->changepasswordurl);
    }

    function prelogin_hook() {
        // Load alternative login screens if necessary
        // TODO: fix the cas login screen
        return;

        if(!empty($CFG->cas_enabled)) {
            require($CFG->dirroot.'/auth/cas/login.php');
        }
    }


    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include 'config.html';
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return string
     */
    function change_password_url() {
        return $this->config->changepasswordurl;
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset ($config->hostname)) {
            $config->hostname = '';
        }
        if (!isset ($config->port)) {
            $config->port = '';
        }
        if (!isset ($config->casversion)) {
            $config->casversion = '';
        }
        if (!isset ($config->baseuri)) {
            $config->baseuri = '';
        }
        if (!isset ($config->language)) {
            $config->language = '';
        }
        if (!isset ($config->use_cas)) {
            $config->use_cas = '';
        }
        if (!isset ($config->auth_user_create)) {
            $config->auth_user_create = '';
        }
        if (!isset ($config->create_user)) {
            $config->create_user = '0';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save CAS settings
        set_config('hostname',    $config->hostname,    'auth/cas');
        set_config('port',        $config->port,        'auth/cas');
        set_config('casversion',     $config->casversion,     'auth/cas');
        set_config('baseuri',     $config->baseuri,     'auth/cas');
        set_config('language',    $config->language,    'auth/cas');
        set_config('use_cas',     $config->use_cas,     'auth/cas');
        set_config('auth_user_create', $config->auth_user_create, 'auth/cas');
        set_config('create_user', $config->create_user, 'auth/cas');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/cas');

        // save LDAP settings
        // TODO: settings must be separated now that we have multiauth!
        $ldapauth = get_auth_plugin('ldap');
        $ldapauth->process_config($config);

        return true;
    }

}

?>
