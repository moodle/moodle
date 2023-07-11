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

namespace tool_mfa\event;

use stdClass;

/**
 * Event for when user factor is deleted.
 *
 * @property-read array $other {
 *      Extra information about event.
 * }
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_deleted_factor extends \core\event\base {

    /**
     * Create instance of event.
     *
     * @param stdClass $user the User object of the User who had the factor deleted.
     * @param stdClass $deleteuser the user who performed the factor delete.
     * @param string $factorname deleted factor
     *
     * @return \core\event\base the user_factor_deleted event
     *
     * @throws \coding_exception
     */
    public static function user_deleted_factor_event(stdClass $user, $deleteuser, $factorname): \core\event\base {

        $data = [
            'relateduserid' => $user->id,
            'context' => \context_user::instance($user->id),
            'other' => [
                'userid' => $user->id,
                'factorname' => $factorname,
                'delete' => $deleteuser->id,
            ],
        ];

        return self::create($data);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        // The log message changed from logging the deleter user object to the ID. This must be kept for backwards compat
        // With old log events.
        if (is_object($this->other['delete'])) {
            return "The user with id '{$this->other['delete']->id}' successfully deleted
                {$this->other['factorname']} factor for user with id '{$this->other['userid']}'";
        } else {
            return "The user with id '{$this->other['delete']}' successfully deleted
                {$this->other['factorname']} factor for user with id '{$this->other['userid']}'";
        }
    }

    /**
     * Return localised event name.
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_name(): string {
        return get_string('event:userdeletedfactor', 'tool_mfa');
    }
}
