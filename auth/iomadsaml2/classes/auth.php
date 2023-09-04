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
 * Authenticate using an embeded SimpleSamlPhp instance
 *
 * @package   auth_iomadsaml2
 * @copyright Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use pix_icon;
use auth_iomadsaml2\admin\iomadsaml2_settings;
use coding_exception;
use core\output\notification;
use dml_exception;
use Exception;
use moodle_exception;
use stdClass;
use iomad;
use company;
use context_system;

global $CFG;
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/login/lib.php');
require_once(__DIR__.'/../locallib.php');

/**
 * Plugin for Saml2 authentication.
 *
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth extends \auth_plugin_base {
    /**
     * @var array $metadataentities List of configured active IdPs.
     */
    public $metadataentities = [];

    /**
     * @var bool $multiidp Indicates if we have more than one active IdP.
     */
    private $multiidp = false;

    /**
     * @var stdClass $defaultidp.
     */
    private $defaultidp;

    /**
     * @var array $defaults The config defaults
     */
    public $defaults = [
        'idpname'            => '',
        'idpdefaultname'     => '', // Set in constructor.
        'idpmetadata'        => '',
        'debug'              => 0,
        'duallogin'          => iomadsaml2_settings::OPTION_DUAL_LOGIN_YES,
        'autologin'          => iomadsaml2_settings::OPTION_AUTO_LOGIN_NO,
        'autologincookie'    => '',
        'anyauth'            => 1,
        'idpattr'            => 'uid',
        'mdlattr'            => 'username',
        'tolower'            => iomadsaml2_settings::OPTION_TOLOWER_EXACT,
        'autocreate'         => 0,
        'spmetadatasign'     => true,
        'showidplink'        => true,
        'alterlogout'        => '',
        'idpmetadatarefresh' => 0,
        'logtofile'          => 0,
        'logdir'             => '/tmp/',
        'nameidasattrib'     => 0,
        'flagresponsetype'   => iomadsaml2_settings::OPTION_FLAGGED_LOGIN_MESSAGE,
        'flagredirecturl'    => '',
        'flagmessage'        => '' // Set in constructor.
    ];

    /**
     * Constructor.
     */
    public function __construct() {
        global $CFG, $DB;

        // Add username field to the list of data mapping to be able to update it on user creation if required.
        if (!in_array('username', $this->userfields)) {
            array_unshift($this->userfields, "username");
        }

        $this->defaults['idpdefaultname'] = get_string('idpnamedefault', 'auth_iomadsaml2');
        $this->defaults['flagmessage'] = get_string('flagmessage_default', 'auth_iomadsaml2');
        $this->authtype = 'iomadsaml2';

        $baseurl = optional_param('baseurl', $CFG->wwwroot, PARAM_URL);
        $mdl = new moodle_url($baseurl);
        $this->spname = $mdl->get_host();
        $this->certpem = $this->get_file("{$this->spname}.pem");
        $this->certcrt = $this->get_file("{$this->spname}.crt");

        $companyid = iomad::get_my_companyid(context_system::instance(), false);
        $postfix = '';
        if (!empty($companyid)) {
            $postfix = "_$companyid";
        }

        $fullconfig = (array) get_config('auth_iomadsaml2');
        $myconfig = array_merge($this->defaults, $fullconfig );
        // Do we have anything company specific?
        if (!empty($companyid)) {
            foreach ($this->defaults as $defaultidetifier => $ignore) {
                if (!empty($fullconfig[$defaultidetifier . $postfix])) {
                    $myconfig[$defaultidetifier] = $fullconfig[$defaultidetifier . $postfix];
                }
            }
        }

        // Convert it to an object as that's what they expect.
        $this->config = (object) $myconfig;

        // Parsed IdP metadata, either a list of IdP metadata urls or a single XML blob.
        $parser = new idp_parser();
        $this->metadatalist = $parser->parse($this->config->idpmetadata);

        // Fetch active entitiyIDs provided by the metadata and populate metadataentities list.
        $idpentities = $DB->get_records('auth_iomadsaml2_idps', ['activeidp' => 1, 'companyid' => $companyid]);
        foreach ($idpentities as $idpentity) {
            // Set name.
            $idpentity->name = empty($idpentity->displayname) ? $idpentity->defaultname : $idpentity->displayname;
            $idpentity->md5entityid = md5($idpentity->entityid);
            // Set default IdP if we found one.
            if ((bool) $idpentity->defaultidp && !isset($this->defaultidp)) {
                $this->defaultidp = $idpentity;
            }
            $this->metadataentities[$idpentity->md5entityid] = $idpentity;
        }

        // Check if we have mutiple IdPs configured.
        // If we have mutliple metadata entries set multiidp to true.
        $this->multiidp = (count($this->metadataentities) > 1);
    }

    /**
     * If debug mode enabled for plugin.
     *
     * @return bool
     */
    public function is_debugging() {
        return (bool) $this->config->debug;
    }

    /**
     * Get iomadsaml2 directory.
     *
     * @return string
     */
    public function get_iomadsaml2_directory() {
        global $CFG;
        $directory = "{$CFG->dataroot}/iomadsaml2";
        if (!file_exists($directory)) {
            mkdir($directory);
        }
        return $directory;
    }

    /**
     * Get file.
     *
     * @param string $file
     * @return string
     */
    public function get_file($file) {
        return $this->get_iomadsaml2_directory() . '/' . $file;
    }

    /**
     * Get metadata file.
     *
     * @return string
     */
    public function get_file_sp_metadata_file() {
        return $this->get_file($this->spname . '.xml');
    }

    /**
     * Get idp Metadata file.
     *
     * @param string|array $url The string with the URL or an array with all URLs as keys.
     * @return string Metadata file path.
     */
    public function get_file_idp_metadata_file($url) {
        if (is_object($url)) {
            $url = (array)$url;
        }

        if (is_array($url)) {
            $url = array_keys($url);
            $url = implode("\n", $url);
        }

        $filename = md5($url) . '.idp.xml';
        return $this->get_file($filename);
    }

    /**
     * A debug function, dumps to the php log
     *
     * @param string $msg Log message
     */
    private function log($msg) {
        if ($this->is_debugging()) {
            // @codingStandardsIgnoreLine
            error_log('auth_iomadsaml2: ' . $msg);

            // If SSP logs to tmp file we want these to also go there.
            if ($this->config->logtofile) {
                require_once(__DIR__.'/../setup.php');
                \SimpleSAML\Logger::debug('auth_iomadsaml2: ' . $msg);
            }
        }
    }

    /**
     * Returns a list of potential IdPs that this authentication plugin supports.
     * This is used to provide links on the login page.
     *
     * @param string $wantsurl the relative url fragment the user wants to get to.
     *
     * @return array of IdP's
     */
    public function loginpage_idp_list($wantsurl) {
        $conf = $this->config;

        // If we have disabled the visibility of the idp link, return with an empty array right away.
        if (!$conf->showidplink) {
            return [];
        }

        // If the plugin has not been configured then do not return an IdP link.
        if ($this->is_configured() === false) {
            return [];
        }

        // The array of IdPs to return.
        $idplist = [];

        // Create IdP metadata url => name mapping.
        $idpurls = array_combine(array_column($this->metadatalist, 'idpurl'), array_column($this->metadatalist, 'idpname'));
        foreach ($this->metadataentities as $idp) {
            // Check for unlikely case that entity metadataurl is no longer in configuration.
            if (!array_key_exists($idp->metadataurl, $idpurls)) {
                debugging("Missing IdP metadata configuration for '{$idp->metadataurl}'");
                continue;
            }

            // Moodle Workplace - Check IdP's tenant availability.
            // Check if function exists required for Totara 12 compatibility.
            if (class_exists(\tool_tenant\local\auth\iomadsaml2\manager::class) && !component_class_callback('\tool_tenant\local\auth\iomadsaml2\manager',
                    'issuer_available', [$idp->md5entityid], true)) {
                continue;
            }

            // The wants url may already be routed via login.php so don't re-re-route it.
            if (strpos($wantsurl, '/auth/iomadsaml2/login.php') !== false) {
                $idpurl = new moodle_url($wantsurl);
            } else {
                $idpurl = new moodle_url('/auth/iomadsaml2/login.php', ['wants' => $wantsurl, 'idp' => $idp->md5entityid]);
            }
            $idpurl->param('passive', 'off');

            // A default icon.
            $idpiconurl = null;
            $idpicon = null;
            if (!empty($idp->logo)) {
                $idpiconurl = new moodle_url($idp->logo);
            } else {
                $idpicon = new pix_icon('i/user', 'Login');
            }

            // Initially use the default name. This is suitable for a single IdP.
            $idpname = $conf->idpdefaultname;

            // When multiple IdPs are configured, use a different default based on the IdP.
            if ($this->multiidp) {
                $host = parse_url($idp->entityid, PHP_URL_HOST);
                $idpname = get_string('idpnamedefault_varaible', 'auth_iomadsaml2', $host);
            }

            // Use a forced override set in the idpmetadata field.
            if (!empty($idpurls[$idp->metadataurl])) {
                $idpname = $idpurls[$idp->metadataurl];
            }

            // Try to use the <mdui:DisplayName> if it exists.
            if (!empty($idp->name)) {
                $idpname = $idp->name;
            }

            // Has the IdP label override been set in the admin configuration?
            // This is best used with a single IdP. Multiple IdP overrides are different.
            if (!empty($conf->idpname)) {
                $idpname = $conf->idpname;
            }

            $idplist[] = [
                'url'  => $idpurl,
                'icon' => $idpicon,
                'iconurl' => $idpiconurl,
                'name' => $idpname,
            ];
        }

        return $idplist;
    }

    /**
     * We don't manage passwords internally.
     *
     * @return bool Always false
     */
    public function is_internal() {
        return false;
    }

    /**
     * Checks to see if the plugin has been configured and the IdP/SP metadata files exist.
     *
     * @return bool
     */
    public function is_configured() {
        $file = $this->certcrt;
        if (!file_exists($file)) {
            $this->log(__FUNCTION__ . ' file not found, ' . $file);
            return false;
        }

        $file = $this->certpem;
        if (!file_exists($file)) {
            $this->log(__FUNCTION__ . ' file not found, ' . $file);
            return false;
        }

        // Requires at least one active IdP to work.
        if (!count($this->metadataentities)) {
            $this->log(__FUNCTION__ . ' no active IdPs');
            return false;
        }

        foreach ($this->metadataentities as $idpentity) {
            $file = $this->get_file_idp_metadata_file($idpentity->metadataurl);
            if (!file_exists($file)) {
                $this->log(__FUNCTION__ . ' file not found, ' . $file);
                return false;
            }
        }

        return true;
    }

    /**
     * Shows an error page for various authentication issues.
     *
     * @param string $msg The error message.
     */
    public function error_page($msg) {
        global $PAGE, $OUTPUT, $SESSION;

        // Clean up $SESSION->wantsurl that was set explicitly in {@see auth_iomadsaml2\login},
        // we don't go anywhere.
        unset($SESSION->wantsurl);

        $PAGE->set_context(\context_system::instance());
        $PAGE->set_url('/auth/iomadsaml2/error.php');
        $PAGE->set_title(get_string('error', 'auth_iomadsaml2'));
        $PAGE->set_heading(get_string('error', 'auth_iomadsaml2'));
        echo $OUTPUT->header();
        echo $OUTPUT->box($msg, 'generalbox', 'notice');
        $logouturl = new moodle_url('/auth/iomadsaml2/logout.php');
        echo $OUTPUT->single_button($logouturl, get_string('logout'), 'get');
        echo $OUTPUT->footer();
        exit(1);
    }

    /**
     * All the checking happens before the login page in this hook
     */
    public function pre_loginpage_hook() {

        global $SESSION;

        $this->log(__FUNCTION__ . ' enter');

        // If we previously tried to force saml on, but then navigated
        // away, and come in from another deep link while dual auth is
        // on, then reset the previous session memory of forcing SAML.
        if (isset($SESSION->saml)) {
            $this->log(__FUNCTION__ . ' unset $SESSION->saml');
            unset($SESSION->saml);
        }

        $this->loginpage_hook();
        $this->log(__FUNCTION__ . ' exit');
    }

    /**
     * All the checking happens before the login page in this hook
     */
    public function loginpage_hook() {
        global $SESSION;

        $this->execute_callback('auth_iomadsaml2_loginpage_hook');

        $this->log(__FUNCTION__ . ' enter');

        // For Behat tests, clear the wantsurl if it has ended up pointing to the fixture. This
        // happens in older browsers which don't support the Referrer-Policy header used by fixture.
        if (defined('BEHAT_SITE_RUNNING') && !empty($SESSION->wantsurl) &&
                strpos($SESSION->wantsurl, '/auth/iomadsaml2/tests/fixtures/') !== false) {
            unset($SESSION->wantsurl);
        }

        // If the plugin has not been configured then do NOT try to use iomadsaml2.
        if ($this->is_configured() === false) {
            return;
        }

        $redirect = $this->should_login_redirect();
        if (is_string($redirect)) {
            redirect($redirect);
        } else if ($redirect === true) {
            $this->saml_login();
        } else {
            $this->log(__FUNCTION__ . ' exit');
            return;
        }

    }

    /**
     * Determines if we will redirect to the SAML login.
     *
     * @return bool|string If this returns true then we redirect to the SAML login.
     */
    public function should_login_redirect() {
        global $SESSION;

        $this->log(__FUNCTION__ . ' enter');

        $saml = optional_param('saml', null, PARAM_BOOL);
        $multiidp = optional_param('multiidp', false, PARAM_BOOL);
        // Also support noredirect param - used by other auth plugins.
        $noredirect = optional_param('noredirect', 0, PARAM_BOOL);
        if (!empty($noredirect)) {
            $saml = 0;
        }

        // Never redirect on POST.
        if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $this->log(__FUNCTION__ . ' skipping due to method=post');
            return false;
        }

        // Never redirect if requested so.
        if ($saml === 0) {
            $SESSION->saml = $saml;
            $this->log(__FUNCTION__ . ' skipping due to saml=off parameter');
            return false;
        }

        if ($this->check_whitelisted_ip_redirect()) {
            $this->log(__FUNCTION__ . ' redirecting due to ip found in idp whitelist');
            return true;
        }

        // Redirect to the select IdP page if requested so.
        if ($multiidp) {
            $this->log(__FUNCTION__ . ' redirecting due to multiidp=on parameter');
            $idpurl = new moodle_url('/auth/iomadsaml2/selectidp.php');
            return $idpurl->out();
        }

        // Never redirect if has error.
        if (!empty($_GET['SimpleSAML_Auth_State_exceptionId'])) {
            $this->log(__FUNCTION__ . ' skipping due to SimpleSAML_Auth_State_exceptionId');
            return false;
        }

        // If dual auth then stop and show login page.
        if ($this->config->duallogin == iomadsaml2_settings::OPTION_DUAL_LOGIN_YES && $saml == 0) {
            $this->log(__FUNCTION__ . ' skipping due to dual auth');
            return false;
        }

        if ($this->config->duallogin == iomadsaml2_settings::OPTION_DUAL_LOGIN_TEST && $saml == 0) {
            $this->log(__FUNCTION__ . ' skipping to test connectivity first');
            // Inject JS to test connectivity to the login endpoint. Some networks may not be aware of the IdP.
            global $PAGE, $ME;
            $PAGE->requires->js_call_amd('auth_iomadsaml2/connectivity_test', 'init', [
                $this->config->testendpoint,
                (new moodle_url($ME, ['saml' => 'on']))->out(),
            ]);
            return false;
        }

        // If ?saml=on even when duallogin is on, go directly to IdP.
        if ($saml == 1) {
            $this->log(__FUNCTION__ . ' redirecting due to query param ?saml=on');
            return true;
        }

        // Check whether we've skipped saml already.
        // This is here because loginpage_hook is called again during form
        // submission (all of login.php is processed) and ?saml=off is not
        // preserved forcing us to the IdP.
        //
        // This isn't needed when duallogin is on because $saml will default to 0
        // and duallogin is not part of the request.
        if ((isset($SESSION->saml) && $SESSION->saml == 0) && $this->config->duallogin == iomadsaml2_settings::OPTION_DUAL_LOGIN_NO) {
            $this->log(__FUNCTION__ . ' skipping due to no sso session');
            return false;
        }

        // If passive mode always redirect, except if saml=off. It will redirect back to login page.
        // The second time around saml=0 will be set in the session.
        if ($this->config->duallogin == iomadsaml2_settings::OPTION_DUAL_LOGIN_PASSIVE) {
            $this->log(__FUNCTION__ . ' redirecting due to passive mode.');
            return true;
        }

        // If ?saml=off even when duallogin is off, then always show the login page.
        // Additionally store this in the session so if the password fails we get
        // the login page again, and don't get booted to the IdP on the second
        // attempt to login manually.
        $saml = optional_param('saml', 1, PARAM_BOOL);
        $noredirect = optional_param('noredirect', 0, PARAM_BOOL);
        if (!empty($noredirect)) {
            $saml = 0;
        }

        if ($saml == 0) {
            $SESSION->saml = $saml;
            $this->log(__FUNCTION__ . ' skipping due to ?saml=off');
            return false;
        }

        // We are off to SAML land so reset the force in SESSION.
        if (isset($SESSION->saml)) {
            $this->log(__FUNCTION__ . ' unset SESSION->saml');
            unset($SESSION->saml);
        }

        return true;
    }

    /**
     * All the checking happens before the login page in this hook
     */
    public function saml_login() {
        global $CFG, $SESSION;

        require_once(__DIR__.'/../setup.php');
        require_once("$CFG->dirroot/login/lib.php");

        // Set the default IdP to be the first in the list. Used when dual login is disabled.
        $SESSION->iomadsaml2idp = reset($this->metadataentities)->md5entityid;

        // We store the IdP in the session to generate the config/config.php array with the default local SP.
        $idpalias = optional_param('idpalias', '', PARAM_TEXT);
        if (!empty($idpalias)) {
            $idpfound = false;

            foreach ($this->metadataentities as $idpentity) {
                if ($idpalias == $idpentity->alias) {
                    $SESSION->iomadsaml2idp = $idpentity->md5entityid;
                    $idpfound = true;
                    break;
                }
            }

            if (!$idpfound) {
                $this->error_page(get_string('noidpfound', 'auth_iomadsaml2', $idpalias));
            }
        } else if (isset($_GET['idp'])) {
            $SESSION->iomadsaml2idp = $_GET['idp'];
        } else if (!is_null($this->defaultidp)) {
            $SESSION->iomadsaml2idp = $this->defaultidp->md5entityid;
        } else if ($this->multiidp) {
            // At this stage there is no alias, get-param or default IdP configured.
            // On a multi-idp system, now check for any whitelisted IP address redirection.
            $entitiyid = $this->check_whitelisted_ip_redirect();
            if ($entitiyid !== null) {
                $SESSION->iomadsaml2idp = $entitiyid;
            } else {
                $idpurl = new moodle_url('/auth/iomadsaml2/selectidp.php');
                redirect($idpurl);
            }
        }

        if (isset($_GET['rememberidp']) && $_GET['rememberidp'] == 1) {
            $this->set_idp_cookie($SESSION->iomadsaml2idp);
        }

        // Configure passive authentication.
        $passive = $this->config->duallogin == iomadsaml2_settings::OPTION_DUAL_LOGIN_PASSIVE;
        $passive = (bool)optional_param('passive', $passive, PARAM_BOOL);
        $params = ['isPassive' => $passive];
        if ($passive) {
            $params['ErrorURL'] = (new moodle_url('/login/index.php', ['saml' => 0]))->out(false);
        }
        $params['AllowCreate'] = $this->config->allowcreate == 1;

        $auth = new \SimpleSAML\Auth\Simple($this->spname);
        // Redirect to IdP login page for authentication.
        $auth->requireAuth($params);

        // Complete login process.
        $attributes = $auth->getAttributes();
        $this->saml_login_complete($attributes);
    }


    /**
     * The user has done the SAML handshake now we can log them in
     *
     * This is split so we can handle SP and IdP first login flows.
     *
     * @param array $attributes
     */
    public function saml_login_complete($attributes) {
        global $CFG, $USER, $SESSION;

        if ($this->config->attrsimple) {
            $attributes = $this->simplify_attr($attributes);
        }

        $attr = $this->config->idpattr;
        if (empty($attributes[$attr])) {
            // Missing mapping IdP attribute. Login failed.
            $event = \core\event\user_login_failed::create(['other' => ['username' => 'unknown',
                'reason' => AUTH_LOGIN_NOUSER]]);
            $event->trigger();
            $this->error_page(get_string('noattribute', 'auth_iomadsaml2', $attr));
        }

        // Testing user's groups and allow access according to preferences.
        if (!$this->is_access_allowed_for_member($attributes)) {
            $event = \core\event\user_login_failed::create(['other' => ['username' => 'unknown',
                'reason' => AUTH_LOGIN_UNAUTHORISED]]);
            $event->trigger();
            $this->handle_blocked_access();
        }

        // Find Moodle user.
        $user = false;
        foreach ($attributes[$attr] as $uid) {
            $insensitive = false;
            $accentsensitive = true;
            if ($this->config->tolower == iomadsaml2_settings::OPTION_TOLOWER_LOWER_CASE) {
                $this->log(__FUNCTION__ . " to lowercase for $uid");
                $uid = strtolower($uid);
            }
            if ($this->config->tolower == iomadsaml2_settings::OPTION_TOLOWER_CASE_INSENSITIVE) {
                $this->log(__FUNCTION__ . " case insensitive compare for $uid");
                $insensitive = true;
            }
            if ($this->config->tolower == iomadsaml2_settings::OPTION_TOLOWER_CASE_AND_ACCENT_INSENSITIVE) {
                $this->log(__FUNCTION__ . " case and accent insensitive compare for $uid");
                $insensitive = true;
                $accentsensitive = false;
            }
            if ($user = user_extractor::get_user($this->config->mdlattr, $uid, $insensitive, $accentsensitive)) {
                // We found a user.
                break;
            }
        }

        // Moodle Workplace - Check IdP's tenant availability, for new user pre-allocate to tenant.
        // Check if function exists required for Totara 12 compatibility.
        if (class_exists(\tool_tenant\local\auth\iomadsaml2\manager::class)) {
            component_class_callback('\tool_tenant\local\auth\iomadsaml2\manager', 'complete_login_hook',
                [$SESSION->iomadsaml2idp ?? '', $uid, $user]);
        }

        $newuser = false;
        if (!$user) {
            // No existing user.
            if ($this->config->autocreate) {
                $email = $this->get_email_from_attributes($attributes);
                // If can't have accounts with the same emails, check if email is taken before create a new user.
                if (empty($CFG->allowaccountssameemail) && $this->is_email_taken($email)) {
                    $event = \core\event\user_login_failed::create(['other' => ['username' => $uid,
                        'reason' => AUTH_LOGIN_FAILED]]);
                    $event->trigger();

                    $this->log(__FUNCTION__ . " user '$uid' can't be autocreated as email '$email' is taken");
                    $this->error_page(get_string('emailtaken', 'auth_iomadsaml2', $email));
                }

                // Honor the core allowemailaddresses setting.
                if ($error = email_is_not_allowed($email)) {
                    $event = \core\event\user_login_failed::create(['other' => ['username' => $uid,
                        'reason' => AUTH_LOGIN_FAILED]]);
                    $event->trigger();

                    $this->log(__FUNCTION__ . " '$email' " . $error);
                    $this->handle_blocked_access();
                }

                // Generate the users information from the attribute map.
                $user = new stdClass();
                $this->update_user_record_from_attribute_map($user, $attributes, true);
                if (empty($user->username)) {
                    // Just in case username field not set, use uid.
                    $user->username = strtolower($uid);
                }
                // Set the auth to iomadsaml2 if it's not set from the attributes.
                if (empty($user->auth)) {
                    $user->auth = 'iomadsaml2';
                }

                $this->log(__FUNCTION__ . " user '$user->username' is not in moodle so autocreating");
                require_once($CFG->dirroot.'/user/lib.php');

                // Various values that user_create_user doesn't validate or set.
                $user->confirmed = 1;
                $user->lastip = getremoteaddr();
                $user->timecreated = time();
                $user->timemodified = $user->timecreated;
                $user->mnethostid = $CFG->mnet_localhost_id;

                $user->id = \user_create_user($user, true, true);
                $newuser = true;
                // Store any custom profile fields.
                profile_save_data($user);
                // Make sure all user data is fetched.
                $user = \core_user::get_user($user->id);
            } else {
                // Moodle user does not exist and settings prevent creating new accounts.
                $event = \core\event\user_login_failed::create(['other' => ['username' => $uid,
                    'reason' => AUTH_LOGIN_NOUSER]]);
                $event->trigger();
                $this->log(__FUNCTION__ . " user '$uid' is not in moodle so error");
                $this->error_page(get_string('nouser', 'auth_iomadsaml2', $uid));
            }
        } else {
            // Prevent access to users who are suspended.
            if ($user->suspended) {
                $event = \core\event\user_login_failed::create([
                    'userid' => $user->id,
                    'other' => [
                        'username' => $user->username,
                        'reason' => AUTH_LOGIN_SUSPENDED,
                    ]
                ]);
                $event->trigger();

                $this->error_page(get_string('suspendeduser', 'auth_iomadsaml2', $uid));
            }

            $this->log(__FUNCTION__ . ' found user '.$user->username);
        }

        if (!$this->config->anyauth && $user->auth != 'iomadsaml2') {
            $event = \core\event\user_login_failed::create([
                'userid' => $user->id,
                'other' => [
                    'username' => $user->username,
                    'reason' => AUTH_LOGIN_UNAUTHORISED,
                ]
            ]);
            $event->trigger();

            $this->log(__FUNCTION__ . " user $uid is auth type: $user->auth");
            $this->error_page(get_string('wrongauth', 'auth_iomadsaml2', $uid));
        }

        if ($this->config->anyauth && !is_enabled_auth($user->auth)) {
            $event = \core\event\user_login_failed::create([
                'userid' => $user->id,
                'other' => [
                    'username' => $user->username,
                    'reason' => AUTH_LOGIN_UNAUTHORISED,
                ]
            ]);
            $event->trigger();

            $this->log(__FUNCTION__ . " user $uid's auth type: $user->auth is not enabled");
            $this->error_page(get_string('anyauthotherdisabled', 'auth_iomadsaml2', [
                'username' => $uid, 'auth' => $user->auth,
            ]));
        }

        // Do we need to update any user fields? Unlike ldap, we can only do
        // this now. We cannot query the IdP at any time.
        $this->update_user_profile_fields($user, $attributes, $newuser);

        // If admin has been set for this IdP we make the user an admin.
        if (!empty($SESSION->iomadsaml2idp) && $this->metadataentities[$SESSION->iomadsaml2idp]->adminidp) {
            $admins = explode(',', $CFG->siteadmins);
            if (!in_array($user->id, $admins)) {
                $admins[] = $user->id;
            }
            set_config('siteadmins', implode(',', $admins));
        }

        // Make sure all user data is fetched.
        $user = get_complete_user_data('username', $user->username, null, false);
        complete_user_login($user);
        $USER->loggedin = true;
        $USER->site = $CFG->wwwroot;
        set_moodle_cookie($USER->username);

        $wantsurl = core_login_get_return_url();
        // If we are not on the page we want, then redirect to it (unless this is CLI).
        if ( qualified_me() !== false && qualified_me() !== $wantsurl ) {
            $this->log(__FUNCTION__ . " redirecting to $wantsurl");
            unset($SESSION->wantsurl);
            redirect($wantsurl);
            exit;
        } else {
            $this->log(__FUNCTION__ . " continuing onto " . qualified_me() );
        }

        return;
    }

    /**
     * Redirect IOMAD SAML2 login if a flagredirecturl has been configured.
     *
     * @throws \moodle_exception
     */
    protected function redirect_blocked_access() {

        if (!empty($this->config->flagredirecturl)) {
            redirect(new moodle_url($this->config->flagredirecturl));
        } else {
            $this->log(__FUNCTION__ . ' no redirect URL value set.');
            // Fallback to flag message if redirect URL not set.
            $this->error_page($this->config->flagmessage);
        }
    }

    /**
     * Handles blocked access based on configuration.
     */
    protected function handle_blocked_access() {
        switch ($this->config->flagresponsetype) {
            case iomadsaml2_settings::OPTION_FLAGGED_LOGIN_REDIRECT :
                $this->redirect_blocked_access();
                break;
            case iomadsaml2_settings::OPTION_FLAGGED_LOGIN_MESSAGE :
            default :
                $this->error_page($this->config->flagmessage);
                break;
        }
    }

    /**
     * Checks configuration of the multiple IdP IP whitelist field. If the users IP matches, this will
     * return the md5 hash of IdP entityid on true. Or false if not found.
     *
     * This is used in two places, firstly to determine if a saml redirect is to happen.
     * Secondly to determine which IdP to force the redirect to.
     *
     * @return bool|string
     */
    protected function check_whitelisted_ip_redirect() {
        foreach ($this->metadataentities as $idpentity) {
            if (\core\ip_utils::is_ip_in_subnet_list(getremoteaddr(), $idpentity->whitelist)) {
                return $idpentity->md5entityid;
            }
        }
        return false;
    }

    /**
     * Testing user's groups attribute and allow access decided on preferences.
     *
     * @param array $attributes A list of attributes from the request
     * @return bool
     */
    public function is_access_allowed_for_member($attributes) {

        // If there is no encumberance attribute configured in Moodle, let them pass.
        if (empty($this->config->grouprules) ) {
            return true;
        }

        $uid = $attributes[$this->config->idpattr][0];
        $rules = group_rule::get_list($this->config->grouprules);
        $userhasgroups = false;

        foreach ($rules as $rule) {
            if (empty($attributes[$rule->get_attribute()])) {
                continue;
            }

            $userhasgroups = true; // At least one encumberance attribute is detected.

            foreach ($attributes[$rule->get_attribute()] as $group) {
                if ($group == $rule->get_group()) {
                    if ($rule->is_allowed()) {
                        $this->log(__FUNCTION__ . " user '$uid' is in allowed group. Access allowed.");
                        return true;
                    } else {
                        $this->log(__FUNCTION__ . " user '$uid' is in restricted group. Access denied.");
                        return false;
                    }
                }
            }
        }

        // If a user has no encumberance attribute let them into Moodle.
        if (empty($userhasgroups)) {
            return true;
        }

        $this->log(__FUNCTION__ . " user '$uid' isn't in allowed. Access denied.");
        return false;
    }

    /**
     * Simplifies attribute key names
     *
     * Rather than attempting to have an explicity mapping this simply
     * detects long key names which contain non word characters and then
     * grabs the last useful component of the string. Note it creates new
     * keys, doesn't remove the old ones, and will not overwrite keys either.
     *
     * @param array $attributes A list of attributes from the request
     */
    public function simplify_attr($attributes) {

        foreach ($attributes as $key => $val) {
            if (preg_match("/\W/", $key)) {
                $parts = preg_split("/\W/", $key);
                $simple = $parts[count($parts) - 1];
                $attributes[$simple] = $attributes[$key];
            }
        }
        return $attributes;
    }

    /**
     * Given a user record, updates the fields on that user as per the mappings in the
     * iomadsaml2 configuration. Uses the attributes array as the source of data for updating each field.
     *
     * This is split into it's own function so update and creating users can use it
     * @param mixed $user The user record to update
     * @param mixed $attributes The attribute array (from the SAML Login)
     * @param bool $newuser If this user does not yet exist in the database
     * @return void
     * @throws dml_exception
     * @throws Exception
     * @throws coding_exception
     */
    public function update_user_record_from_attribute_map(&$user, $attributes, $newuser= false) {
        global $CFG;

        $mapconfig = get_config('auth_iomadsaml2');
        $allkeys = array_keys(get_object_vars($mapconfig));
        $update = false;

        foreach ($allkeys as $key) {
            if (preg_match('/^field_updatelocal_(.+)$/', $key, $match)) {
                $field = $match[1];
                if (!empty($mapconfig->{'field_map_'.$field})) {
                    $attr = $mapconfig->{'field_map_'.$field};
                    $updateonlogin = $mapconfig->{'field_updatelocal_'.$field} === 'onlogin';

                    if ($newuser || $updateonlogin) {
                        // Basic error handling, check to see if the attributes exist before mapping the data.
                        if (array_key_exists($attr, $attributes)) {
                            // Handing an empty array of attributes.
                            if (!empty($attributes[$attr])) {

                                // If can't have accounts with the same emails, check if email is taken before update a new user.
                                if ($field == 'email' && empty($CFG->allowaccountssameemail)) {
                                    $email = $attributes[$attr][0];
                                    if ($this->is_email_taken($email, $user->username ?? null)) {
                                        $this->log(__FUNCTION__ .
                                            " user '$user->username' email can't be updated as '$email' is taken");
                                        // Warn user that we are not able to update his email.
                                        \core\notification::warning(get_string('emailtakenupdate', 'auth_iomadsaml2', $email));

                                        continue;
                                    }
                                }

                                // We don't want Mapping Moodle field or username to be updated once they are set on user creation.
                                if (!$newuser) {
                                    if ($field == $this->config->mdlattr || $field == 'username') {
                                        $this->log(__FUNCTION__ .
                                            " user '$user->username' $field can't be updated once set");
                                        \core\notification::warning("Your $field wasn't updated");
                                        continue;
                                    }
                                }
                                if ($field == 'username') {
                                    $user->$field = strtolower($attributes[$attr][0]);
                                } else {
                                    // Custom profile fields have the prefix profile_field_ and will be saved as profile field data.
                                    $delimiter = $mapconfig->fielddelimiter;
                                    $user->$field = implode($delimiter, (array) $attributes[$attr]);
                                }
                                $update = true;
                            }
                        }
                    }
                }
            }
        }
        return $update;
    }

    /**
     * Checks the field map config for values that update onlogin or when a new user is created
     * and returns true when the fields have been merged into the user object.
     *
     * @param mixed $user The user record to update
     * @param mixed $attributes The attribute array (from the SAML Login)
     * @param bool $newuser If this user does not yet exist in the database
     * @return bool true on success
     */
    public function update_user_profile_fields(&$user, $attributes, $newuser = false) {
        global $CFG;
        if ($this->update_user_record_from_attribute_map($user, $attributes, $newuser)) {
            require_once($CFG->dirroot.'/user/lib.php');
            if ($user->description === true) {
                // Function get_complete_user_data() sets description = true to avoid keeping in memory.
                // If set to true - don't update based on data from this call.
                unset($user->description);
            }
            // We should save the profile fields first so they are present and
            // then we update the user which also fires events which other
            // plugins listen to so they have the correct user data.
            profile_save_data($user);
            user_update_user($user, false);
            return true;
        }
        return false;
    }

    /**
     * Get email address from attributes.
     *
     * @param array $attributes A list of attributes.
     *
     * @return bool|string
     */
    public function get_email_from_attributes(array $attributes) {
        if (!empty($this->config->field_map_email) && !empty($attributes[$this->config->field_map_email])) {
            return $attributes[$this->config->field_map_email][0];
        }

        return false;
    }

    /**
     * Get lowercase username from attributes, force as lowercase because Moodle requires it.
     *
     * @param array $attributes A list of attributes.
     *
     * @return bool|string
     */
    private function get_username_from_attributes(array $attributes) {
        if (!empty($this->config->field_map_username) && !empty($attributes[$this->config->field_map_username])) {
            return strtolower($attributes[$this->config->field_map_username][0]);
        }

        return false;
    }

    /**
     * Check if given email is taken by other user(s).
     *
     * @param string|bool $email Email to check.
     * @param string|null $excludeusername A user name to exclude.
     *
     * @return bool
     */
    public function is_email_taken($email, $excludeusername = null) {
        global $CFG, $DB;

        if (!empty($email)) {
            // Make a case-insensitive query for the given email address.
            $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid AND deleted = :deleted';
            $params = array(
                'email' => $email,
                'mnethostid' => $CFG->mnet_localhost_id,
                'deleted' => 0
            );

            if ($excludeusername) {
                $select .= ' AND username <> :username';
                $params['username'] = $excludeusername;
            }

            // If there are other user(s) that already have the same email, display an error.
            if ($DB->record_exists_select('user', $select, $params)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hook for overriding behaviour of logout page.
     * This method is called from login/logout.php page.
     *
     * There are 3 sessions we need to logout:
     * 1) The moodle session.
     * 2) The SimpleSAML SP session.
     * 3) The IdP session, if the IdP supports SingleSignout.
     */
    public function logoutpage_hook() {
        global $CFG, $SESSION, $redirect, $iomadsaml2config;

        $this->execute_callback('auth_iomadsaml2_logoutpage_hook');

        // Lets capture the iomadsaml2idp hash.
        $idp = $this->spname;
        if (!empty($SESSION->iomadsaml2idp)) {
            $idp = $SESSION->iomadsaml2idp;
        }

        $this->log(__FUNCTION__ . ' Do moodle logout');
        // Do the normal moodle logout first as we may redirect away before it
        // gets called by the normal core process.
        require_logout();

        require_once(__DIR__.'/../setup.php');

        // We just loaded the SP session which replaces the Moodle so we lost
        // the session data, lets temporarily restore the IdP.
        $SESSION->iomadsaml2idp = $idp;
        $auth = new \SimpleSAML\Auth\Simple($this->spname);

        // Regardless of wether we contact the IdP for Single Signout lets
        // still delete the local SP cookie so we force auth again next time.
        $cookiename = $iomadsaml2config['session.cookie.name'];
        $cookiesecure = is_moodle_cookie_secure();
        setcookie($cookiename, '', time() - HOURSECS, $CFG->sessioncookiepath, $CFG->sessioncookiedomain,
              $cookiesecure, $CFG->cookiehttponly);

        // Do not attempt to log out of the IdP.
        if (!$this->config->attemptsignout) {
            $alterlogout = $this->config->alterlogout;
            if (!empty($alterlogout)) {
                // If we don't sign out of the IdP we still want to honor the
                // alternate logout page.
                $this->log(__FUNCTION__ . " Do SSP alternate URL logout $alterlogout");
                redirect(new moodle_url($alterlogout));
            }
            return;
        }

        // Only log out of the IdP if we logged in via the IdP. TODO check session timeouts.
        if ($auth->isAuthenticated()) {
            $this->log(__FUNCTION__ . ' Do SSP logout');
            $alterlogout = $this->config->alterlogout;
            if (!empty($alterlogout)) {
                $this->log(__FUNCTION__ . " Do SSP alternate URL logout $alterlogout");
                $redirect = $alterlogout;
            }
            $auth->logout([
                'ReturnTo' => $redirect,
                'ReturnCallback' => ['\auth_iomadsaml2\api', 'after_logout_from_sp'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $username
     * @param string $password
     */
    public function user_login($username, $password) {
        return false;
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param object $config
     * @return boolean
     */
    public function process_config($config) {
        $haschanged = false;

        foreach (array_keys($this->defaults) as $key) {
            if ($config->$key != $this->config->$key) {
                set_config($key, $config->$key, 'auth_iomadsaml2');
                $haschanged = true;
            }
        }

        if ($haschanged) {
            $file = $this->get_file_sp_metadata_file();
            @unlink($file);
        }
        return true;
    }

    /**
     * A simple GUI tester which shows the raw API output
     */
    public function test_settings() {
        global $OUTPUT;

        if ($this->is_configured() && $this->is_debugging() && api::is_enabled()) {
            $action = new moodle_url('/auth/iomadsaml2/test.php');
            $mform = new \auth_iomadsaml2\form\testidpselect($action, ['metadataentities' => $this->metadataentities]);
            $mform->display();
        } else {
            echo $OUTPUT->render(new notification(get_string('test_noticetestrequirements', 'auth_iomadsaml2'),
                notification::NOTIFY_WARNING, false));

        }
    }

    /**
     * Returns the version of SSP that this plugin is using.
     *
     * @return string
     */
    public function get_ssp_version() {
        // To get the version there is no need to create key files and
        // perform the full initialization. For better performance
        // we only make sure \SimpleSAML\Configuration is accessible
        // through _autoload.php.
        require_once(__DIR__ . '/../_autoload.php');
        $config = new \SimpleSAML\Configuration(array(), '');
        return $config->getVersion();
    }

    /**
     * Allow iomadsaml2 auth method to be manually set for users e.g. bulk uploading users.
     */
    public function can_be_manually_set() {
        return true;
    }

    /**
     * Sets a preferred IdP in a cookie for faster subsequent logging in.
     *
     * @param string $idp a md5 encoded IdP entityid
     */
    public function set_idp_cookie($idp) {
        global $CFG;

        if (NO_MOODLE_COOKIES) {
            return;
        }

        $cookiename = 'MOODLEIDP1_'.$CFG->sessioncookie;

        $cookiesecure = is_moodle_cookie_secure();

        // Delete old cookie.
        setcookie($cookiename, '', time() - HOURSECS, $CFG->sessioncookiepath, $CFG->sessioncookiedomain,
                  $cookiesecure, $CFG->cookiehttponly);

        if ($idp !== '') {
            // Set username cookie for 60 days.
            setcookie($cookiename, $idp, time() + (DAYSECS * 60), $CFG->sessioncookiepath, $CFG->sessioncookiedomain,
                      $cookiesecure, $CFG->cookiehttponly);
        }
    }

    /**
     * Gets a preferred IdP from a cookie for faster subsequent logging in.
     *
     * @return string $idp a md5 encoded IdP entityid
     */
    public function get_idp_cookie() {
        global $CFG;

        if (NO_MOODLE_COOKIES) {
            return '';
        }

        $cookiename = 'MOODLEIDP1_'.$CFG->sessioncookie;

        if (empty($_COOKIE[$cookiename])) {
            return '';
        } else {
            return $_COOKIE[$cookiename];
        }
    }

    /**
     * Execute callback function
     * @param string $function name of the callback function to be executed
     * @param string $file file to find the function
     */
    private function execute_callback($function, $file = 'lib.php') {
        if (function_exists('get_plugins_with_function')) {
            $pluginsfunction = get_plugins_with_function($function, $file);
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $pluginfunction();
                }
            }
        }
    }
}
