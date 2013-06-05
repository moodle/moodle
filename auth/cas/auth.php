<?php

/**
 * @author Martin Dougiamas
 * @author Jerome GUTIERREZ
 * @author Iñaki Arenaza
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

require_once($CFG->dirroot.'/auth/ldap/auth.php');
require_once($CFG->dirroot.'/auth/cas/CAS/CAS.php');

/**
 * CAS authentication plugin.
 */
class auth_plugin_cas extends auth_plugin_ldap {

    /**
     * Constructor.
     */
    function auth_plugin_cas() {
        $this->authtype = 'cas';
        $this->roleauth = 'auth_cas';
        $this->errorlogtag = '[AUTH CAS] ';
        $this->init_plugin($this->authtype);
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
        return phpCAS::isAuthenticated() && (trim(textlib::strtolower(phpCAS::getUser())) == $username);
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

        // Connection to CAS server
        $this->connectCAS();

        if (phpCAS::checkAuthentication()) {
            $frm->username = phpCAS::getUser();
            $frm->password = 'passwdCas';
            return;
        }

        if (isset($_GET['loginguest']) && ($_GET['loginguest'] == true)) {
            $frm->username = 'guest';
            $frm->password = 'guest';
            return;
        }

        if ($this->config->multiauth) {
            $authCAS = optional_param('authCAS', '', PARAM_RAW);
            if ($authCAS == 'NOCAS') {
                return;
            }

            // Show authentication form for multi-authentication
            // test pgtIou parameter for proxy mode (https connection
            // in background from CAS server to the php server)
            if ($authCAS != 'CAS' && !isset($_GET['pgtIou'])) {
                $PAGE->set_url('/auth/cas/auth.php');
                $PAGE->navbar->add($CASform);
                $PAGE->set_title("$site->fullname: $CASform");
                $PAGE->set_heading($site->fullname);
                echo $OUTPUT->header();
                include($CFG->dirroot.'/auth/cas/cas_form.html');
                echo $OUTPUT->footer();
                exit();
            }
        }

        // Force CAS authentication (if needed).
        if (!phpCAS::isAuthenticated()) {
            phpCAS::setLang($this->config->language);
            phpCAS::forceAuthentication();
        }
    }

    /**
     * Logout from the CAS
     *
     */
    function prelogout_hook() {
        global $CFG;

        if (!empty($this->config->logoutcas)) {
            $backurl = $CFG->wwwroot;
            $this->connectCAS();
            phpCAS::logoutWithURL($backurl);
        }
    }

