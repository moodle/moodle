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

/**
 * Class matrix_rooms to manage the updates to the room information in db.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix_rooms {

    /**
     * @var \stdClass|null $matrixroomrecord The matrix room record from db
     */
    private ?\stdClass $matrixroomrecord = null;


    /**
     * Matrix rooms constructor to load the matrix room information from matrix_rooms table.
     *
     * @param int $commid The id of the communication record
     */
    public function __construct(int $commid) {
        $this->load_matrix_room_data($commid);
    }

    /**
     * Get the matrix room data from database. Either get the data object or return false if no data found.
     *
     * @param int $commid The id of the communication record
     */
    public function load_matrix_room_data(int $commid): void {
        global $DB;
        if ($record = $DB->get_record('matrix_rooms', ['commid' => $commid])) {
            $this->matrixroomrecord = $record;
        }
    }

    /**
     * Create matrix room data.
     *
     * @param int $commid The id of the communication record
     * @param string|null $roomid The id of the room from matrix
     * @param string|null $roomtopic The topic of the room for matrix
     */
    public function create_matrix_room_record(
        int $commid,
        ?string $roomid,
        ?string $roomtopic
    ): void {
        global $DB;
        $roomrecord = new \stdClass();
        $roomrecord->commid = $commid;
        $roomrecord->roomid = $roomid;
        $roomrecord->topic = $roomtopic;
        $roomrecord->id = $DB->insert_record('matrix_rooms', $roomrecord);
        $this->matrixroomrecord = $roomrecord;
    }

    /**
     * Update matrix room data.
     *
     * @param string|null $roomid The id of the room from matrix
     * @param string|null $roomtopic The topic of the room for matrix
     */
    public function update_matrix_room_record(?string $roomid, ?string $roomtopic): void {
        global $DB;
        if ($this->room_record_exists()) {
            $this->matrixroomrecord->roomid = $roomid;
            $this->matrixroomrecord->topic = $roomtopic;
            $DB->update_record('matrix_rooms', $this->matrixroomrecord);
        }
    }

    /**
     * Delete matrix room data.
     *
     * @return bool
     */
    public function delete_matrix_room_record(): bool {
        global $DB;
        if ($this->room_record_exists()) {
            return $DB->delete_records('matrix_rooms', ['commid' => $this->matrixroomrecord->commid]);
        }
        return false;
    }

    /**
     * Get the matrix room id.
     *
     * @return string|null
     */
    public function get_matrix_room_id(): ?string {
        if ($this->room_record_exists()) {
            return $this->matrixroomrecord->roomid;
        }
        return null;
    }

    /**
     * Get the matrix room topic.
     *
     * @return string|null
     */
    public function get_matrix_room_topic(): ?string {
        if ($this->room_record_exists()) {
            return $this->matrixroomrecord->topic;
        }
        return null;
    }

    /**
     * Check if room record exist for matrix.
     *
     * @return bool
     */
    public function room_record_exists(): bool {
        return (bool) $this->matrixroomrecord;
    }
}
