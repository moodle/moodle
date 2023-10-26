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
 * Native postgresql recordset.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/moodle_recordset.php');

/**
 * pgsql specific moodle recordset class
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pgsql_native_moodle_recordset extends moodle_recordset {

    protected $result;
    /** @var current row as array.*/
    protected $current;
    protected $blobs = array();

    /** @var string Name of cursor or '' if none */
    protected $cursorname;

    /** @var pgsql_native_moodle_database Postgres database resource */
    protected $db;

    /** @var bool True if there are no more rows to fetch from the cursor */
    protected $lastbatch;

    /**
     * Build a new recordset to iterate over.
     *
     * When using cursors, $result will be null initially.
     *
     * @param resource|null $result A pg_query() result object to create a recordset from.
     * @param pgsql_native_moodle_database $db Database object (only required when using cursors)
     * @param string $cursorname Name of cursor or '' if none
     */
    public function __construct($result, pgsql_native_moodle_database $db = null, $cursorname = '') {
        if ($cursorname && !$db) {
            throw new coding_exception('When specifying a cursor, $db is required');
        }
        $this->result = $result;
        $this->db = $db;
        $this->cursorname = $cursorname;

        // When there is a cursor, do the initial fetch.
        if ($cursorname) {
            $this->fetch_cursor_block();
        }

        // Find out if there are any blobs.
        $numfields = pg_num_fields($this->result);
        for ($i = 0; $i < $numfields; $i++) {
            $type = $this->db->pg_field_type($this->result, $i);
            if ($type == 'bytea') {
                $this->blobs[] = pg_field_name($this->result, $i);
            }
        }

        $this->current = $this->fetch_next();
    }

    /**
     * Fetches the next block of data when using cursors.
     *
     * @throws coding_exception If you call this when the fetch buffer wasn't freed yet
     */
    protected function fetch_cursor_block() {
        if ($this->result) {
            throw new coding_exception('Unexpected non-empty result when fetching from cursor');
        }
        list($this->result, $this->lastbatch) = $this->db->fetch_from_cursor($this->cursorname);
        if (!$this->result) {
            throw new coding_exception('Unexpected failure when fetching from cursor');
        }
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        if (!$this->result) {
            return false;
        }
        if (!$row = pg_fetch_assoc($this->result)) {
            // There are no more rows in this result.
            pg_free_result($this->result);
            $this->result = null;

            // If using a cursor, can we fetch the next block?
            if ($this->cursorname && !$this->lastbatch) {
                $this->fetch_cursor_block();
                if (!$row = pg_fetch_assoc($this->result)) {
                    pg_free_result($this->result);
                    $this->result = null;
                    return false;
                }
            } else {
                return false;
            }
        }

        if ($this->blobs) {
            foreach ($this->blobs as $blob) {
                $row[$blob] = $row[$blob] !== null ? pg_unescape_bytea($row[$blob]) : null;
            }
        }

        return $row;
    }

    public function current(): stdClass {
        return (object)$this->current;
    }

    #[\ReturnTypeWillChange]
    public function key() {
        // return first column value as key
        if (!$this->current) {
            return false;
        }
        $key = reset($this->current);
        return $key;
    }

    public function next(): void {
        $this->current = $this->fetch_next();
    }

    public function valid(): bool {
        return !empty($this->current);
    }

    public function close() {
        if ($this->result) {
            pg_free_result($this->result);
            $this->result  = null;
        }
        $this->current = null;
        $this->blobs   = null;

        // If using cursors, close the cursor.
        if ($this->cursorname) {
            $this->db->close_cursor($this->cursorname);
            $this->cursorname = null;
        }
    }
}
