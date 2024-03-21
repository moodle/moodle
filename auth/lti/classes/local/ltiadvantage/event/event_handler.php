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

namespace auth_lti\local\ltiadvantage\event;

use auth_lti\local\ltiadvantage\utility\cookie_helper;
use core\event\user_loggedin;

/**
 * Event handler for auth_lti.
 *
 * @package    auth_lti
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_handler {

    /**
     * Allows the plugin to augment Set-Cookie headers when the user_loggedin event is fired as part of complete_user_login() calls.
     *
     * @param user_loggedin $event the event
     * @return void
     */
    public static function handle_user_loggedin(user_loggedin $event): void {
        // The event data isn't important here. The intent of this listener is to ensure that the MoodleSession cookie gets the
        // 'Partitioned' attribute, when required - an opt-in flag needed to use Chrome's partitioning mechanism, CHIPS. During LTI
        // auth, the auth class (auth/lti/auth.php) calls complete_user_login(), which generates a new session cookie as part of its
        // login process. This handler makes sure that this new cookie is intercepted and partitioned, if needed.
        if (cookie_helper::cookies_supported()) {
            if (cookie_helper::get_cookies_supported_method() == cookie_helper::COOKIE_METHOD_EXPLICIT_PARTITIONING) {
                global $CFG;
                cookie_helper::add_attributes_to_cookie_response_header('MoodleSession' . $CFG->sessioncookie,
                    ['Partitioned', 'Secure']);
            }
        }
    }
}
