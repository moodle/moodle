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
 * Migrated mod_hvp to mod_h5pactivity event.
 *
 * @package     tool_migratehvp2h5p
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_migratehvp2h5p\event;

use core\event\base;

/**
 * Event hvp_migrated.
 *
 * @package     tool_migratehvp2h5p
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hvp_migrated extends base {

    /**
     * Initialise the event.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'hvp';
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has migrated to the mod_h5pactivity with id '{$this->other['h5pactivityid']}' ".
            " the mod_hvp activity with id '$this->objectid' and course module id '$this->contextinstanceid.";
    }

    /**
     * Returns event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_hvp_migrated', 'tool_migratehvp2h5p');
    }

    /**
     * Create event from record.
     *
     * @param stdClass $record
     * @return acceptance_created
     */
    public static function create_from_record($record) {
        $event = static::create([
            'objectid' => $record->hvpid,
            'relateduserid' => $record->userid,
            'context' => \context::instance_by_id($record->contextid),
            'other' => [
                'h5pactivityid' => $record->h5pactivityid,
                'h5pactivitycmid' => $record->h5pactivitycmid,
            ],
        ]);
        return $event;
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/h5pactivity/view.php', ['id' => $this->other['h5pactivitycmid']]);
    }

    /**
     * Get the object ID mapping.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'hvp', 'restore' => 'hvp'];
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (empty($this->other['h5pactivityid'])) {
            throw new \coding_exception('The \'h5pactivityid\' value must be set');
        }
        if (empty($this->other['h5pactivitycmid'])) {
            throw new \coding_exception('The \'h5pactivitycmid\' value must be set');
        }
    }

    /**
     * No mapping required for this event because this event is not backed up.
     *
     * @return bool
     */
    public static function get_other_mapping() {
        $othermapped = [
            'h5pactivityid' => ['db' => 'h5pactivity', 'restore' => 'h5pactivity']
        ];

        return $othermapped;
    }
}
