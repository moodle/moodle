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
 * @package    moodlecore
 * @subpackage backup-helper
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Implementation of iterator interface to work without information
 *
 * This class implementes the iterator but does nothing (as far as it
 * doesn't handle real data at all). It's here to provide one common
 * API when we want to skip some elements from structure, while also
 * working with array/db iterators at the same time.
 *
 * TODO: Finish phpdocs
 */
class backup_null_iterator implements iterator {

    public function rewind(): void {
    }

    #[\ReturnTypeWillChange]
    public function current() {
    }

    #[\ReturnTypeWillChange]
    public function key() {
    }

    public function next(): void {
    }

    public function valid(): bool {
        return false;
    }

    public function close() { // Added to provide compatibility with recordset iterators
    }
}
