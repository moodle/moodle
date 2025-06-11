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

namespace communication_matrix;

use stdClass;

/**
 * Class to manage the updates to the room information in db.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix_room {
    private const TABLE = 'matrix_room';

    /** @var \stdClass|null $record The matrix room record from db */

    /**
     * Load the matrix room record for the supplied processor.
     * @param int $processorid
     * @return null|self
     */
    public static function load_by_processor_id(
        int $processorid,
    ): ?self {
        global $DB;

        $record = $DB->get_record(self::TABLE, ['commid' => $processorid]);

        if (!$record) {
            return null;
        }
        return new self($record);
    }

    /**
     * Matrix rooms constructor to load the matrix room information from matrix_room table.
     *
     * @param stdClass $record
     */
    private function __construct(
        private stdClass $record,
    ) {
    }

    /**
     * Create matrix room data.
     *
     * @param int $processorid The id of the communication record
     * @param string|null $topic The topic of the room for matrix
     * @param string|null $roomid The id of the room from matrix
     * @return self
     */
    public static function create_room_record(
        int $processorid,
        ?string $topic,
        ?string $roomid = null,
    ): self {
        global $DB;

        $roomrecord = (object) [
            'commid' => $processorid,
            'roomid' => $roomid,
            'topic' => $topic,
        ];
        $roomrecord->id = $DB->insert_record(self::TABLE, $roomrecord);

        return self::load_by_processor_id($processorid);
    }

    /**
     * Update matrix room data.
     *
     * @param string|null $roomid The id of the room from matrix
     * @param string|null $topic The topic of the room for matrix
     */
    public function update_room_record(
        ?string $roomid = null,
        ?string $topic = null,
    ): void {
        global $DB;

        if ($roomid !== null) {
            $this->record->roomid = $roomid;
        }

        if ($topic !== null) {
            $this->record->topic = $topic;
        }

        $DB->update_record(self::TABLE, $this->record);
    }

    /**
     * Delete matrix room data.
     */
    public function delete_room_record(): void {
        global $DB;

        $DB->delete_records(self::TABLE, ['commid' => $this->record->commid]);

        unset($this->record);
    }

    /**
     * Get the processor id.
     *
     * @return int
     */
    public function get_processor_id(): int {
        return $this->record->commid;
    }

    /**
     * Get the matrix room id.
     *
     * @return string|null
     */
    public function get_room_id(): ?string {
        return $this->record->roomid;
    }

    /**
     * Get the matrix room topic.
     *
     * @return string
     */
    public function get_topic(): string {
        return $this->record->topic ?? '';
    }
}
