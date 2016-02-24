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
 * @package    local_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_recyclebin;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a recyclebin.
 *
 * @package    local_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class recyclebin
{
    /**
     * Is this recyclebin enabled?
     */
    public static function is_enabled() {
        return false;
    }

    /**
     * Returns an item from the recycle bin.
     *
     * @param $item int Item ID to retrieve.
     */
    public abstract function get_item($itemid);

    /**
     * Returns a list of items in the recycle bin.
     */
    public abstract function get_items();

    /**
     * Store an item in this recycle bin.
     *
     * @param $item stdClass Item to store.
     * @throws \coding_exception
     * @throws \invalid_dataroot_permissions
     * @throws \moodle_exception
     */
    public abstract function store_item($item);

    /**
     * Restore an item from the recycle bin.
     *
     * @param stdClass $item The item database record
     * @throws \Exception
     * @throws \coding_exception
     * @throws \moodle_exception
     * @throws \restore_controller_exception
     */
    public abstract function restore_item($item);

    /**
     * Delete an item from the recycle bin.
     *
     * @param stdClass $item The item database record
     * @param boolean $noevent Whether or not to fire a purged event.
     * @throws \coding_exception
     */
    public abstract function delete_item($item, $noevent = false);

    /**
     * Empty the recycle bin.
     */
    public function delete_all_items() {
        // Cleanup all items.
        $items = $this->get_items();
        foreach ($items as $item) {
            if ($this->can_delete($item)) {
                $this->delete_item($item);
            }
        }
    }

    /**
     * Can we view this?
     *
     * @param stdClass $item The item database record
     */
    public abstract function can_view($item);

    /**
     * Can we restore this?
     *
     * @param stdClass $item The item database record
     */
    public abstract function can_restore($item);

    /**
     * Can we delete this?
     *
     * @param stdClass $item The item database record
     */
    public abstract function can_delete($item);
}
