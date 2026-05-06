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
 * Interface for anything appearing in the admin tree
 *
 * The interface that is implemented by anything that appears in the admin tree
 * block. It forces inheriting classes to define a method for checking user permissions
 * and methods for finding something in the admin tree.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface part_of_admin_tree {

/**
 * Finds a named part_of_admin_tree.
 *
 * Used to find a part_of_admin_tree. If a class only inherits part_of_admin_tree
 * and not parentable_part_of_admin_tree, then this function should only check if
 * $this->name matches $name. If it does, it should return a reference to $this,
 * otherwise, it should return a reference to NULL.
 *
 * If a class inherits parentable_part_of_admin_tree, this method should be called
 * recursively on all child objects (assuming, of course, the parent object's name
 * doesn't match the search criterion).
 *
 * @param string $name The internal name of the part_of_admin_tree we're searching for.
 * @return mixed An object reference or a NULL reference.
 */
    public function locate($name);

    /**
     * Removes named part_of_admin_tree.
     *
     * @param string $name The internal name of the part_of_admin_tree we want to remove.
     * @return bool success.
     */
    public function prune($name);

    /**
     * Search using query
     * @param string $query
     * @return mixed array-object structure of found settings and pages
     */
    public function search($query);

    /**
     * Verifies current user's access to this part_of_admin_tree.
     *
     * Used to check if the current user has access to this part of the admin tree or
     * not. If a class only inherits part_of_admin_tree and not parentable_part_of_admin_tree,
     * then this method is usually just a call to has_capability() in the site context.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should return the
     * logical OR of the return of check_access() on all child objects.
     *
     * @return bool True if the user has access, false if she doesn't.
     */
    public function check_access();

    /**
     * Mostly useful for removing of some parts of the tree in admin tree block.
     *
     * @return bool True is hidden from normal list view
     */
    public function is_hidden();

    /**
     * Show we display Save button at the page bottom?
     * @return bool
     */
    public function show_save();
}
