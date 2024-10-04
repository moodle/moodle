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
 * Database driver test class for testing moodle_read_replica_trait
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Database recordset mock test class
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_replica_moodle_recordset_special extends moodle_recordset {
    /**
     * Iterator interface
     * @return void
     */
    public function close() {
    }
    /**
     * Iterator interface
     * @return stdClass
     */
    public function current(): stdClass {
        return new stdClass();
    }
    /**
     * Iterator interface
     * @return void
     */
    public function next(): void {
    }
    /**
     * Iterator interface
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key() {
    }
    /**
     * Iterator interface
     * @return bool
     */
    public function valid(): bool {
        return false;
    }
}
