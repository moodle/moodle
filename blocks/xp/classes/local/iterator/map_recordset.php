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
 * Recordset map iterator.
 *
 * This is based on recordset_walk which is only available from 2.9.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\iterator;

use moodle_recordset;
use NoRewindIterator;

/**
 * Recordset map iterator.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class map_recordset extends NoRewindIterator {

    /** @var callable The callable. */
    protected $callback;

    /**
     * Create a new iterator applying the callback to each record.
     *
     * @param \moodle_recordset $recordset Recordset to iterate.
     * @param callable $callback The callback to apply.
     */
    public function __construct(moodle_recordset $recordset, callable $callback) {
        parent::__construct($recordset);
        $this->callback = $callback;
    }

    /**
     * Closes the recordset.
     *
     * @return void
     */
    public function __destruct() {
        $this->getInnerIterator()->close();
    }

    // @codingStandardsIgnoreLine.
    #[\ReturnTypeWillChange]
    /**
     * Returns the current element.
     *
     * @return mixed|bool False when the iterator has reached its end.
     */
    public function current() {
        $current = parent::current();
        if ($current === false) {
            return false;
        }
        return call_user_func($this->callback, $current);
    }

    /**
     * Checks validity.
     *
     * Automatically closes the recordset when no longer valid.
     *
     * @return bool
     */
    public function valid(): bool {
        $valid = parent::valid();
        if (!$valid) {
            $this->getInnerIterator()->close();
        }
        return $valid;
    }

}
