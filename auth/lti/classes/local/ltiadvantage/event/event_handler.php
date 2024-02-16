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
        // The event data isn't important here. The intent of this listener is to ensure that the MoodleSession cookie is set up
        // properly during LTI launches + login. This means two things:
        // i) it's set with SameSite=None; Secure; where possible (since OIDC needs HTTPS this will almost always be possible).
        // ii) it set with the 'Partitioned' attribute, when required.
        // The former ensures cross-site cookies are sent for embedded launches. The latter is an opt-in flag needed to use Chrome's
        // partitioning mechanism, CHIPS.
        if (cookie_helper::cookies_supported()) {
            cookie_helper::setup_session_cookie();
        }
    }
}
