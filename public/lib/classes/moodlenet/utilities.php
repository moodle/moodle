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
     * Check whether a user has the capabilities required to share activities or courses to MoodleNet.
     *
     * @param \core\context\course $coursecontext Course context where the activity or course would be shared from.
     * @param int $userid The user ID being checked.
     * @param string $type The type of resource being checked (either 'activity' or 'course').
     * @return boolean
     * @throws \coding_exception If an invalid resource type is provided.
     */
    public static function can_user_share(\core\context\course $coursecontext, int $userid, string $type = 'activity'): bool {
        if ($type === 'course') {
            return (has_capability('moodle/moodlenet:sharecourse', $coursecontext, $userid) &&
                has_capability('moodle/backup:backupcourse', $coursecontext, $userid));
        } else if ($type === 'activity') {
            return (has_capability('moodle/moodlenet:shareactivity', $coursecontext, $userid) &&
                has_capability('moodle/backup:backupactivity', $coursecontext, $userid));
        }

        throw new \coding_exception('Invalid resource type');
    }

    /**
     * Get the support url.
     *
     * @return string
     */
    public static function get_support_url(): string {
        global $CFG;
        $supporturl = '';

        if ($CFG->supportavailability && $CFG->supportavailability !== CONTACT_SUPPORT_DISABLED) {
            if (!empty($CFG->supportpage)) {
                $supporturl = $CFG->supportpage;
            } else {
                $supporturl = $CFG->wwwroot . '/user/contactsitesupport.php';
            }
        }

        return $supporturl;
    }

    /**
     * Check the user has a valid capability in any course they are enrolled in.
     *
     * @param int $userid The user id to query
     * @return string Returns 'yes' if capability found, 'no' if not
     */
    public static function does_user_have_capability_in_any_course(int $userid): string {
        // We are checking this way because we are not always in the course context and need
        // a way to retrieve the user's courses to see if any of them have the correct capability.
        $capabilities = [
            'moodle/moodlenet:sharecourse',
            'moodle/moodlenet:shareactivity'
        ];
        // We are using 'no' instead of false to avoid confusing a cache key
        // that was not found (returns false) with a user who does not have the capablity.
        $isallowed = 'no';
        $cache = \cache::make('core', 'moodlenet_usercanshare');
        $cachedvalue = $cache->get($userid);

        if ($cachedvalue === false) {
            foreach ($capabilities as $capability) {
                // Find at least one course that contains a capability match.
                $course = get_user_capability_course($capability, $userid, true, '', 'id', 1);

                if (!empty($course)) {
                    // Set the cache to 'yes' and break out of the loop.
                    $isallowed = 'yes';
                    break;
                }
            }
            $cache->set($userid, $isallowed);
        } else {
            $isallowed = $cachedvalue;
        }

        return $isallowed;
    }
}
