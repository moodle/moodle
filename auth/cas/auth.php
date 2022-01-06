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
 * Authentication Plugin: CAS Authentication
 *
 * Authentication using CAS (Central Authentication Server).
 *
 * @author Martin Dougiamas
 * @author Jerome GUTIERREZ
 * @author Iñaki Arenaza
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package auth_cas
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/auth/ldap/auth.php');
require_once($CFG->dirroot.'/auth/cas/CAS/CAS.php');

/**
 * CAS authentication plugin.
 */
class auth_plugin_cas extends auth_plugin_ldap {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'cas';
        $this->roleauth = 'auth_cas';
        $this->errorlogtag = '[AUTH CAS] ';
        $this->init_plugin($this->authtype);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_cas() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    function prevent_local_passwords() {
        return true;
    }

    /**
     * Authenticates user against CAS
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        $this->connectCAS();
        return phpCAS::isAuthenticated() && (trim(core_text::strtolower(phpCAS::getUser())) == $username);
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
        return false;
    }

    /**
     * Authentication choice (CAS or other)
     * Redirection to the CAS form or to login/index.php
     * for other authentication
     */
    function loginpage_hook() {
        global $frm;
        global $CFG;
        global $SESSION, $OUTPUT, $PAGE;

        $site = get_site();
        $CASform = get_string('CASform', 'auth_cas');
        $username = optional_param('username', '', PARAM_RAW);
        $courseid = optional_param('courseid', 0, PARAM_INT);

        if (!empty($username)) {
            if (isset($SESSION->wantsurl) && (strstr($SESSION->wantsurl, 'ticket') ||
                                              strstr($SESSION->wantsurl, 'NOCAS'))) {
                unset($SESSION->wantsurl);
            }
            return;
        }

        // Return if CAS enabled and settings not specified yet
        if (empty($this->config->hostname)) {
            return;
        }

        // If the multi-authentication setting is used, check for the param before connecting to CAS.
        if ($this->config->multiauth) {

            // If there is an authentication error, stay on the default authentication page.
            if (!empty($SESSION->loginerrormsg)) {
                return;
            }

            $authCAS = optional_param('authCAS', '', PARAM_RAW);
            if ($authCAS != 'CAS') {
                return;
            }

        }

        // Connection to CAS server
        $this->connectCAS();

        if (phpCAS::checkAuthentication()) {
            $frm = new stdClass();
            $frm->username = phpCAS::getUser();
            $frm->password = 'passwdCas';
            $frm->logintoken = \core\session\manager::get_login_token();

            // Redirect to a course if multi-auth is activated, authCAS is set to CAS and the courseid is specified.
            if ($this->config->multiauth && !empty($courseid)) {
                redirect(new moodle_url('/course/view.php', array('id'=>$courseid)));
            }

            return;
        }

        if (isset($_GET['loginguest']) && ($_GET['loginguest'] == true)) {
            $frm = new stdClass();
            $frm->username = 'guest';
            $frm->password = 'guest';
            $frm->logintoken = \core\session\manager::get_login_token();
            return;
        }

        // Force CAS authentication (if needed).
        if (!phpCAS::isAuthenticated()) {
            phpCAS::setLang($this->config->language);
            phpCAS::forceAuthentication();
        }
    }


    /**
     * Connect to the CAS (clientcas connection or proxycas connection)
     *
     */
    function connectCAS() {
        global $CFG;
        static $connected = false;

        if (!$connected) {
            // Make sure phpCAS doesn't try to start a new PHP session when connecting to the CAS server.
            if ($this->config->proxycas) {
                phpCAS::proxy($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri, false);
            } else {
                phpCAS::client($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri, false);
            }
            // Some CAS installs require SSLv3 that should be explicitly set.
            if (!empty($this->config->curl_ssl_version)) {
                phpCAS::setExtraCurlOption(CURLOPT_SSLVERSION, $this->config->curl_ssl_version);
            }

            $connected = true;
        }

        // If Moodle is configured to use a proxy, phpCAS needs some curl options set.
        if (!empty($CFG->proxyhost) && !is_proxybypass(phpCAS::getServerLoginURL())) {
            phpCAS::setExtraCurlOption(CURLOPT_PROXY, $CFG->proxyhost);
            if (!empty($CFG->proxyport)) {
                phpCAS::setExtraCurlOption(CURLOPT_PROXYPORT, $CFG->proxyport);
            }
            if (!empty($CFG->proxytype)) {
                // Only set CURLOPT_PROXYTYPE if it's something other than the curl-default http
                if ($CFG->proxytype == 'SOCKS5') {
                    phpCAS::setExtraCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                }
            }
            if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
                phpCAS::setExtraCurlOption(CURLOPT_PROXYUSERPWD, $CFG->proxyuser.':'.$CFG->proxypassword);
                if (defined('CURLOPT_PROXYAUTH')) {
                    // any proxy authentication if PHP 5.1
                    phpCAS::setExtraCurlOption(CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
                }
            }
        }

        if ($this->config->certificate_check && $this->config->certificate_path){
            phpCAS::setCasServerCACert($this->config->certificate_path);
        } else {
            // Don't try to validate the server SSL credentials
            phpCAS::setNoCasServerValidation();
        }
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return null;
    }

