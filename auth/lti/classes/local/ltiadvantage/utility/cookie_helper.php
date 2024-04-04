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

namespace auth_lti\local\ltiadvantage\utility;
use core\session\utility\cookie_helper as core_cookie_helper;

/**
 * Helper class providing utils dealing with cookies in LTI, particularly 3rd party cookies.
 *
 * @package    auth_lti
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cookie_helper {

    /** @var int Cookies are not supported. */
    public const COOKIE_METHOD_NOT_SUPPORTED = 0;

    /** @var int Cookies are supported without explicit partitioning. */
    public const COOKIE_METHOD_NO_PARTITIONING = 1;

    /** @var int Cookies are supported via explicit partitioning. */
    public const COOKIE_METHOD_EXPLICIT_PARTITIONING = 2;

    /**
     * Check whether cookies can be used with the current user agent and, if so, via what method they are set.
     *
     * Currently, this tries 2 modes of setting a test cookie:
     * 1. Setting a SameSite=None, Secure cookie. This will work in any first party context, and in 3rd party contexts for
     * any browsers supporting automatic partitioning of 3rd party cookies (E.g. Firefox, Brave).
     * 2. If 1 fails, setting a cookie with the Chrome 'Partitioned' attribute included, opting that cookie into CHIPS. This will
     * work for Chrome.
     *
     * Upon completion of the cookie check, the check sets a SESSION flag indicating the method used to set the cookie, and upgrades
     * the session cookie ('MoodleSession') using the respective method. This ensure the session cookie will continue to be sent.
     *
     * Then, the following methods can be used by client code to query whether the UA supports cookies, and how:
     * @see self::cookies_supported() - whether it could be set at all.
     * @see self::get_cookies_supported_mode() - if a cookie could be set, what mode was used to set it.
     *
     * This permits client code to make sure it's setting its cookies appropriately (via the advertised method), and allows it to
     * present notices - such as in the case where a given UA is found to be lacking the requisite cookie support.
     * E.g.
     * cookie_helper::do_cookie_check($mypageurl);
     * if (!cookie_helper::cookies_supported()) {
     *     // Print a notice stating that cookie support is required.
     * }
     * // Elsewhere in other client code...
     * if (cookie_helper::get_cookies_supported_mode() === cookie_helper::COOKIE_METHOD_EXPLICIT_PARTITIONING) {
     *     // Set a cookie, making sure to use the helper to also opt-in to partitioning.
     *     setcookie('myauthcookie', 'myauthcookievalue', ['samesite' => 'None', 'secure' => true]);
     *     cookie_helper::add_partitioning_to_cookie('myauthcookie');
     * }
     *
     * @param \moodle_url $pageurl the URL of the page making the check, used to redirect back to after setting test cookies.
     * @return void
     */
    public static function do_cookie_check(\moodle_url $pageurl): void {
        global $_COOKIE, $SESSION, $CFG;
        $cookiecheck1 = optional_param('cookiecheck1', null, PARAM_INT);
        $cookiecheck2 = optional_param('cookiecheck2', null, PARAM_INT);

        if (empty($cookiecheck1)) {
            // Start the cookie check. Set two test cookies - one samesite none, and one partitioned - and redirect.
            // Set cookiecheck to show the check has started.
            self::set_test_cookie('cookiecheck1', self::COOKIE_METHOD_NO_PARTITIONING);
            self::set_test_cookie('cookiecheck2', self::COOKIE_METHOD_EXPLICIT_PARTITIONING, true);
            $pageurl->params([
                'cookiecheck1' => self::COOKIE_METHOD_NO_PARTITIONING,
                'cookiecheck2' => self::COOKIE_METHOD_EXPLICIT_PARTITIONING,
            ]);

            // LTI needs to guarantee the 'SameSite=None', 'Secure' (and sometimes 'Partitioned') attributes are set on the
            // MoodleSession cookie. This is done via manipulation of the outgoing headers after the cookie check redirect. To
            // guarantee these outgoing Set-Cookie headers will be created after the redirect, expire the current cookie.
            core_cookie_helper::expire_moodlesession();

            redirect($pageurl);
        } else {
            // Have already started a cookie check, so check the result.
            $cookie1received = isset($_COOKIE['cookiecheck1']) && $_COOKIE['cookiecheck1'] == $cookiecheck1;
            $cookie2received = isset($_COOKIE['cookiecheck2']) && $_COOKIE['cookiecheck2'] == $cookiecheck2;

            if ($cookie1received || $cookie2received) {
                // The test cookie could be set and received.
                // Set a session flag storing the method used to set it, and make sure the session cookie uses this method.
                $cookiemethod = $cookie1received ? self::COOKIE_METHOD_NO_PARTITIONING : self::COOKIE_METHOD_EXPLICIT_PARTITIONING;
                $SESSION->auth_lti_cookie_method = $cookiemethod;

                self::setup_session_cookie();
            }
        }
    }

    /**
     * If a cookie check has been made, returns whether cookies could be set or not.
     *
     * @return bool whether cookies are supported or not.
     */
    public static function cookies_supported(): bool {
        return self::get_cookies_supported_method() !== self::COOKIE_METHOD_NOT_SUPPORTED;
    }

    /**
     * If a cookie check has been made, gets the method used to set a cookie, or self::COOKIE_METHOD_NOT_SUPPORTED if not supported.
     *
     * For cookie methods:
     * @see self::COOKIE_METHOD_NOT_SUPPORTED
     * @see self::COOKIE_METHOD_NO_PARTITIONING
     * @see self::COOKIE_METHOD_EXPLICIT_PARTITIONING
     *
     * @return int the constant representing the method by which the cookie was set, or not.
     */
    public static function get_cookies_supported_method(): int {
        global $SESSION;
        return $SESSION->auth_lti_cookie_method ?? self::COOKIE_METHOD_NOT_SUPPORTED;
    }

    /**
     * Sets up the session cookie according to the method used in the cookie check, and with SameSite=None; Secure attributes.
     *
     * @return void
     */
    public static function setup_session_cookie(): void {
        global $CFG;
        require_once($CFG->libdir . '/sessionlib.php');

        if (is_moodle_cookie_secure()) {
            $atts = ['SameSite=None', 'Secure'];
            if (self::get_cookies_supported_method() == self::COOKIE_METHOD_EXPLICIT_PARTITIONING) {
                $atts[] = 'Partitioned';
            }
            core_cookie_helper::add_attributes_to_cookie_response_header('MoodleSession' . $CFG->sessioncookie, $atts);
        }
    }

    /**
     * Set a test cookie, using SameSite=None; Secure; attributes if possible, and with or without partitioning opt-in.
     *
     * @param string $name cookie name
     * @param string $value cookie value
     * @param bool $partitioned whether to try to add partitioning opt-in, which requires secure cookies (https sites).
     * @return void
     */
    private static function set_test_cookie(string $name, string $value, bool $partitioned = false): void {
        global $CFG;
        require_once($CFG->libdir . '/sessionlib.php');

        $atts = ['expires' => time() + 30];
        if (is_moodle_cookie_secure()) {
            $atts['samesite'] = 'none';
            $atts['secure'] = true;
        }
        setcookie($name, $value, $atts);

        if (is_moodle_cookie_secure() && $partitioned) {
            core_cookie_helper::add_attributes_to_cookie_response_header($name, ['Partitioned']);
        }
    }
}
