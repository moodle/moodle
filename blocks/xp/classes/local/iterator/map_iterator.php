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
 * Iterator over an object applying a callback on each iterator.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\iterator;

use ArrayIterator;
use IteratorIterator;
use Traversable;

/**
 * Iterator map.
 *
 * The callback receives the value, and the key as argument.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class map_iterator extends IteratorIterator {

    /** @var callable The callable. */
    protected $callback;

    /**
     * Create a new iterator applying the callback to each record.
     *
     * @param Traversable $iterator The inner iterator.
     * @param callable $callback The callback
     */
    public function __construct($iterator, callable $callback) {
        parent::__construct(is_array($iterator) ? new ArrayIterator($iterator) : $iterator);
        $this->callback = $callback;
    }

    // @codingStandardsIgnoreLine.
    #[\ReturnTypeWillChange]
    /**
     * Returns the current element.
     *
     * @return mixed
     */
    public function current() {
        return call_user_func($this->callback, parent::current(), parent::key());
    }

}
