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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\persistents\concerns;

defined('MOODLE_INTERNAL') || die();

use coding_exception;

trait can_be_soft_deleted {
    /**
     * Permanently deletes an entry from the database.
     *
     * @return bool True on success.
     */
    public function hard_delete() {
        $result = $this->delete();

        return $result;
    }

    /**
     * Updates an entry from the database to appear as if it has been deleted
     *
     * NOTE: this relies on core moodle persistent class functionality!!!
     *
     * @return bool True on success.
     */
    public function soft_delete() {
        global $DB;

        if (empty($this->raw_get('id'))) {
            throw new coding_exception('id is required to delete');
        }

        // Hook before delete.
        $this->before_delete();

        $record = $this->to_record();
        $record = (array) $record;
        $record['timedeleted'] = time();

        // Save the record.
        $result = $DB->update_record(static::TABLE, $record);

        // Hook after delete.
        $this->after_delete($result);

        // Refresh the model to reflect changes.
        $this->read();

        return $result;
    }

}
