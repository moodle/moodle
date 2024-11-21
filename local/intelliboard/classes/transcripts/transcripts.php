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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\transcripts;

class transcripts {

    public $table   = null;
    public $record  = null;
    protected $db   = null;

    public function __construct($id = 0) {
        global $DB;

        $this->db = $DB;
        $this->record = ($id) ? $this->db->get_record($this->table, array('id' => $id)) : new \stdClass();
    }

    public function set_record($record) {
        $this->record = $record;
    }

    public function get_record($params = []) {
        return $this->db->get_record($this->table, $params);
    }

    public function get_records($params = []) {
        return $this->db->get_records($this->table, $params);
    }

    public function insert() {
        return $this->db->insert_record($this->table, $this->record);
    }

    public function update() {
        return $this->db->update_record($this->table, $this->record);
    }

    public function delete() {
        return $this->db->delete_records($this->table, ['id' => $this->record->id]);
    }
}