    /**
     * Connect to the CAS (clientcas connection or proxycas connection)
     *
     */
    function connectCAS() {
        global $CFG, $PHPCAS_CLIENT;

        if (!is_object($PHPCAS_CLIENT)) {
            // Make sure phpCAS doesn't try to start a new PHP session when connecting to the CAS server.
            if ($this->config->proxycas) {
                phpCAS::proxy($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri, false);
            } else {
                phpCAS::client($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri, false);
            }
        }

        // If Moodle is configured to use a proxy, phpCAS needs some curl options set.
        if (!empty($CFG->proxyhost) && !is_proxybypass($this->config->hostname)) {
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

        if($this->config->certificate_check && $this->config->certificate_path){
            phpCAS::setCasServerCACert($this->config->certificate_path);
        }else{
            // Don't try to validate the server SSL credentials
            phpCAS::setNoCasServerValidation();
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
        global $CFG, $OUTPUT;

        if (!function_exists('ldap_connect')) { // Is php-ldap really there?
            echo $OUTPUT->notification(get_string('auth_ldap_noextension', 'auth_ldap'));

            // Don't return here, like we do in auth/ldap. We cas use CAS without LDAP.
            // So just warn the user (done above) and define the LDAP constants we use
            // in config.html, to silence the warnings.
            if (!defined('LDAP_DEREF_NEVER')) {
                define ('LDAP_DEREF_NEVER', 0);
            }
            if (!defined('LDAP_DEREF_ALWAYS')) {
            define ('LDAP_DEREF_ALWAYS', 3);
            }
        }

        include($CFG->dirroot.'/auth/cas/config.html');
    }

    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     * @param object object with submitted configuration settings (without system magic quotes)
     * @param array $err array of error messages
     */
    function validate_form($form, &$err) {
        $certificate_path = trim($form->certificate_path);
        if ($form->certificate_check && empty($certificate_path)) {
            $err['certificate_path'] = get_string('auth_cas_certificate_path_empty', 'auth_cas');
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
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {

        // CAS settings
        if (!isset($config->hostname)) {
            $config->hostname = '';
        }
        if (!isset($config->port)) {
            $config->port = '';
        }
        if (!isset($config->casversion)) {
            $config->casversion = '';
        }
        if (!isset($config->baseuri)) {
            $config->baseuri = '';
        }
        if (!isset($config->language)) {
            $config->language = '';
        }
        if (!isset($config->proxycas)) {
            $config->proxycas = '';
        }
        if (!isset($config->logoutcas)) {
            $config->logoutcas = '';
        }
        if (!isset($config->multiauth)) {
            $config->multiauth = '';
        }
        if (!isset($config->certificate_check)) {
            $config->certificate_check = '';
        }
        if (!isset($config->certificate_path)) {
            $config->certificate_path = '';
        }
        if (!isset($config->logout_return_url)) {
            $config->logout_return_url = '';
        }

        // LDAP settings
        if (!isset($config->host_url)) {
            $config->host_url = '';
        }
        if (empty($config->ldapencoding)) {
            $config->ldapencoding = 'utf-8';
        }
        if (!isset($config->pagesize)) {
            $config->pagesize = LDAP_DEFAULT_PAGESIZE;
        }
        if (!isset($config->contexts)) {
            $config->contexts = '';
        }
        if (!isset($config->user_type)) {
            $config->user_type = 'default';
        }
        if (!isset($config->user_attribute)) {
            $config->user_attribute = '';
        }
        if (!isset($config->search_sub)) {
            $config->search_sub = '';
        }
        if (!isset($config->opt_deref)) {
            $config->opt_deref = LDAP_DEREF_NEVER;
        }
        if (!isset($config->bind_dn)) {
            $config->bind_dn = '';
        }
        if (!isset($config->bind_pw)) {
            $config->bind_pw = '';
        }
        if (!isset($config->ldap_version)) {
            $config->ldap_version = '3';
        }
        if (!isset($config->objectclass)) {
            $config->objectclass = '';
        }
        if (!isset($config->memberattribute)) {
            $config->memberattribute = '';
        }

        if (!isset($config->memberattribute_isdn)) {
            $config->memberattribute_isdn = '';
        }
        if (!isset($config->attrcreators)) {
            $config->attrcreators = '';
        }
        if (!isset($config->groupecreators)) {
            $config->groupecreators = '';
        }
        if (!isset($config->removeuser)) {
            $config->removeuser = AUTH_REMOVEUSER_KEEP;
        }

        // save CAS settings
        set_config('hostname', trim($config->hostname), $this->pluginconfig);
        set_config('port', trim($config->port), $this->pluginconfig);
        set_config('casversion', $config->casversion, $this->pluginconfig);
        set_config('baseuri', trim($config->baseuri), $this->pluginconfig);
        set_config('language', $config->language, $this->pluginconfig);
        set_config('proxycas', $config->proxycas, $this->pluginconfig);
        set_config('logoutcas', $config->logoutcas, $this->pluginconfig);
        set_config('multiauth', $config->multiauth, $this->pluginconfig);
        set_config('certificate_check', $config->certificate_check, $this->pluginconfig);
        set_config('certificate_path', $config->certificate_path, $this->pluginconfig);
        set_config('logout_return_url', $config->logout_return_url, $this->pluginconfig);

        // save LDAP settings
        set_config('host_url', trim($config->host_url), $this->pluginconfig);
        set_config('ldapencoding', trim($config->ldapencoding), $this->pluginconfig);
        set_config('pagesize', (int)trim($config->pagesize), $this->pluginconfig);
        set_config('contexts', trim($config->contexts), $this->pluginconfig);
        set_config('user_type', textlib::strtolower(trim($config->user_type)), $this->pluginconfig);
        set_config('user_attribute', textlib::strtolower(trim($config->user_attribute)), $this->pluginconfig);
        set_config('search_sub', $config->search_sub, $this->pluginconfig);
        set_config('opt_deref', $config->opt_deref, $this->pluginconfig);
        set_config('bind_dn', trim($config->bind_dn), $this->pluginconfig);
        set_config('bind_pw', $config->bind_pw, $this->pluginconfig);
        set_config('ldap_version', $config->ldap_version, $this->pluginconfig);
        set_config('objectclass', trim($config->objectclass), $this->pluginconfig);
        set_config('memberattribute', textlib::strtolower(trim($config->memberattribute)), $this->pluginconfig);
        set_config('memberattribute_isdn', $config->memberattribute_isdn, $this->pluginconfig);
        set_config('attrcreators', trim($config->attrcreators), $this->pluginconfig);
        set_config('groupecreators', trim($config->groupecreators), $this->pluginconfig);
        set_config('removeuser', $config->removeuser, $this->pluginconfig);

        return true;
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

        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);

        // Test for group creator
        if (!empty($this->config->groupecreators)) {
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
}
