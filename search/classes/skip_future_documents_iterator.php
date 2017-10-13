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
 * Iterator for skipping search recordset documents that are in the future.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Iterator for skipping search recordset documents that are in the future.
 *
 * This iterator stops iterating if it receives a document that was modified later than the
 * specified cut-off time (usually current time).
 *
 * This iterator assumes that its parent iterator returns documents in modified order (which is
 * required to be the case for search indexing). This means we will stop retrieving data from the
 * recordset
 *
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class skip_future_documents_iterator implements \Iterator {
    /** @var \Iterator Parent iterator */
    protected $parent;

    /** @var int Cutoff time; anything later than this will cause the iterator to stop */
    protected $cutoff;

    /**
     * Constructor.
     *
     * @param \Iterator $parent Parent iterator, must return search documents in modified order
     * @param int $cutoff Cut-off time, default is current time
     */
    public function __construct(\Iterator $parent, $cutoff = 0) {
        $this->parent = $parent;
        if ($cutoff) {
            $this->cutoff = $cutoff;
        } else {
            $this->cutoff = time();
        }
    }

    public function current() {
        return $this->parent->current();
    }

    public function next() {
        $this->parent->next();
    }

    public function key() {
        return $this->parent->key();
    }

    public function valid() {
        // Check that the parent is valid.
        if (!$this->parent->valid()) {
            return false;
        }

        if ($doc = $this->parent->current()) {
            // This document is valid if the modification date is before the cutoff.
            return $doc->get('modified') <= $this->cutoff;
        }

        return false;

    }

    public function rewind() {
        $this->parent->rewind();
    }
}
