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
 * Event observers for local_launchpad.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_launchpad;

defined('MOODLE_INTERNAL') || die();

/**
 * LaunchPad observers: Fellowship signup tagging + persona home routing.
 *
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * After auth_email creates the user, store toba_portal preference.
     *
     * From Landing-Page+Login-Screen (#14).
     *
     * @param \core\event\user_created $event
     */
    public static function user_created(\core\event\user_created $event): void {
        global $CFG, $DB, $SESSION;

        if (empty($SESSION->{registration::SESSION_PORTAL})) {
            return;
        }

        $userid = (int) $event->objectid;
        $user = $DB->get_record('user', ['id' => $userid], 'id,auth,confirmed');
        if (!$user) {
            return;
        }

        $registerauth = !empty($CFG->registerauth) ? $CFG->registerauth : 'email';
        // Only tag unconfirmed self-registration accounts from the Fellowship signup flow.
        if ($user->auth !== $registerauth || (int) $user->confirmed !== 0) {
            return;
        }

        registration::apply_portal_preference($userid);
    }

    /**
     * After login: honour login portal tab, then persona default home if still unset.
     *
     * From Fellowship UI / persona homes (PR #15) + login portal gate wiring.
     * Deep wantsurl targets are preserved inside persona_home.
     *
     * @param \core\event\user_loggedin $event
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        $userid = (int) $event->userid;
        if ($userid <= 0) {
            return;
        }
        // Login form portal field (employer/fellowship) must win over /my/ defaults.
        login_portal::maybe_pin_wantsurl_from_request();
        persona_home::maybe_set_wantsurl($userid);
    }
}
