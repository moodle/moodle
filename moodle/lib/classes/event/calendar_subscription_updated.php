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
 * calendar subscription updated event.
 *
 * @package    core
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered after a calendar subscription is updated.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string eventtype: the type of events (site, course, group, user).
 *      - int courseid: The ID of the course (SITEID, User(0) or actual course)
 * }
 *
 * @package    core
 * @since      Moodle 3.2
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_subscription_updated extends base
{

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'event_subscriptions';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsubscriptionupdated', 'calendar');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "User {$this->userid} has updated a calendar
        subscription with id {$this->objectid} of event type {$this->other['eventtype']}.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        if (($this->other['courseid'] == SITEID) || ($this->other['courseid'] == 0)) {
            return new \moodle_url('calendar/managesubscriptions.php');
        } else {
            return new \moodle_url('calendar/managesubscriptions.php', array('course' => $this->other['courseid']));
        }
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->context)) {
            throw new \coding_exception('The \'context\' must be set.');
        }
        if (!isset($this->objectid)) {
            throw new \coding_exception('The \'objectid\' must be set.');
        }
        if (!isset($this->other['eventtype'])) {
            throw new \coding_exception('The \'eventtype\' value must be set in other.');
        }
        if (!isset($this->other['courseid'])) {
            throw new \coding_exception('The \'courseid\' value must be set in other.');
        }
    }

    /**
     * Returns mappings for restore
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return array('db' => 'event_subscriptions', 'restore' => 'event_subscriptions');
    }
}
