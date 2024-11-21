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
 * Authentication Plugin: CAS Authentication with attribute release
 *
 * Authentication using CAS (Central Authentication Server) with attributes returned from the CAS server
 *
 * @package auth_casattras
 * @author Adam Franco
 * @copyright 2014 Middlebury College  {@link http://www.middlebury.edu}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/cas/CAS/vendor/autoload.php');
require_once($CFG->dirroot.'/auth/cas/CAS/vendor/apereo/phpcas/source/CAS.php');

/**
 * CAS-Attras authentication plugin.
 *
 * @package auth_casattras
 * @author Adam Franco
 * @copyright 2014 Middlebury College  {@link http://www.middlebury.edu}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_casattras extends auth_plugin_base {

    /** @var boolean Flag to ensure that phpCAS only gets initialized once. */
    protected static $casinitialized = false;

    /**
     * Constructor with initialization.
     */
    public function __construct() {
        $this->authtype = 'casattras';
        $this->errorlogtag = '[AUTH CAS-ATTRAS] ';
        $this->config = get_config('auth_casattras');

        // Verify that the CAS auth plugin is not enabled, disable this plugin (casattras) if so, because they will conflict.
        if (is_enabled_auth('cas') && is_enabled_auth('casattras')) {
            // This code is modeled on that in moodle/admin/auth.php.
            global $CFG;
            get_enabled_auth_plugins(true);
            if (empty($CFG->auth)) {
                $authsenabled = array();
            } else {
                $authsenabled = explode(',', $CFG->auth);
            }
            $key = array_search('casattras', $authsenabled);
            if ($key !== false) {
                unset($authsenabled[$key]);
                set_config('auth', implode(',', $authsenabled));
            }
            if ('casattras' == $CFG->registerauth) {
                set_config('registerauth', '');
            }
            \core\session\manager::gc(); // Remove stale sessions.

            $returnurl = new moodle_url('/admin/settings.php', array('section' => 'manageauths'));
            print_error('casattras_disabled_by_cas', 'auth_casattras', $returnurl, null,
                get_string('casattras_disabled_by_cas', 'auth_casattras'));
        }
    }

    /**
     * Return the properly translated human-friendly title of this auth plugin
     *
     * @todo Document this function
     */
    public function get_title() {
        $title = parent::get_title();
        if (is_enabled_auth('cas')) {
            $title .= ' - '.get_string('cas_conflict_warning', 'auth_casattras');
        }
        return $title;
    }

    /**
     * Returns true if this authentication plugin is "internal".
     *
     * Internal plugins use password hashes from Moodle user table for authentication.
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

    /**
     * Initialize phpCAS configuration.
     *
     */
    protected function init_cas() {
        global $CFG;
        if (self::$casinitialized) {
            return;
        }

        // See MDL-75479
        // Form the base URL of the server with just the protocol and hostname.
        $serverurl = new moodle_url("/");
        $servicebaseurl = $serverurl->get_scheme() ? $serverurl->get_scheme() . "://" : '';
        $servicebaseurl .= $serverurl->get_host();
        // Add the port if set.
        $servicebaseurl .= $serverurl->get_port() ? ':' . $serverurl->get_port() : '';

        // Make sure phpCAS doesn't try to start a new PHP session when connecting to the CAS server.
        if ($this->config->proxycas) {
            phpCAS::proxy(
                constant($this->config->casversion),
                $this->config->hostname,
                (int) $this->config->port,
                $this->config->baseuri,
                $servicebaseurl,
                false);
        } else {
            phpCAS::client(
                constant($this->config->casversion),
                $this->config->hostname,
                (int) $this->config->port,
                $this->config->baseuri,
                $servicebaseurl,
                false);
        }
        self::$casinitialized = true;

        // If Moodle is configured to use a proxy, phpCAS needs some curl options set.
        if (!empty($CFG->proxyhost) && !is_proxybypass($this->config->hostname)) {
            phpCAS::setExtraCurlOption(CURLOPT_PROXY, $CFG->proxyhost);
            if (!empty($CFG->proxyport)) {
                phpCAS::setExtraCurlOption(CURLOPT_PROXYPORT, $CFG->proxyport);
            }
            if (!empty($CFG->proxytype)) {
                // Only set CURLOPT_PROXYTYPE if it's something other than the curl-default http.
                if ($CFG->proxytype == 'SOCKS5') {
                    phpCAS::setExtraCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                }
            }
            if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
                phpCAS::setExtraCurlOption(CURLOPT_PROXYUSERPWD, $CFG->proxyuser.':'.$CFG->proxypassword);
                if (defined('CURLOPT_PROXYAUTH')) {
                    // Any proxy authentication if PHP 5.1.
                    phpCAS::setExtraCurlOption(CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
                }
            }
        }

        if ($this->config->certificatecheck && $this->config->certificatepath) {
            phpCAS::setCasServerCACert($this->config->certificatepath);
        } else {
            // Don't try to validate the server SSL credentials.
            phpCAS::setNoCasServerValidation();
        }
    }

    /**
     * Hook for overriding behaviour of login page.
     * This method is called from login/index.php page for all enabled auth plugins.
     */
    public function loginpage_hook() {
        global $frm;  // Can be used to override submitted login form.
        global $SESSION;

        // Return if CAS enabled and settings are not specified yet.
        if (empty($this->config->hostname)) {
            return;
        }

        // Don't do CAS authentication if the username/password form was submitted.
        $username = optional_param('username', '', PARAM_RAW);
        $ticket = optional_param('ticket', '', PARAM_RAW);
        if (!empty($username)) {
            if (isset($SESSION->wantsurl) && (strstr($SESSION->wantsurl, 'ticket') ||
                                              strstr($SESSION->wantsurl, 'NOCAS'))) {
                unset($SESSION->wantsurl);
            }
            return;
        }

        if ($this->config->multiauth) {
            // If there is an authentication error, stay on the default authentication page.
            if (!empty($SESSION->loginerrormsg)) {
                return;
            }

            $usecas = optional_param('authCASattras', '', PARAM_ALPHA);
            if ($usecas != 'CASattras') {
                return;
            }
        }

        // Configure phpCAS.
        $this->init_cas();

        // If already authenticated.
        if (phpCAS::checkAuthentication()) {
            if (empty($frm)) {
                $frm = new stdClass;
            }
            $frm->username = phpCAS::getUser();
            $frm->password = 'passwdCas';
            $frm->logintoken = \core\session\manager::get_login_token();
            return;
        }

        // Force CAS authentication (if needed).
        if (!phpCAS::isAuthenticated()) {
            phpCAS::forceAuthentication();
        }
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
    public function user_login ($username, $password) {
        $this->init_cas();
        return phpCAS::isAuthenticated() && (trim(core_text::strtolower(phpCAS::getUser())) == $username);
    }

    /**
     * Returns user attribute mappings between Moodle and the CAS server.
     *
     * @return array
     */
    protected function attributes() {
        $moodleattributes = array();
        $customfields = $this->get_custom_user_profile_fields();
        if (!empty($customfields) && !empty($this->userfields)) {
            $userfields = array_merge($this->userfields, $customfields);
        } else {
            $userfields = $this->userfields;
        }
        foreach ($userfields as $field) {
            if (!empty($this->config->{"field_map_$field"})) {
                $moodleattributes[$field] = $this->config->{"field_map_$field"};
            }
        }
        return $moodleattributes;
    }

    /**
     * Read user information from cas server and returns it as array().
     * Function should return all information available. If you are saving
     * this information to moodle user-table you should honour synchronisation flags
     *
     * @param string $username username
     *
     * @return mixed array with no magic quotes or false on error
     */
    public function get_userinfo($username) {
        if (!phpCAS::isAuthenticated() || trim(core_text::strtolower(phpCAS::getUser())) != $username) {
            return array();
        }

        $casattras = phpCAS::getAttributes();
        $moodleattras = array();

        foreach ($this->attributes() as $key => $field) {
            $moodleattras[$key] = $casattras[$field];
        }
        return $moodleattras;
    }

    /**
     * Logout from the CAS
     *
     */
    public function prelogout_hook() {
        global $CFG;

        if (!empty($this->config->logoutcas)) {
            $backurl = $CFG->wwwroot;
            $this->init_cas();
            phpCAS::logoutWithURL($backurl);
        }
    }

    /**
     * Return a list of identity providers to display on the login page.
     *
     * @param string|moodle_url $wantsurl The requested URL.
     * @return array List of arrays with keys url, iconurl and name.
     */
    public function loginpage_idp_list($wantsurl) {
        global $CFG;
        $config = get_config('auth_casattras');
        $params = ["authCASattras" => "CASattras"];
        $url = new moodle_url(get_login_url(), $params);
        $iconurl = moodle_url::make_pluginfile_url(context_system::instance()->id,
                                                   'auth_casattras',
                                                   'logo',
                                                   null,
                                                   '/',
                                                   $config->auth_logo);
        $result[] = ['url' => $url, 'iconurl' => $iconurl, 'name' => $config->auth_name];
        return $result;
    }
}
