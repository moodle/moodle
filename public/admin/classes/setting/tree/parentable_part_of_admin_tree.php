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
 * Interface implemented by any part_of_admin_tree that has children.
 *
 * The interface implemented by any part_of_admin_tree that can be a parent
 * to other part_of_admin_tree's. (For now, this only includes admin_category.) Apart
 * from ensuring part_of_admin_tree compliancy, it also ensures inheriting methods
 * include an add method for adding other part_of_admin_tree objects as children.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface parentable_part_of_admin_tree extends part_of_admin_tree {

/**
 * Adds a part_of_admin_tree object to the admin tree.
 *
 * Used to add a part_of_admin_tree object to this object or a child of this
 * object. $something should only be added if $destinationname matches
 * $this->name. If it doesn't, add should be called on child objects that are
 * also parentable_part_of_admin_tree's.
 *
 * $something should be appended as the last child in the $destinationname. If the
 * $beforesibling is specified, $something should be prepended to it. If the given
 * sibling is not found, $something should be appended to the end of $destinationname
 * and a developer debugging message should be displayed.
 *
 * @param string $destinationname The internal name of the new parent for $something.
 * @param part_of_admin_tree $something The object to be added.
 * @return bool True on success, false on failure.
 */
    public function add($destinationname, $something, $beforesibling = null);

}