    /**
     * Returns true if user should be coursecreator.
     *
     * @param mixed $username    username (without system magic quotes)
     * @return boolean result
     */
    function iscreator($username) {
        if (empty($this->config->host_url) or (empty($this->config->attrcreators) && empty($this->config->groupecreators)) or empty($this->config->memberattribute)) {
            return false;
        }

        $extusername = core_text::convert($username, 'utf-8', $this->config->ldapencoding);

        // Test for group creator
        if (!empty($this->config->groupecreators)) {
            $ldapconnection = $this->ldap_connect();
            if ($this->config->memberattribute_isdn) {
                if(!($userid = $this->ldap_find_userdn($ldapconnection, $extusername))) {
                    return false;
                }
            } else {
                $userid = $extusername;
            }

            $group_dns = explode(';', $this->config->groupecreators);
            if (ldap_isgroupmember($ldapconnection, $userid, $group_dns, $this->config->memberattribute)) {
                return true;
            }
        }

        // Build filter for attrcreator
        if (!empty($this->config->attrcreators)) {
            $attrs = explode(';', $this->config->attrcreators);
            $filter = '(& ('.$this->config->user_attribute."=$username)(|";
            foreach ($attrs as $attr){
                if(strpos($attr, '=')) {
                    $filter .= "($attr)";
                } else {
                    $filter .= '('.$this->config->memberattribute."=$attr)";
                }
            }
            $filter .= '))';

            // Search
            $result = $this->ldap_get_userlist($filter);
            if (count($result) != 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reads user information from LDAP and returns it as array()
     *
     * If no LDAP servers are configured, user information has to be
     * provided via other methods (CSV file, manually, etc.). Return
     * an empty array so existing user info is not lost. Otherwise,
     * calls parent class method to get user info.
     *
     * @param string $username username
     * @return mixed array with no magic quotes or false on error
     */
    function get_userinfo($username) {
        if (empty($this->config->host_url)) {
            return array();
        }
        return parent::get_userinfo($username);
    }

    /**
     * Syncronizes users from LDAP server to moodle user table.
     *
     * If no LDAP servers are configured, simply return. Otherwise,
     * call parent class method to do the work.
     *
     * @param bool $do_updates will do pull in data updates from LDAP if relevant
     * @return nothing
     */
    function sync_users($do_updates=true) {
        if (empty($this->config->host_url)) {
            error_log('[AUTH CAS] '.get_string('noldapserver', 'auth_cas'));
            return;
        }
        parent::sync_users($do_updates);
    }

    /**
    * Hook for logout page
    */
    function logoutpage_hook() {
        global $USER, $redirect;

        // Only do this if the user is actually logged in via CAS
        if ($USER->auth === $this->authtype) {
            // Check if there is an alternative logout return url defined
            if (isset($this->config->logout_return_url) && !empty($this->config->logout_return_url)) {
                // Set redirect to alternative return url
                $redirect = $this->config->logout_return_url;
            }
        }
    }

    /**
     * Post logout hook.
     *
     * Note: this method replace the prelogout_hook method to avoid redirect to CAS logout
     * before the event userlogout being triggered.
     *
     * @param stdClass $user clone of USER object object before the user session was terminated
     */
    public function postlogout_hook($user) {
        global $CFG;
        // Only redirect to CAS logout if the user is logged as a CAS user.
        if (!empty($this->config->logoutcas) && $user->auth == $this->authtype) {
            $backurl = !empty($this->config->logout_return_url) ? $this->config->logout_return_url : $CFG->wwwroot;
            $this->connectCAS();
            phpCAS::logoutWithRedirectService($backurl);
        }
    }

    /**
     * Return a list of identity providers to display on the login page.
     *
     * @param string|moodle_url $wantsurl The requested URL.
     * @return array List of arrays with keys url, iconurl and name.
     */
    public function loginpage_idp_list($wantsurl) {
        if (empty($this->config->hostname)) {
            // CAS is not configured.
            return [];
        }

        if ($this->config->auth_logo) {
            $iconurl = moodle_url::make_pluginfile_url(
                context_system::instance()->id,
                'auth_cas',
                'logo',
                null,
                null,
                $this->config->auth_logo);
        } else {
            $iconurl = null;
        }

        return [
            [
                'url' => new moodle_url(get_login_url(), [
                        'authCAS' => 'CAS',
                    ]),
                'iconurl' => $iconurl,
                'name' => format_string($this->config->auth_name),
            ],
        ];
    }
}
