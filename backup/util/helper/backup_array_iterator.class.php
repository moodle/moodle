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
 * Implementation of iterator interface to work with common arrays
 *
 * This class implements the iterator interface in order to provide one
 * common API to be used in backup and restore when, within the same code,
 * both database recordsets (already iteratorors) and arrays of information
 * are used.
 *
 * TODO: Finish phpdocs
 */
class backup_array_iterator implements iterator {

    private $arr;

    public function __construct(array $arr) {
        $this->arr = $arr;
    }

    public function rewind() {
        return reset($this->arr);
    }

    public function current() {
        return current($this->arr);
    }

    public function key() {
        return key($this->arr);
    }

    public function next() {
        return next($this->arr);
    }

    public function valid() {
        return key($this->arr) !== null;
    }

    public function close() { // Added to provide compatibility with recordset iterators
        reset($this->arr); // Just reset the array
    }
}
