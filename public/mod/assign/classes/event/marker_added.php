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

namespace mod_assign\event;

use assign;
use coding_exception;
use stdClass;

/**
 * The mod_assign marker added event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int markerid: user id of marker.
 * }
 *
 * @package    mod_assign
 * @since      Moodle 5.3
 * @copyright  2026 Catalyst IT Australia Pty Ltd
 * @author     Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marker_added extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @param assign $assign
     * @param stdClass $user
     * @param stdClass $marker
     * @param stdClass $allocatedmarker
     * @return marker_added
     */
    public static function create_from_marker(assign $assign, stdClass $user, stdClass $marker, stdClass $allocatedmarker) {
        $data = [
            'context' => $assign->get_context(),
            'objectid' => $allocatedmarker->id,
            'relateduserid' => $user->id,
            'other' => [
                'markerid' => $marker->id,
            ],
        ];
        self::$preventcreatecall = false;
        /** @var marker_added $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_assign($assign);
        $event->add_record_snapshot('user', $user);
        $event->add_record_snapshot('user', $marker);
        $event->add_record_snapshot('assign_allocated_marker', $allocatedmarker);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has allocated the user with id '{$this->other['markerid']}' as a marker " .
            "for the user with id '$this->relateduserid' for the assignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventmarkeradded', 'mod_assign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'assign_allocated_marker';
    }

    /**
     * Custom validation.
     *
     * @throws coding_exception
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new coding_exception(
                'cannot call marker_added::create() directly, use marker_added::create_from_marker() instead.',
            );
        }

        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['markerid'])) {
            throw new coding_exception('The \'markerid\' value must be set in other.');
        }
    }

    /**
     * Get objectid mapping.
     *
     * @return array
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'assign_allocated_marker', 'restore' => 'allocatedmarker'];
    }

    /**
     * Get other mapping.
     *
     * @return array
     */
    public static function get_other_mapping(): array {
        $othermapped = [];
        $othermapped['markerid'] = ['db' => 'user', 'restore' => 'user'];

        return $othermapped;
    }
}
