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
 * Defines a category in my profile page navigation.
 *
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\output\myprofile;
defined('MOODLE_INTERNAL') || die();

/**
 * Defines a category in my profile page navigation.
 *
 * @since     Moodle 2.9
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category implements \renderable {

    /**
     * @var string Name of the category after which this category should appear.
     */
    private $after;

    /**
     * @var string Name of the category.
     */
    private $name;

    /**
     * @var string Title of the category.
     */
    private $title;

    /**
     * @var node[] Array of nodes associated with this category.
     */
    private $nodes = array();

    /**
     * @var array list of properties publicly accessible via __get.
     */
    private $properties = array('after', 'name', 'title', 'nodes');

    /**
     * Constructor for category class.
     *
     * @param string $name Category name.
     * @param string $title category title.
     * @param null|string $after Name of category after which this category should appear.
     */
    public function __construct($name, $title, $after = null) {
        $this->after = $after;
        $this->name = $name;
        $this->title = $title;
    }

    /**
     * Add a node to this category.
     *
     * @param node $node node object.
     * @see \core_user\output\myprofile\tree::add_node()
     *
     * @throws \coding_exception
     */
    public function add_node(\core_user\output\myprofile\node $node) {
        $name = $node->name;
        if (isset($this->nodes[$name])) {
            throw new \coding_exception("Node with name $name already exists");
        }
        if ($node->parentcat !== $this->name) {
            throw new \coding_exception("Node parent must match with the category it is added to");
        }
        $this->nodes[$node->name] = $node;
    }

    /**
     * Sort nodes of the category in the order in which they should be displayed.
     *
     * @see \core_user\output\myprofile\tree::sort_categories()
     * @throws \coding_exception
     */
    public function sort_nodes() {
        $tempnodes = array();
        foreach ($this->nodes as $node) {
            $after = $node->after;
            if ($after == null) {
                // Can go anywhere in the cat.
                $tempnodes = array_merge($tempnodes, array($node->name => $node), $this->find_nodes_after($node));
            }
        }
        if (count($tempnodes) !== count($this->nodes)) {
            // Orphan nodes found.
            throw new \coding_exception('Some of the nodes specified contains invalid \'after\' property');
        }
        $this->nodes = $tempnodes;
    }

    /**
     * Given a node object find all node objects that should appear after it.
     *
     * @param node $node node object
     *
     * @return array
     */
    protected function find_nodes_after($node) {
        $return = array();
        $nodearray = $this->nodes;
        foreach ($nodearray as $nodeelement) {
            if ($nodeelement->after === $node->name) {
                // Find all nodes that comes after this node as well.
                $return = array_merge($return, array($nodeelement), $this->find_nodes_after($nodeelement));
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
