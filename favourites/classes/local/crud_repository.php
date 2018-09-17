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
 * Contains the crud_repository interface.
 *
 * @package   core_favourites
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_favourites\local;

defined('MOODLE_INTERNAL') || die();

/**
 * The crud_repository interface, defining the basic CRUD operations for any repository types within core_favourites.
 */
interface crud_repository {
    /**
     * Add one item to this repository.
     *
     * @param \stdClass $item the item to add.
     * @return \stdClass the item which was added.
     */
    public function add(\stdClass $item) : \stdClass;

    /**
     * Add all the items in the list to this repository.
     *
     * @param array $items the list of items to add.
     * @return array the list of items added to this repository.
     */
    public function add_all(array $items) : array;

    /**
     * Find an item in this repository based on its id.
     *
     * @param int $id the id of the item.
     * @return \stdClass the item.
     */
    public function find(int $id) : \stdClass;

    /**
     * Find all items in this repository.
     *
     * @return array list of all items in this repository.
     */
    public function find_all() : array;

    /**
     * Find all items with attributes matching certain values.
     *
     * @param array $criteria the array of attribute/value pairs.
     * @return array the list of items matching the criteria.
     */
    public function find_by(array $criteria) : array;

    /**
     * Check whether an item exists in this repository, based on its id.
     *
     * @param int $id the id to search for.
     * @return bool true if the item could be found, false otherwise.
     */
    public function exists(int $id) : bool;

    /**
     * Return the total number of items in this repository.
     *
     * @return int the total number of items.
     */
    public function count() : int;

    /**
     * Update an item within this repository.
     *
     * @param \stdClass $item the item to update.
     * @return \stdClass the updated item.
     */
    public function update(\stdClass $item) : \stdClass;

    /**
     * Delete an item by id.
     *
     * @param int $id the id of the item to delete.
     * @return void
     */
    public function delete(int $id);
}
