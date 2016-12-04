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
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use context_system;
use help_icon;
use moodle_url;
use renderable;
use renderer_base;
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
    /** @var moodle_url Forgot password URL. */
    public $forgotpasswordurl;
    /** @var array Additional identify providers, contains the keys 'url', 'name' and 'icon'. */
    public $identityproviders;
    /** @var string Login instructions, if any. */
    public $instructions;
    /** @var moodle_url The form action login URL. */
    public $loginurl;
    /** @var bool Whether the password can be auto completed. */
    public $passwordautocomplete;
    /** @var bool Whether the username should be remembered. */
    public $rememberusername;
    /** @var moodle_url The sign-up URL. */
    public $signupurl;
    /** @var string The user name to pre-fill the form with. */
    public $username;

    /**
     * Constructor.
     *
     * @param array $authsequence The enabled sequence of authentication plugins.
     * @param string $username The username to display.
     */
    public function __construct(array $authsequence, $username = '') {
        global $CFG, $SESSION;

        $this->username = $username;

        $this->canloginasguest = $CFG->guestloginbutton and !isguestuser();
        $this->canloginbyemail = !empty($CFG->authloginviaemail);
        $this->cansignup = $CFG->registerauth == 'email' || !empty($CFG->registerauth);
        $this->cookieshelpicon = new help_icon('cookiesenabled', 'core');

        $this->autofocusform = !empty($CFG->loginpageautofocus);
        $this->passwordautocomplete = !empty($CFG->loginpasswordautocomplete);
        $this->rememberusername = isset($CFG->rememberusername) and $CFG->rememberusername == 2;

        $this->forgotpasswordurl = new moodle_url($CFG->httpswwwroot . '/login/forgot_password.php');
        $this->loginurl = new moodle_url($CFG->httpswwwroot . '/login/index.php');
        $this->signupurl = new moodle_url('/login/signup.php');

        // Authentication instructions.
        $this->instructions = $CFG->auth_instructions;
        if (is_enabled_auth('none')) {
            $this->instructions = get_string('loginstepsnone');
        } else if ($CFG->registerauth == 'email' && empty($this->instructions)) {
            $this->instructions = get_string('loginsteps', 'core', 'signup.php');
        }

        // Identity providers.
        $identityproviders = [];
        foreach ($authsequence as $authname) {
            $authplugin = get_auth_plugin($authname);
            $identityproviders = array_merge($identityproviders, $authplugin->loginpage_idp_list($SESSION->wantsurl));
        }
        $this->identityproviders = $identityproviders;
    }

    /**
     * Set the error message.
     *
     * @param string $error The error message.
     */
    public function set_error($error) {
        $this->error = $error;
    }

    public function export_for_template(renderer_base $output) {
        global $CFG;

        $identityproviders = array_map(function($idp) use ($output) {
            $idp['icon'] = $idp['icon']->export_for_template($output);
            if ($idp['url'] instanceof moodle_url) {
                $idp['url'] = $idp['url']->out(false);
            }
            return $idp;
        }, $this->identityproviders);

        $data = new stdClass();
        $data->autofocusform = $this->autofocusform;
        $data->canloginasguest = $this->canloginasguest;
        $data->canloginbyemail = $this->canloginbyemail;
        $data->cansignup = $this->cansignup;
        $data->cookieshelpicon = $this->cookieshelpicon->export_for_template($output);
        $data->error = $this->error;
        $data->forgotpasswordurl = $this->forgotpasswordurl->out(false);
        $data->hasidentityproviders = !empty($this->identityproviders);
        $data->hasinstructions = !empty($this->instructions);
        $data->identityproviders = $identityproviders;
        list($data->instructions, $data->instructionsformat) = external_format_text($this->instructions, FORMAT_MOODLE,
            context_system::instance()->id);
        $data->loginurl = $this->loginurl->out(false);
        $data->rememberusername = $this->rememberusername;
        $data->passwordautocomplete = $this->passwordautocomplete;
        $data->signupurl = $this->signupurl->out(false);

        return $data;
    }
}
