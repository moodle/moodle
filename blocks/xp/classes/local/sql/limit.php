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
 * SQL limit.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\sql;

/**
 * SQL limit class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class limit {

    /** @var int The count. */
    protected $count;
    /** @var int The offset. */
    protected $offset;

    /**
     * Constructor.
     *
     * @param int $count The count.
     * @param int $offset The offset.
     */
    public function __construct($count, $offset = 0) {
        $this->count = (int) $count;
        $this->offset = (int) $offset;
    }

    /**
     * Get number of records we want.
     *
     * @return int
     */
    public function get_count() {
        return $this->count;
    }

    /**
     * Get the offset before getting the records.
     *
     * @return int
     */
    public function get_offset() {
        return $this->offset;
    }

}
