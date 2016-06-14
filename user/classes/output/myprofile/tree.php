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
 * Defines profile page navigation tree.
 *
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\output\myprofile;
defined('MOODLE_INTERNAL') || die();

/**
 * Defines my profile page navigation tree.
 *
 * @since     Moodle 2.9
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tree implements \renderable {
    /**
     * @var category[] Array of categories in the tree.
     */
    private $categories = array();

    /**
     * @var node[] Array of nodes in the tree that were directly added to the tree.
     */
    private $nodes = array();

    /**
     * @var array List of properties accessible via __get.
     */
    private $properties = array('categories', 'nodes');

    /**
     * Add a node to the tree.
     *
     * @param node $node node object.
     *
     * @throws \coding_exception
     */
    public function add_node(node $node) {
        $name = $node->name;
        if (isset($this->nodes[$name])) {
            throw new \coding_exception("Node name $name already used");
        }
        $this->nodes[$node->name] = $node;
    }

    /**
     * Add a category to the tree.
     *
     * @param category $cat category object.
     *
     * @throws \coding_exception
     */
    public function add_category(category $cat) {
        $name = $cat->name;
        if (isset($this->categories[$name])) {
            throw new \coding_exception("Category name $name already used");
        }
        $this->categories[$cat->name] = $cat;
    }

    /**
     * Sort categories and nodes. Builds the tree structure that would be displayed to the user.
     *
     * @throws \coding_exception
     */
    public function sort_categories() {
        $this->attach_nodes_to_categories();
        $tempcategories = array();
        foreach ($this->categories as $category) {
            $after = $category->after;
            if ($after == null) {
                // Can go anywhere in the tree.
                $category->sort_nodes();
                $tempcategories = array_merge($tempcategories, array($category->name => $category),
                        $this->find_categories_after($category));
            }
        }
        if (count($tempcategories) !== count($this->categories)) {
            // Orphan categories found.
            throw new \coding_exception('Some of the categories specified contains invalid \'after\' property');
        }
        $this->categories = $tempcategories;
    }

    /**
     * Attach various nodes to their respective categories.
     *
     * @throws \coding_exception
     */
    protected function attach_nodes_to_categories() {
        foreach ($this->nodes as $node) {
            $parentcat = $node->parentcat;
            if (!isset($this->categories[$parentcat])) {
                throw new \coding_exception("Category $parentcat doesn't exist");
            } else {
                $this->categories[$parentcat]->add_node($node);
            }
        }
    }

    /**
     * Find all category nodes that should be displayed after a given a category node.
     *
     * @param category $category category object
     *
     * @return category[] array of category objects
     * @throws \coding_exception
     */
    protected function find_categories_after($category) {
        $return = array();
        $categoryarray = $this->categories;
        foreach ($categoryarray as $categoryelement) {
            if ($categoryelement->after == $category->name) {
                // Find all categories that comes after this category as well.
                $categoryelement->sort_nodes();
                $return = array_merge($return, array($categoryelement->name => $categoryelement),
                        $this->find_categories_after($categoryelement));
            }
        }
        return $return;
    }

    /**
     * Magic get method.
     *
     * @param string $prop property to get.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($prop) {
        if (in_array($prop, $this->properties)) {
            return $this->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }
}
