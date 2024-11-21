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
 * Course level up notification service.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\notification;

/**
 * Course level up notification service.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_level_up_notification_service {

    /** User preference prefix. */
    const USERPREF_NOTIFY = 'block_xp_notify_level_up_';

    /** @var string The key. */
    protected $key;

    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     */
    public function __construct($courseid) {
        $this->key = self::USERPREF_NOTIFY . $courseid;
    }

    /**
     * Get the levels to notify for.
     *
     * @param int $userid The user ID.
     */
    public function get_levels($userid) {
        $levels = $this->extract_from_user_prefs_of($userid);
        usort($levels, function($levela, $levelb) {
            if (!$levela) {
                return 1;
            } else if (!$levelb) {
                return -1;
            }
            return $levela - $levelb;
        });
        return $levels;
    }

    /**
     * Flag the user as having been notified.
     *
     * @param int $userid The user ID.
     * @param int $level The level.
     */
    public function mark_as_notified($userid, $level = 0) {

        // We handle level 0 as the current level, for legacy implementations.
        if (!$level) {
            unset_user_preference($this->key, $userid);
            return;
        }

        // Remove the level from the user preference.
        $levels = $this->extract_from_user_prefs_of($userid);
        $this->save_user_prefs_of($userid, array_filter($levels, function($l) use ($level) {
            return $l != $level;
        }));
    }

    /**
     * Notify a user.
     *
     * @param int $userid The user ID.
     * @param int $level The level.
     * @return void
     */
    public function notify($userid, $level = 0) {
        $levels = $this->extract_from_user_prefs_of($userid);
        $levels[] = max(0, $level);
        $this->save_user_prefs_of($userid, array_unique($levels));
    }

    /**
     * Whether the user should be notified.
     *
     * @param int $userid The user ID.
     * @return bool
     */
    public function should_be_notified($userid) {
        $levels = $this->extract_from_user_prefs_of($userid);
        return !empty($levels);
    }

    /**
     * Extract the value from the user preferences.
     *
     * @param int $userid The user ID.
     * @return array
     */
    protected function extract_from_user_prefs_of($userid) {
        $prefjson = get_user_preferences($this->key, '[]', $userid);

        // The value 1 used to be what we saved in there, we swap that to level 0 which we use
        // as the value for the current level.
        if ($prefjson == '1') {
            $levels = [0];
        } else {
            $levels = json_decode($prefjson, true) ?: [];
        }

        return $levels;
    }

    /**
     * Save the user preferences of user.
     *
     * @param int $userid The user ID.
     * @param mixed $data The data.
     */
    protected function save_user_prefs_of($userid, $data) {
        if (empty($data)) {
            unset_user_preference($this->key, $userid);
            return;
        }
        if (is_array($data)) {
            $data = array_values($data);
        }
        set_user_preference($this->key, json_encode($data), $userid);
    }

}
