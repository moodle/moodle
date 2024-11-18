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
 * Provides {@link tool_iomadpolicy\event\acceptance_base} class.
 *
 * @package     tool_iomadpolicy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for acceptance_created and acceptance_updated events.
 *
 * @package     tool_iomadpolicy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class acceptance_base extends base {

    /**
     * Initialise the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'tool_iomadpolicy_acceptances';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Create event from record.
     *
     * @param stdClass $record
     * @return acceptance_created
     */
    public static function create_from_record($record) {
        $event = static::create([
            'objectid' => $record->id,
            'relateduserid' => $record->userid,
            'context' => \context_user::instance($record->userid),
            'other' => [
                'iomadpolicyversionid' => $record->iomadpolicyversionid,
                'note' => $record->note,
                'status' => $record->status,
            ],
        ]);
        $event->add_record_snapshot($event->objecttable, $record);
        return $event;
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/admin/tool/iomadpolicy/acceptance.php', array('userid' => $this->relateduserid,
            'versionid' => $this->other['iomadpolicyversionid']));
    }

    /**
     * Get the object ID mapping.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return array('db' => 'tool_iomadpolicy', 'restore' => \core\event\base::NOT_MAPPED);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (empty($this->other['iomadpolicyversionid'])) {
            throw new \coding_exception('The \'iomadpolicyversionid\' value must be set');
        }

        if (!isset($this->other['status'])) {
            throw new \coding_exception('The \'status\' value must be set');
        }

        if (empty($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
    }

    /**
     * No mapping required for this event because this event is not backed up.
     *
     * @return bool
     */
    public static function get_other_mapping() {
        return false;
    }
}