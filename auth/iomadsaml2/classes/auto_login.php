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
 * Auto-login class.
 *
 * @package   auth_iomadsaml2
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2;

use auth_iomadsaml2\admin\iomadsaml2_settings;

/**
 * Auto-login class.
 */
class auto_login {
    /** @var bool True if already considered auto-login on this page. */
    protected static $processed;

    /**
     * Called on every page to give an opportunity to auto-login.
     *
     * This is called on most pages via the after_require_login callback, and also by the
     * before_http_headers callback, so it might be called twice. We only want to run it once.
     *
     * The auto-login system is only useful on pages which do not already require a proper login.
     * (For example the user might be trying to access the site homepage, or a course page which
     * allows guest access.) In this case, the auto-login system lets us attempt to log in if
     * we think the user might be logged into the SAML identity provider, so that they automatically
     * get access to the extra controls that may appear on that page if they are logged in to their
     * own account.
     */
    public static function process() {
        global $SESSION, $SCRIPT;

        if (self::$processed) {
            return;
        }
        self::$processed = true;

        // Do not apply this code in auth or login scripts, because those are likely to be already
        // handling login (either auto-login or not). Users do not need to start auto-login from
        // these pages, only from 'normal' Moodle pages they are trying to access.
        if (preg_match('~^(/auth/|/login/)~', $SCRIPT)) {
            return;
        }

        // If this is not a GET request, don't try autologin.
        if (empty($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }

        // Only do anything if the user isn't already logged in, and the auth plugin is enabled.
        if (!self::check_not_logged_in_and_enabled()) {
            return;
        }

        // Get config.
        $auth = get_auth_plugin('iomadsaml2');
        switch ($auth->config->autologin) {
            // Turned off.
            case iomadsaml2_settings::OPTION_AUTO_LOGIN_NO:
                return;

            // Login once per session. Don't try login if we already tried this session.
            case iomadsaml2_settings::OPTION_AUTO_LOGIN_SESSION:
                if (!empty($SESSION->auth_iomadsaml2_triedautologin)) {
                    return;
                }
                break;

            // Login when a cookie exists or changes. Don't try login if we already tried for this
            // cookie value, or the cookie isn't set.
            case iomadsaml2_settings::OPTION_AUTO_LOGIN_COOKIE:
                // If the cookie setting isn't filled in, do nothing.
                if (empty($auth->config->autologincookie)) {
                    return;
                }
                // If the cookie isn't set, do nothing.
                if (empty($_COOKIE[$auth->config->autologincookie])) {
                    return;
                }
                // If the cookie is set to the same value it was last time we looked, do nothing.
                $currentcookie = $_COOKIE[$auth->config->autologincookie];
                if (!empty($SESSION->auth_iomadsaml2_lastautologincookie) &&
                        $SESSION->auth_iomadsaml2_lastautologincookie === $currentcookie) {
                    return;
                }
                break;
        }

        // Check the plugin is configured (it probably is since they set up the autologin option).
        // Note: This check is after the switch because it's highly likely that we will exit there.
        // is_configured is not a costly function call but it does make some filesystem checks.
        if (!$auth->is_configured()) {
            return;
        }

        // Record in session that we attempted login.
        switch ($auth->config->autologin) {
            case iomadsaml2_settings::OPTION_AUTO_LOGIN_SESSION:
                $SESSION->auth_iomadsaml2_triedautologin = true;
                break;

            case iomadsaml2_settings::OPTION_AUTO_LOGIN_COOKIE:
                $SESSION->auth_iomadsaml2_lastautologincookie = $currentcookie;
                break;
        }
        $SESSION->auth_iomadsaml2_autologinattempt = true;

        // Now actually try to log in!
        self::login($auth);
    }

    /**
     * Confirms that the user is not logged in, and that auth_iomadsaml2 is enabled.
     *
     * @return bool True if the user is not logged in (or is guest), and iomadsaml2 is enabled
     */
    protected static function check_not_logged_in_and_enabled() {
        // If they are already logged in, we don't need autologin.
        if (isloggedin() && !isguestuser()) {
            return false;
        }

        // Check if this plugin is enabled.
        return \auth_iomadsaml2\api::is_enabled();
    }

    /**
     * Tries to auto-login by making a passive login request.
     *
     * Also called once redirected back after a successful request.
     *
     * @param \auth_plugin_iomadsaml2 $auth Auth plugin
     */
    protected static function login(\auth_plugin_iomadsaml2 $auth) {
        global $CFG, $FULLME, $SESSION, $SCRIPT;

        require(__DIR__ . '/../setup.php');
        $auth = get_auth_plugin('iomadsaml2');

        // Set the default IdP to be the first in the list. Used when dual login is disabled.
        $SESSION->iomadsaml2idp = reset($auth->metadataentities)->md5entityid;

        // Target URL is normally the same as current page, but if we got redirected to enrol.php
        // with a 'wants' URL, then that means if the login is successful we should try again at
        // the original URL.
        $target = $FULLME;
        if ($SCRIPT === '/enrol/index.php' && !empty($SESSION->wantsurl)) {
            $target = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        }
        $encodedtarget = urlencode((new \moodle_url($target))->out_as_local_url(false));

        $simplesaml = new \SimpleSAML\Auth\Simple($auth->spname);

        $params = [
            'isPassive' => true,
            'ErrorURL' => $CFG->wwwroot . '/auth/iomadsaml2/autologin.php?success=0&url=' . $encodedtarget,
            'ReturnTo' => $CFG->wwwroot . '/auth/iomadsaml2/autologin.php?success=1&url=' . $encodedtarget
        ];

        $simplesaml->requireAuth($params);

        // We only get back here if they already logged in.
        $attributes = $simplesaml->getAttributes();
        $auth->saml_login_complete($attributes);
    }

    /**
     * Finishes the login (called from autologin.php).
     *
     * This function redirects if successful.
     *
     * @param bool $success True if successful login
     * @param \moodle_url $url URL for redirecting to
     */
    public static function finish($success, \moodle_url $url) {
        global $SESSION;
        if (empty($SESSION->auth_iomadsaml2_autologinattempt)) {
            return;
        }
        unset($SESSION->auth_iomadsaml2_autologinattempt);

        // Only do anything if the user isn't already logged in, and the auth plugin is enabled.
        if (!self::check_not_logged_in_and_enabled()) {
            return;
        }

        // Get config.
        $auth = get_auth_plugin('iomadsaml2');
        if ($auth->config->autologin == iomadsaml2_settings::OPTION_AUTO_LOGIN_NO) {
            return;
        }

        // Check plugin.
        if (!$auth->is_configured()) {
            return;
        }

        // Now actually try to finish the login, only if it was successful!
        if ($success) {
            $SESSION->wantsurl = $url;
            self::login($auth);
        }

        // If it didn't succeed, redirect.
        redirect($url);
    }
}
