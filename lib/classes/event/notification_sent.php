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
 * Notification sent event.
 *
 * @package    core
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Notification sent event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int courseid: the id of the related course.
 * }
 *
 * @package    core
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_sent extends base {

    /**
     * Create event using ids.
     *
     * @param int $userfromid
     * @param int $usertoid
     * @param int $notificationid
     * @param int $courseid course id the event is related with - SITEID if no relation exists.
     * @return notification_sent
     */
    public static function create_from_ids($userfromid, $usertoid, $notificationid, $courseid) {
        // We may be sending a notification from the 'noreply' address, which means we are not actually sending a
        // notification from a valid user. In this case, we will set the userid to 0.
        // Check if the userid is valid.
        if (!\core_user::is_real_user($userfromid)) {
            $userfromid = 0;
        }

        // If the courseid is null, then set it to the site id.
        if (is_null($courseid)) {
            $courseid = SITEID;
        }

        $event = self::create(
            [
                'objectid' => $notificationid,
                'userid' => $userfromid,
                'context' => \context_system::instance(),
                'relateduserid' => $usertoid,
                'other' => [
                    'courseid' => $courseid
                ]
            ]
        );

        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'notifications';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventnotificationsent', 'message');
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/message/output/popup/notifications.php', array('notificationid' => $this->objectid));
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        // Check if we are sending from a valid user.
        if (\core_user::is_real_user($this->userid)) {
            return "The user with id '$this->userid' sent a notification to the user with id '$this->relateduserid'.";
        }

        return "A notification was sent by the system to the user with id '$this->relateduserid'.";
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
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['courseid'])) {
            throw new \coding_exception('The \'courseid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'notifications', 'restore' => base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['courseid'] = array('db' => 'course', 'restore' => base::NOT_MAPPED);
        return $othermapped;
    }
}
