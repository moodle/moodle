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

namespace core\moodlenet;

use core\oauth2\issuer;

/**
 * Class containing static utilities (such as various checks) required by the MoodleNet API.
 *
 * @package   core
 * @copyright 2023 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utilities {
    /**
     * Check whether the specified issuer is configured as a MoodleNet instance that can be shared to.
     *
     * @param issuer $issuer The OAuth 2 issuer being validated.
     * @return bool true if the issuer is enabled and available to share to.
     */
    public static function is_valid_instance(issuer $issuer): bool {
        global $CFG;

        $issuerid = $issuer->get('id');
        $allowedissuer = get_config('moodlenet', 'oauthservice');

        return ($CFG->enablesharingtomoodlenet && $issuerid == $allowedissuer && $issuer->get('enabled') &&
            $issuer->get('servicetype') == 'moodlenet');
    }

    /**
     * Check whether a user has the capabilities required to share activities from a given course to MoodleNet.
     *
     * @param \core\context\course $coursecontext Course context where the activity would be shared from.
     * @param int $userid The user ID being checked.
     * @return boolean
     */
    public static function can_user_share(\core\context\course $coursecontext, int $userid): bool {
        return (has_capability('moodle/moodlenet:shareactivity', $coursecontext, $userid) &&
            has_capability('moodle/backup:backupactivity', $coursecontext, $userid));
    }
}
