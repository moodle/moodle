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
 * Contains the favourites_repository interface.
 *
 * @package   core_favourites
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_favourites\local;

defined('MOODLE_INTERNAL') || die();

/**
 * The favourites_repository interface, defining additional operations useful to favourites type repositories.
 *
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface ifavourites_repository extends crud_repository {
    /**
     * Find a single favourite, based on it's unique identifiers.
     *
     * @param int $userid the id of the user to which the favourite belongs.
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param int $itemid the id of the item which was favourited (not the favourite's id).
     * @param int $contextid the contextid of the item which was favourited.
     * @return \stdClass the favourite.
     */
    public function find_favourite(int $userid, string $component, string $itemtype, int $itemid, int $contextid) : \stdClass;
}
