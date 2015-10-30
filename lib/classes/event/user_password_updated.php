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
 * User password updated event.
 *
 * @package    core
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event when user password is changed or reset.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - bool forgottenreset: true means reset via token.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_password_updated extends base {
    /**
     * Create event for user password changing and resetting.
     *
     * @param \stdClass $user
     * @param bool $forgottenreset true if reset via recovery link
     * @return user_password_updated
     */
    public static function create_from_user(\stdClass $user, $forgottenreset = false) {
        $data = array(
            'context' => \context_user::instance($user->id),
            'relateduserid' => $user->id,
            'other' => array('forgottenreset' => $forgottenreset),
        );
        $event = self::create($data);
        $event->add_record_snapshot('user', $user);
        return $event;
    }

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserpasswordupdated');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        if ($this->userid == $this->relateduserid) {
            if ($this->other['forgottenreset']) {
                return "The user with id '$this->userid' reset their password.";
            }
            return "The user with id '$this->userid' changed their password.";
        } else {
            return "The user with id '$this->userid' changed the password of the user with id '$this->relateduserid'.";
        }
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/user/profile.php', array('id' => $this->relateduserid));
    }

    /**
     * Returns array of parameters to be passed to legacy logging.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        if (!$this->other['forgottenreset']) {
            // We did not log password changes in earlier versions.
            return null;
        }
        return array(SITEID, 'user', 'set password', 'profile.php?id='.$this->userid, $this->relateduserid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!$this->relateduserid) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['forgottenreset'])) {
            throw new \coding_exception('The \'forgottenreset\' value must be set in other.');
        }
    }
}
