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
 * Login renderable.
 *
 * @package    core_auth
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_auth\output;

use help_icon;
use moodle_url;
use renderable;
use stdClass;
use templatable;

/**
 * Login renderable class.
 *
 * @package    core_auth
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class login implements renderable, templatable {

    /** @var bool Whether to auto focus the form fields. */
    public $autofocusform;
    /** @var bool Whether we can login as guest. */
    public $canloginasguest;
    /** @var bool Whether we can login by e-mail. */
    public $canloginbyemail;
    /** @var bool Whether we can sign-up. */
    public $cansignup;
    /** @var help_icon The cookies help icon. */
    public $cookieshelpicon;
    /** @var string The error message, if any. */
    public $error;
    /** @var string The error title, shown as bold heading above the error message for credential failures. */
    public $errortitle;
    /** @var string The info message, if any. */
    public $info;
    /** @var moodle_url Forgot password URL. */
    public $forgotpasswordurl;
    /** @var array Additional identify providers, contains the keys 'url', 'name' and 'icon'. */
    public $identityproviders;
    /** @var string Login instructions, if any. */
    public $instructions;
    /** @var moodle_url The form action login URL. */
    public $loginurl;
    /** @var moodle_url The sign-up URL. */
    public $signupurl;
    /** @var string The user name to pre-fill the form with. */
    public $username;
    /** @var string The language selector menu. */
    public $languagemenu;
    /** @var string The csrf token to limit login to requests that come from the login form. */
    public $logintoken;
    /** @var string Maintenance message, if Maintenance is enabled. */
    public $maintenance;
    /** @var string ReCaptcha element HTML. */
    public $recaptcha;
    /** @var bool Toggle the password visibility icon. */
    public $togglepassword;
    /** @var bool Toggle the password visibility icon for small screens only. */
    public $smallscreensonly;
    /** @var bool Whether $instructions was auto-filled with the sign-up fallback message. */
    private bool $instructionsfromsignupfallback = false;
    /**
     * @var \League\OAuth2\Server\Entities\ClientEntityInterface|null The OAuth2 client
     *      requesting authorization, if this login form is being shown as part of an OAuth2
     *      authorization flow, or null for an ordinary Moodle login.
     */
    private ?\League\OAuth2\Server\Entities\ClientEntityInterface $oauth2client = null;

    /**
     * Constructor.
     *
     * @param array $authsequence The enabled sequence of authentication plugins.
     * @param string $username The username to display.
     */
    public function __construct(array $authsequence, $username = '') {
        global $CFG, $PAGE;

        $this->username = $username;

        $languagedata = new \core\output\language_menu($PAGE);

        // Fetch the renderer directly, rather than using the global $OUTPUT, since $OUTPUT may
        // still be the bootstrap_renderer stub at this point (it only resolves to the real
        // renderer the first time a method is *called on* it, and this constructor may run
        // before that has happened, e.g. when building this renderable from a router-based
        // page such as the OAuth2 authorisation flow).
        $this->languagemenu = $languagedata->export_for_action_menu($PAGE->get_renderer('core'));
        $this->canloginasguest = $CFG->guestloginbutton && !isguestuser();
        $this->canloginbyemail = !empty($CFG->authloginviaemail);
        $this->cansignup = $CFG->registerauth == 'email' || !empty($CFG->registerauth);
        if ($CFG->rememberusername == 0) {
            $this->cookieshelpicon = new help_icon('cookiesenabledonlysession', 'core');
        } else {
            $this->cookieshelpicon = new help_icon('cookiesenabled', 'core');
        }

        $this->autofocusform = !empty($CFG->loginpageautofocus);

        $this->forgotpasswordurl = new moodle_url('/login/forgot_password.php');
        $this->loginurl = new moodle_url('/login/index.php');
        $this->signupurl = new moodle_url('/login/signup.php');

        // Authentication instructions.
        $this->instructions = $CFG->auth_instructions;
        if (\core\di::get(\core\authentication::class)->is_enabled('none')) {
            $this->instructions = get_string('loginstepsnone');
        } else if ($CFG->registerauth == 'email' && empty($this->instructions)) {
            $this->instructions = get_string('logindonthaveaccount');
            $this->instructionsfromsignupfallback = true;
        }

        if ($CFG->maintenance_enabled == true) {
            if (!empty($CFG->maintenance_message)) {
                $this->maintenance = $CFG->maintenance_message;
            } else {
                $this->maintenance = get_string('sitemaintenance', 'admin');
            }
        }

        // Identity providers.
        $this->identityproviders = \auth_plugin_base::get_identity_providers($authsequence);
        $this->logintoken = \core\session\manager::get_login_token();

        // ReCaptcha.
        if (login_captcha_enabled()) {
            require_once($CFG->libdir . '/recaptchalib_v2.php');
            $this->recaptcha = recaptcha_get_challenge_html(RECAPTCHA_API_URL, $CFG->recaptchapublickey);
        }

        // Toggle password visibility icon.
        $this->togglepassword = get_config('core', 'loginpasswordtoggle') == TOGGLE_SENSITIVE_ENABLED ||
            get_config('core', 'loginpasswordtoggle') == TOGGLE_SENSITIVE_SMALL_SCREENS_ONLY;
        $this->smallscreensonly = get_config('core', 'loginpasswordtoggle') == TOGGLE_SENSITIVE_SMALL_SCREENS_ONLY;
    }

    /**
     * Set the error message. For the AUTH_LOGIN_FAILED case, also sets
     * an errortitle so the template can render a bold heading above the detail text.
     *
     * @param string $error The error message.
     * @param int $errorcode The error code from login/index.php.
     */
    public function set_error(string $error, int $errorcode = 0): void {
        if ($errorcode === AUTH_LOGIN_FAILED) {
            $this->errortitle = get_string('logininvalidlogintitle');
            $this->error = get_string('logininvalidlogindetail');
        } else {
            $this->error = $error;
        }
    }

    /**
     * Set the info message.
     *
     * @param string $info The info message.
     */
    public function set_info(string $info): void {
        $this->info = $info;
    }

    /**
     * Override whether guest login is offered on this login form instance.
     *
     * Site-wide guest login may be enabled, but a specific flow (for example, the
     * OAuth2 authorisation login screen) may need to suppress it regardless.
     *
     * @param bool $canloginasguest Whether guest login should be offered.
     */
    public function set_can_login_as_guest(bool $canloginasguest): void {
        $this->canloginasguest = $canloginasguest;
    }

    /**
     * Override whether sign-up is offered on this login form instance.
     *
     * Site-wide registration may be open, but a specific flow (for example, the
     * OAuth2 authorisation login screen) may need to suppress it regardless. If the
     * "don't have an account? sign up" instructions text was auto-generated because
     * sign-up is enabled site-wide, it is cleared here too, so the form doesn't point
     * users at sign-up while also hiding the option to do it.
     *
     * @param bool $signupallowed Whether sign-up should be offered.
     */
    public function set_signup_allowed(bool $signupallowed): void {
        $this->cansignup = $signupallowed;
        if (!$signupallowed && $this->instructionsfromsignupfallback) {
            $this->instructions = '';
            $this->instructionsfromsignupfallback = false;
        }
    }

    /**
     * Override the form action URL for this login form instance.
     *
     * Some flows (for example, the OAuth2 authorisation login screen) need the
     * credentials form to post back to a different endpoint than the standard
     * login/index.php, so the flow can be resumed once the user has authenticated,
     * rather than landing on the default post-login destination.
     *
     * @param moodle_url $loginurl The URL the login form should submit to.
     */
    public function set_login_url(moodle_url $loginurl): void {
        $this->loginurl = $loginurl;
    }

    /**
     * Set the OAuth2 client requesting authorization, so its identity can be shown on this
     * login form instance before the user enters their credentials.
     *
     * Optional: ordinary Moodle login (e.g. login/index.php) never calls this, and the
     * template renders exactly as before when no client has been set.
     *
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     */
    public function set_oauth2_client(\League\OAuth2\Server\Entities\ClientEntityInterface $client): void {
        $this->oauth2client = $client;
    }

    /**
     * Export data for the template
     *
     * The supported rendering path always provides a core_renderer. The method signature uses renderer_base to
     * satisfy the templatable interface.
     *
     * @param \core\output\core_renderer $output
     * @return stdClass
     */
    public function export_for_template(\core\output\renderer_base $output) {
        global $CFG, $SITE;

        $identityproviders = \auth_plugin_base::prepare_identity_providers_for_output($this->identityproviders, $output);

        $data = new stdClass();
        $data->autofocusform = $this->autofocusform;
        $data->canloginasguest = $this->canloginasguest;
        $data->canloginbyemail = $this->canloginbyemail;
        $data->cansignup = $this->cansignup;
        $data->cookieshelpicon = $this->cookieshelpicon->export_for_template($output);
        $data->error = $this->error;
        $data->errorformatted = $output->error_text($data->error);
        $data->errortitle = $this->errortitle;
        $data->info = $this->info;
        $data->forgotpasswordurl = $this->forgotpasswordurl->out(false);
        $data->hasidentityproviders = !empty($this->identityproviders);
        $data->identityproviders = $identityproviders;
        [$data->instructions, $data->instructionsformat] = \core_external\util::format_text(
            $this->instructions,
            FORMAT_MOODLE,
            \core\context\system::instance()->id,
        );
        $data->loginurl = $this->loginurl->out(false);
        $data->signupurl = $this->signupurl->out(false);
        $data->username = $this->username;
        $data->logintoken = $this->logintoken;
        $data->maintenance = format_text($this->maintenance, FORMAT_MOODLE);
        $data->languagemenu = $this->languagemenu;
        $data->recaptcha = $this->recaptcha;
        $data->togglepassword = $this->togglepassword;
        $data->smallscreensonly = $this->smallscreensonly;
        $data->showloginform = get_config('core', 'showloginform') === false || get_config('core', 'showloginform');

        $data->hasoauth2client = $this->oauth2client !== null;
        $data->client = $this->oauth2client !== null
            ? \core_auth\output\oauth2\oauth2_page::describe_client($this->oauth2client)
            : null;
        $data->logourl = null;
        $logourl = $output->get_logo_url();
        if ($logourl) {
            $data->logourl = $logourl->out(false);
        }

        $data->sitename = \format_string(
            $SITE->fullname,
            true,
            ['context' => \core\context\course::instance(SITEID), 'escape' => false]
        );

        $data->hasauthinstructions = !empty($CFG->auth_instructions);

        return $data;
    }
}
