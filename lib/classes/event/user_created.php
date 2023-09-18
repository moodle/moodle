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
 * User created event.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event when new user profile is created.
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_created extends base {

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'user';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventusercreated');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        $description = "The user with id '$this->userid' created the user with id '$this->objectid'";
        if (!empty($this->other['restoreid'])) {
            $courseid = $this->other['courseid'] ?? 0;
            return "{$description} during restore of the course with id '$courseid'.";
        }
        return "{$description}.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/user/view.php', array('id' => $this->objectid));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            debugging('The \'relateduserid\' value must be specified in the event.', DEBUG_DEVELOPER);
            $this->relateduserid = $this->objectid;
        }
    }

    /**
     * Create instance of event.
     *
     * @since Moodle 2.6.4, 2.7.1
     *
     * @param int $userid id of user
     * @return user_created
     */
    public static function create_from_userid($userid) {
        $data = array(
            'objectid' => $userid,
            'relateduserid' => $userid,
            'context' => \context_user::instance($userid)
        );

        // Create user_created event.
        $event = self::create($data);
        return $event;
    }

    /**
     * Create instance of event when user is created during the course restore process.
     *
     * @param int $userid id of user
     * @param string $restoreid
     * @param int $courseid
     * @return user_created
     */
    public static function create_from_user_id_on_restore(int $userid, string $restoreid,
            int $courseid): user_created {
        $data = [
            'objectid' => $userid,
            'relateduserid' => $userid,
            'context' => \context_user::instance($userid),
            'other' => ['restoreid' => $restoreid, 'courseid' => $courseid],
        ];

        // Create user_created event.
        return self::create($data);
    }

    public static function get_objectid_mapping() {
        return array('db' => 'user', 'restore' => 'user');
    }
}
