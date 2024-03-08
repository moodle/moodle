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
 * The main interface for recycle bin methods.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_recyclebin;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a recyclebin.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_bin {

    /**
     * Is this recyclebin enabled?
     */
    public static function is_enabled() {
        return false;
    }

    /**
     * Returns an item from the recycle bin.
     *
     * @param int $itemid Item ID to retrieve.
     */
    abstract public function get_item($itemid);

    /**
     * Returns a list of items in the recycle bin.
     */
    abstract public function get_items();

    /**
     * Store an item in this recycle bin.
     *
     * @param \stdClass $item Item to store.
     */
    abstract public function store_item($item);

    /**
     * Restore an item from the recycle bin.
     *
     * @param \stdClass $item The item database record
     */
    abstract public function restore_item($item);

    /**
     * Delete an item from the recycle bin.
     *
     * @param \stdClass $item The item database record
     */
    abstract public function delete_item($item);

    /**
     * Empty the recycle bin.
     */
    public function delete_all_items() {
        // Cleanup all items.
        $items = $this->get_items();
        foreach ($items as $item) {
            if ($this->can_delete()) {
                $this->delete_item($item);
            }
        }
    }

    /**
     * Can we view items in this recycle bin?
     */
    abstract public function can_view();

    /**
     * Can we restore items in this recycle bin?
     */
    abstract public function can_restore();

    /**
     * Can we delete this?
     */
    abstract public function can_delete();
}
