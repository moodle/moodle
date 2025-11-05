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

namespace mod_board\event;

use stdClass;

/**
 * Update column event.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_column extends \core\event\base {
    /**
     * Create new event.
     *
     * @param stdClass $column
     * @param stdClass $board
     * @param \context_module $context
     * @return self
     */
    public static function create_from_column(stdClass $column, stdClass $board, \context_module $context): self {
        /** @var self $event */
        $event = self::create([
            'objectid' => $column->id,
            'context' => $context,
            'other' => [
                'name' => get_config('mod_board', 'addcolumnnametolog') ? $column->name : null,
            ],
        ]);

        $event->add_record_snapshot('board', $board);
        $event->add_record_snapshot('board_columns', $column);

        return $event;
    }

    /**
     * Init function.
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'board_columns';
    }

    /**
     * Get name.
     * @return \lang_string|string
     */
    public static function get_name() {
        return get_string('event_update_column', 'mod_board');
    }

    /**
     * Get description.
     * @return \lang_string|string|null
     */
    public function get_description() {
        $obj = new stdClass();
        $obj->userid = $this->userid;
        $obj->objectid = $this->objectid;
        $obj->name = $this->other['name'];
        return get_string('event_update_column_desc', 'mod_board', $obj);
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the objectid to it's new value in the new course.
     *
     * Does nothing in the base class except display a debugging message warning
     * the user that the event does not contain the required functionality to
     * map this information. For events that do not store an objectid this won't
     * be called, so no debugging message will be displayed.
     *
     * @return array the name of the restore mapping the objectid links to
     */
    public static function get_objectid_mapping() {
        return [
            'db'        => 'board_columns',
            'restore'   => 'board_column',
        ];
    }

    /**
     * The 'other' fields for this event do not need to mapped during backup and restore as they
     * only contain test values, not IDs for anything on the course.
     *
     * @return array Empty array
     */
    public static function get_other_mapping(): array {
        return [];
    }

    #[\Override]
    public function get_url() {
        $context = $this->get_context();
        if (!$context) {
            return null;
        }
        return new \moodle_url('/mod/board/view.php', ['id' => $context->instanceid]);
    }
}
